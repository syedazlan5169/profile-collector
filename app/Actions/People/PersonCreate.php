<?php

namespace App\Actions\People;

use App\Models\Person;
use Illuminate\Support\Facades\DB;

class PersonCreate
{
    public function __invoke(array $data): Person
    {
        return DB::transaction(function () use ($data) {
            // normalize
            $data['nric'] = preg_replace('/\D/','', $data['nric']);
            $data['car'] = $data['car'] ?? null;

            // create
            return Person::create([
                'name' => $data['name'],
                'nric' => $data['nric'],
                'date_of_birth' => $data['date_of_birth'],
                'address' => $data['address'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'gender' => $data['gender'],
                'rank' => $data['rank'],
                'pk_number' => $data['pk_number'],
                'union_number' => $data['union_number'],
                'department' => $data['department'],
                'car' => $data['car'],
            ]);
        });
    }
}
