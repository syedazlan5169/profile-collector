<?php

namespace App\Actions\People;

use App\Models\Person;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class PersonDelete
{
    /**
     * Soft-delete by default. Pass $force = true to permanently delete.
     */
    public function __invoke(Person $person, bool $force = false): void
    {
        DB::transaction(function () use ($person, $force) {
            // put any domain checks here (e.g., cannot delete if has unpaid invoices)
            if ($force) {
                // Hard delete (will fail if FK constraints exist)
                $person->forceDelete();
            } else {
                // Soft delete (safe default)
                $person->delete();
            }
        });
    }
}
