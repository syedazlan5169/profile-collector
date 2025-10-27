<?php

namespace App\Actions\People;

use App\Models\Person;
use Illuminate\Support\Facades\DB;

class PersonRestore
{
    public function __invoke(int $id): Person
    {
        return DB::transaction(function () use ($id) {
            $person = Person::withTrashed()->findOrFail($id);
            $person->restore();
            return $person->refresh();
        });
    }
}
