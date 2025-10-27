<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Support\Nric;
use App\Models\Person;
use Maatwebsite\Excel\Facades\Excel;

class ImportPeople extends Component
{
    use WithFileUploads;

    /** @var \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null */
    public $file;

    public array $report = [
        'processed'     => 0,
        'created'       => 0,
        'updated'       => 0,
        'skipped'       => 0,
        'errors'        => [], // [row => message]
        'header_map'    => [], // raw header -> db column/null
        'created_rows'  => [], // ['row'=>N,'id'=>?,'name'=>'...','keys'=>[...]]
        'updated_rows'  => [], // ['row'=>N,'id'=>?,'name'=>'...','dirty'=>['field'=>['old'=>..,'new'=>..]]]
        'skipped_rows'  => [], // ['row'=>N,'reason'=>'...']
    ];

    /**
     * Canonical DB columns we allow to be filled from Excel.
     */
    protected array $dbColumns = [
        'name',
        'nric',
        'date_of_birth',
        'address',
        'phone',
        'email',
        'gender',
        'rank',
        'pk_number',
        'department',
        'union_number',
        'car', // expects JSON string or will be converted to JSON
    ];

    /**
     * Header alias map to improve matching odds.
     * key = normalized excel header, value = canonical DB column.
     */
    protected array $headerAliases = [
        'ic' => 'nric',
        'no_ic' => 'nric',
        'mykad' => 'nric',
        'dob' => 'date_of_birth',
        'birthdate' => 'date_of_birth',
        'phone_no' => 'phone',
        'phone_number' => 'phone',
        'mobile' => 'phone',
        'e-mail' => 'email',
        'mail' => 'email',
        'sex' => 'gender',
        'dept' => 'department',
        'union' => 'union_number',
        'vehicle' => 'car',
    ];

    protected function rules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB
        ];
    }

    public function render()
    {
        return view('livewire.import-people');
    }

    public function import()
    {
        $this->validate();

        $path = $this->file->store('imports');

        try {
            $sheets = Excel::toCollection(null, Storage::path($path));
            $sheet  = $sheets->first();
            if (!$sheet || $sheet->isEmpty()) {
                $this->report['errors'][] = ['row' => 0, 'message' => 'No rows found in the spreadsheet.'];
                return;
            }

            // Determine headers
            $first = $sheet->first();
            $firstRow = $first instanceof \Illuminate\Support\Collection ? $first->toArray() : (array) $first;

            // Determine if first row is positional (0..n) or associative (already has headings)
            $numericKeys = array_keys($firstRow) === range(0, count($firstRow) - 1);

            if ($numericKeys) {
                // Row 0 is header; data starts at row 1
                $rawHeaders = array_map(fn($v) => (string) $v, array_values($firstRow));
                $rows = $sheet->slice(1)->values();
            } else {
                // Already associative: keys are headers, and the first row is data
                $rawHeaders = array_keys($firstRow);
                $rows = $sheet; // includes the first row
            }

            // Normalize headers and map to canonical db columns
            $normalizedHeaders = array_map([$this, 'normalizeHeader'], $rawHeaders);

            $headerToDb = [];
            foreach ($normalizedHeaders as $i => $h) {
                $canonical = $this->headerAliases[$h] ?? $h; // map alias -> canonical
                if (in_array($canonical, $this->dbColumns, true)) {
                    $headerToDb[$i] = $canonical;
                } else {
                    // Unrecognized header; leave unmapped
                    $headerToDb[$i] = null;
                }
            }

            // Store header map for debugging (raw header -> db column or null)
            $this->report['header_map'] = [];
            foreach ($rawHeaders as $i => $label) {
                $this->report['header_map'][(string)$label] = $headerToDb[$i] ?? null;
            }

            // Process rows
            foreach ($rows as $rowIndex => $row) {
                $this->report['processed']++;

                // Normalize row into a flat positional array for header index mapping
                if ($row instanceof \Illuminate\Support\Collection) {
                    $rowArr = $row->toArray();
                } else {
                    $rowArr = (array) $row;
                }

                // If the row is associative (keys are header names), convert to positional by value order
                if (!array_key_exists(0, $rowArr)) {
                    $rowArr = array_values($rowArr);
                }

                // Trim all strings
                $rowArr = array_map(function ($v) {
                    return is_string($v) ? trim($v) : $v;
                }, $rowArr);

                // Build data using intersecting headers
                $data = [];
                foreach ($rowArr as $i => $value) {
                    $col = $headerToDb[$i] ?? null;
                    if (!$col) continue;

                    $data[$col] = $this->transformValue($col, $value);
                }

                // Skip if no name and no identifier fields
                if (empty($data['name']) && empty($data['nric']) && empty($data['email']) && empty($data['phone'])) {
                    $this->report['skipped']++;
                    $this->report['errors'][] = ['row' => $rowIndex + 2, 'message' => 'Missing name/NRIC/email/phone'];
                    $this->report['skipped_rows'][] = ['row' => $rowIndex + 2, 'reason' => 'Missing name/NRIC/email/phone'];
                    continue;
                }

                // Matching priority: NRIC → email → phone → name (exact)
                $person = $this->findExisting($data);

                // Only persist allowed keys
                $payload = Arr::only($data, $this->dbColumns);

                // Build a "dirty" payload: only non-empty values coming from Excel
                $dirty = [];
                foreach ($payload as $k => $v) {
                    if (is_string($v)) {
                        $v = trim($v);
                    }
                    // Skip null/empty so we NEVER overwrite existing DB values with blanks
                    if ($v === null || $v === '') {
                        continue;
                    }
                    $dirty[$k] = $v;
                }

                // --- Auto-fill DOB & gender from NRIC (non-destructive) ---
                // Always parse fresh for THIS row to avoid leaking a previous row's value.
                $sourceNric = $dirty['nric'] ?? ($person->nric ?? null);

                if ($sourceNric) {
                    $parsedNric = \App\Support\Nric::parse($sourceNric); // fresh parse per iteration

                    if ($parsedNric) {
                        // Only set if Excel didn't provide a value AND DB is currently empty
                        $dbGenderEmpty = !$person || empty($person->gender);
                        if ((!isset($dirty['gender']) || $dirty['gender'] === '') && $dbGenderEmpty) {
                            if (!empty($parsedNric['gender'])) {
                                $dirty['gender'] = $parsedNric['gender'];
                            }
                        }

                        $dbDobEmpty = !$person || empty($person->date_of_birth);
                        if ((!isset($dirty['date_of_birth']) || $dirty['date_of_birth'] === '') && $dbDobEmpty) {
                            if (!empty($parsedNric['date_of_birth'])) {
                                $dirty['date_of_birth'] = $parsedNric['date_of_birth'];
                            }
                        }
                    }
                }
                // --- end NRIC auto-fill ---



                try {
                    if ($person) {
                        if (!empty($dirty)) {
                            // Snapshot original values for diff (Model has no ->only(); use attributes)
                            $before = Arr::only($person->getAttributes(), array_keys($dirty));

                            $person->fill($dirty);

                            if ($person->isDirty()) {
                                $changed = $person->getDirty(); // keys that actually changed
                                $person->save();
                                $this->report['updated']++;

                                // Build a per-field old/new diff
                                $diff = [];
                                foreach ($changed as $field => $newVal) {
                                    $diff[$field] = [
                                        'old' => $before[$field] ?? null,
                                        'new' => $person->{$field},
                                    ];
                                }

                                // Track updated entry
                                $this->report['updated_rows'][] = [
                                    'row'  => $rowIndex + 2,
                                    'id'   => $person->id,
                                    'name' => $person->name,
                                    'dirty'=> $diff,
                                ];

                                Log::info('ImportPeople: updated row', [
                                    'row'   => $rowIndex + 2,
                                    'id'    => $person->id,
                                    'name'  => $person->name,
                                    'dirty' => $diff,
                                ]);
                            } else {
                                // No actual change
                                $this->report['skipped']++;
                                $this->report['skipped_rows'][] = [
                                    'row'    => $rowIndex + 2,
                                    'reason' => 'No actual change',
                                ];
                            }
                        } else {
                            // Nothing non-empty to write
                            $this->report['skipped']++;
                            $this->report['skipped_rows'][] = [
                                'row'    => $rowIndex + 2,
                                'reason' => 'All provided cells empty',
                            ];
                        }
                    } else {
                        // Creating: require at least a name; if missing, synthesize
                        if (empty($dirty['name'])) {
                            $dirty['name'] = $this->fallbackName($dirty);
                        }
                        $created = Person::create($dirty);
                        $this->report['created']++;

                        // Track created entry
                        $this->report['created_rows'][] = [
                            'row'  => $rowIndex + 2,
                            'id'   => $created->id,
                            'name' => $created->name,
                            'keys' => array_keys($dirty),
                        ];

                        Log::info('ImportPeople: created row', [
                            'row'  => $rowIndex + 2,
                            'id'   => $created->id,
                            'name' => $created->name,
                            'keys' => array_keys($dirty),
                        ]);
                    }
                } catch (\Throwable $e) {
                    $this->report['errors'][] = [
                        'row' => $rowIndex + 2,
                        'message' => $e->getMessage(),
                    ];
                    Log::error('ImportPeople: error', [
                        'row' => $rowIndex + 2,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

        } finally {
            Storage::delete($path);
        }

        session()->flash('success', 'Import finished.');
    }

    private function normalizeHeader(string $h): string
    {
        $h = trim(mb_strtolower($h));
        $h = str_replace(['-', ' ', '.'], '_', $h);
        $h = preg_replace('/[^a-z0-9_]/', '', $h);
        $h = preg_replace('/_+/', '_', $h);
        return $h;
    }

    private function transformValue(string $col, $value)
    {
        if (is_string($value)) {
            $value = trim($value);
        }

        return match ($col) {
            'gender' => $this->normalizeGender($value),
            'date_of_birth' => $this->normalizeDate($value),
            'phone' => $this->normalizePhone($value),
            'car' => $this->normalizeCar($value),
            default => $value,
        };
    }

    private function normalizeGender($v): ?string
    {
        if ($v === null || $v === '') return null;
        $v = Str::lower((string)$v);
        return match (true) {
            str_starts_with($v, 'm') => 'male',
            str_starts_with($v, 'f') => 'female',
            default => null,
        };
    }

    private function normalizeDate($v): ?string
    {
        if (!$v) return null;

        try {
            if (is_numeric($v)) {
                // Excel date serial; 25569 is 1970-01-01
                $ts = ((int)$v - 25569) * 86400;
                return Carbon::createFromTimestamp($ts)->toDateString();
            }
            return Carbon::parse((string)$v)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizePhone($v): ?string
    {
        if ($v === null || $v === '') return null;
        // Remove spaces, dashes, brackets
        $digits = preg_replace('/[^0-9+]/', '', (string)$v);
        return $digits;
    }

    private function normalizeCar($v): ?string
    {
        if ($v === null || $v === '') return null;
        $s = (string)$v;
        if ($this->looksLikeJson($s)) {
            return $s;
        }
        // Try to parse "Model=Axia; Plate=ABC1234" → {"Model":"Axia","Plate":"ABC1234"}
        $pairs = array_filter(array_map('trim', preg_split('/[;,]/', $s)));
        $arr = [];
        foreach ($pairs as $pair) {
            if (str_contains($pair, '=')) {
                [$k, $val] = array_map('trim', explode('=', $pair, 2));
                if ($k !== '' && $val !== '') $arr[$k] = $val;
            } else {
                $arr[] = $pair;
            }
        }
        return json_encode($arr, JSON_UNESCAPED_UNICODE);
    }

    private function looksLikeJson(string $s): bool
    {
        if (!str_starts_with($s, '{') && !str_starts_with($s, '[')) return false;
        json_decode($s);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Find an existing person including soft-deleted rows.
     * If a soft-deleted match is found, restore it before returning.
     */
    private function findExisting(array $data): ?Person
    {
        // Priority: NRIC → email → phone → name (exact), include soft-deleted rows
        if (!empty($data['nric'])) {
            $p = Person::withTrashed()->where('nric', $data['nric'])->first();
            if ($p) { if ($p->trashed()) { $p->restore(); } return $p; }
        }

        if (!empty($data['email'])) {
            $p = Person::withTrashed()->where('email', $data['email'])->first();
            if ($p) { if ($p->trashed()) { $p->restore(); } return $p; }
        }

        if (!empty($data['phone'])) {
            $p = Person::withTrashed()->where('phone', $data['phone'])->first();
            if ($p) { if ($p->trashed()) { $p->restore(); } return $p; }
        }

        if (!empty($data['name'])) {
            $p = Person::withTrashed()->where('name', $data['name'])->first();
            if ($p) { if ($p->trashed()) { $p->restore(); } return $p; }
        }

        return null;
    }

    private function fallbackName(array $payload): string
    {
        return $payload['email'] ?? $payload['phone'] ?? $payload['nric'] ?? 'Unknown';
    }
}
