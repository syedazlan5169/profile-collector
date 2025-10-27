<div class="max-w-6xl mx-auto py-10 space-y-8">
    {{-- Flash --}}
    @if (session()->has('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Card: Uploader --}}
    <div class="rounded-lg border bg-white dark:bg-slate-900 dark:border-slate-800 p-6 space-y-4">
        <h2 class="text-xl font-semibold tracking-tight">Import People</h2>

        <div class="grid gap-3 sm:grid-cols-[1fr_auto] items-end">
            <div>
                <input type="file" wire:model="file" accept=".xlsx,.xls,.csv" class="block w-full text-sm file:mr-4 file:rounded-md file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-white hover:file:bg-indigo-700">
                @error('file')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
                @if ($file)
                    <p class="text-xs text-slate-500 mt-1">Selected: {{ $file->getClientOriginalName() }} ({{ number_format($file->getSize()/1024, 1) }} KB)</p>
                @endif
            </div>
            <button wire:click="import"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 disabled:opacity-50">
                <span wire:loading.remove>Import</span>
                <span wire:loading>Importing…</span>
            </button>
        </div>
    </div>

    {{-- Card: Summary --}}
    <div class="rounded-lg border bg-white dark:bg-slate-900 dark:border-slate-800 p-6">
        <h3 class="font-semibold mb-3">Summary</h3>
        <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
            <div class="rounded-md border p-3">
                <dt class="text-slate-500">Processed</dt>
                <dd class="text-lg font-semibold">{{ $report['processed'] ?? 0 }}</dd>
            </div>
            <div class="rounded-md border p-3">
                <dt class="text-slate-500">Created</dt>
                <dd class="text-lg font-semibold text-emerald-600">{{ $report['created'] ?? 0 }}</dd>
            </div>
            <div class="rounded-md border p-3">
                <dt class="text-slate-500">Updated</dt>
                <dd class="text-lg font-semibold text-indigo-600">{{ $report['updated'] ?? 0 }}</dd>
            </div>
            <div class="rounded-md border p-3">
                <dt class="text-slate-500">Skipped</dt>
                <dd class="text-lg font-semibold text-amber-600">{{ $report['skipped'] ?? 0 }}</dd>
            </div>
        </dl>
    </div>

    {{-- Card: Header map --}}
    @if (!empty($report['header_map']))
        <div class="rounded-lg border bg-white dark:bg-slate-900 dark:border-slate-800 p-6">
            <details class="group">
                <summary class="cursor-pointer font-semibold flex items-center justify-between">
                    Header mapping <span class="text-xs text-slate-500">raw header → DB column</span>
                    <span class="text-slate-400 group-open:rotate-90 transition-transform">›</span>
                </summary>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="text-left border-b">
                            <th class="py-2 pr-4">Raw header</th>
                            <th class="py-2">DB column</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($report['header_map'] as $raw => $db)
                            <tr class="border-b last:border-none">
                                <td class="py-2 pr-4 font-medium">{{ $raw }}</td>
                                <td class="py-2">
                                    @if ($db)
                                        <code class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800">{{ $db }}</code>
                                    @else
                                        <span class="text-slate-400 italic">unmapped</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        </div>
    @endif

    {{-- Card: Updated rows --}}
    @if (!empty($report['updated_rows']))
        <div class="rounded-lg border bg-white dark:bg-slate-900 dark:border-slate-800 p-6">
            <details open class="group">
                <summary class="cursor-pointer font-semibold flex items-center justify-between">
                    Updated rows ({{ count($report['updated_rows']) }})
                    <span class="text-slate-400 group-open:rotate-90 transition-transform">›</span>
                </summary>

                <div class="mt-4 space-y-4">
                    @foreach ($report['updated_rows'] as $u)
                        <div class="rounded-md border p-4">
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm mb-3">
                                <span class="font-medium">Row {{ $u['row'] }}</span>
                                <span class="text-slate-500">ID: {{ $u['id'] }}</span>
                                <span class="text-slate-700 font-semibold">{{ $u['name'] }}</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                    <tr class="text-left border-b">
                                        <th class="py-2 pr-4">Field</th>
                                        <th class="py-2 pr-4">Old</th>
                                        <th class="py-2">New</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($u['dirty'] as $field => $pair)
                                        <tr class="border-b last:border-none">
                                            <td class="py-2 pr-4 font-medium">
                                                <code class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800">{{ $field }}</code>
                                            </td>
                                            <td class="py-2 pr-4 text-slate-500">
                                                {{ is_array($pair['old'] ?? null) ? json_encode($pair['old']) : ($pair['old'] ?? '—') }}
                                            </td>
                                            <td class="py-2">
                                                {{ is_array($pair['new'] ?? null) ? json_encode($pair['new']) : ($pair['new'] ?? '—') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </details>
        </div>
    @endif

    {{-- Card: Created rows --}}
    @if (!empty($report['created_rows']))
        <div class="rounded-lg border bg-white dark:bg-slate-900 dark:border-slate-800 p-6">
            <details open class="group">
                <summary class="cursor-pointer font-semibold flex items-center justify-between">
                    Created rows ({{ count($report['created_rows']) }})
                    <span class="text-slate-400 group-open:rotate-90 transition-transform">›</span>
                </summary>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="text-left border-b">
                            <th class="py-2 pr-4">Row</th>
                            <th class="py-2 pr-4">ID</th>
                            <th class="py-2 pr-4">Name</th>
                            <th class="py-2">Fields set</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($report['created_rows'] as $c)
                            <tr class="border-b last:border-none">
                                <td class="py-2 pr-4">{{ $c['row'] }}</td>
                                <td class="py-2 pr-4">{{ $c['id'] }}</td>
                                <td class="py-2 pr-4 font-medium">{{ $c['name'] }}</td>
                                <td class="py-2">
                                    @foreach (($c['keys'] ?? []) as $k)
                                        <code class="px-2 py-0.5 mr-1 mb-1 inline-block rounded bg-slate-100 dark:bg-slate-800">{{ $k }}</code>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        </div>
    @endif

    {{-- Card: Skipped rows --}}
    @if (!empty($report['skipped_rows']))
        <div class="rounded-lg border bg-white dark:bg-slate-900 dark:border-slate-800 p-6">
            <details class="group">
                <summary class="cursor-pointer font-semibold flex items-center justify-between">
                    Skipped rows ({{ count($report['skipped_rows']) }})
                    <span class="text-slate-400 group-open:rotate-90 transition-transform">›</span>
                </summary>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="text-left border-b">
                            <th class="py-2 pr-4">Row</th>
                            <th class="py-2">Reason</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($report['skipped_rows'] as $s)
                            <tr class="border-b last:border-none">
                                <td class="py-2 pr-4">{{ $s['row'] ?? '—' }}</td>
                                <td class="py-2 text-slate-600">{{ $s['reason'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        </div>
    @endif

    {{-- Card: Errors --}}
    @if (!empty($report['errors']))
        <div class="rounded-lg border bg-white dark:bg-slate-900 dark:border-slate-800 p-6">
            <details class="group open">
                <summary class="cursor-pointer font-semibold flex items-center justify-between">
                    Errors ({{ count($report['errors']) }})
                    <span class="text-slate-400 group-open:rotate-90 transition-transform">›</span>
                </summary>
                <ul class="mt-3 list-disc ml-6 text-sm text-red-700 space-y-1">
                    @foreach ($report['errors'] as $err)
                        <li>Row {{ $err['row'] ?? '?' }} — {{ $err['message'] ?? 'Unknown error' }}</li>
                    @endforeach
                </ul>
            </details>
        </div>
    @endif
</div>
