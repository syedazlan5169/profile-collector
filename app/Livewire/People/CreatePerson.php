<?php

namespace App\Livewire\People;

use App\Actions\People\PersonCreate;
use Livewire\Component;

class CreatePerson extends Component
{
    public string $name = '';
    public string $nric = '';
    public string $date_of_birth = '';
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public string $gender = '';
    public string $rank = '';
    public string $pk_number = '';
    public string $union_number = '';
    public string $department = '';
    public array $car = [
        'make'  => '',
        'model' => '',
        'color' => '',
        'plate' => '',
    ];

    protected $rules = [
        'name' => 'required|string|max:120',
        'nric' => ['required','regex:/^\d{12}$/','unique:people,nric'],
        'date_of_birth' => 'nullable|date',
        'address' => 'nullable|string|max:255',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'gender' => 'nullable|in:male,female',
        'rank' => 'nullable|string|max:50',
        'pk_number' => 'nullable|string|max:50',
        'union_number' => 'nullable|string|max:50',
        'department' => 'nullable|string|max:50',
        'car' => 'nullable|array',
        'car.make' => 'nullable|string|max:50',
        'car.model' => 'nullable|string|max:50',
        'car.color' => 'nullable|string|max:50',
        'car.plate' => 'nullable|string|max:50',
    ];

    public function save(PersonCreate $create)
    {
        $data = $this->validate();

        // normalize (optional)
        if (!empty($data['car'])) {
            $data['car']['plate'] = isset($data['car']['plate'])
                ? strtoupper(trim($data['car']['plate']))
                : null;

            // remove empty keys
            $data['car'] = array_filter($data['car'], fn($v) => $v !== null && $v !== '');
            if ($data['car'] === []) {
                $data['car'] = null;
            }
        }
        $person = $create($data);

        session()->flash('success', 'Person created: '.$person->name);
        return redirect()->route('people.index');
    }

    public function render()
    {
        return view('livewire.people.create-person');
    }
}
