<?php

namespace App\Livewire\People;

use App\Actions\People\PersonUpdate;
use App\Models\Person;
use Livewire\Component;

class EditPerson extends Component
{
    public Person $person;
    
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
    public string $branch = '';
    public array $car = [
        'make'  => '',
        'model' => '',
        'color' => '',
        'plate' => '',
    ];

    public function mount(Person $person)
    {
        $this->person = $person;
        
        // Populate form fields with existing data
        $this->name = $person->name ?? '';
        $this->nric = $person->nric ?? '';
        $this->date_of_birth = $person->date_of_birth ? $person->date_of_birth->format('Y-m-d') : '';
        $this->address = $person->address ?? '';
        $this->phone = $person->phone ?? '';
        $this->email = $person->email ?? '';
        $this->gender = $person->gender ?? '';
        $this->rank = $person->rank ?? '';
        $this->pk_number = $person->pk_number ?? '';
        $this->union_number = $person->union_number ?? '';
        $this->department = $person->department ?? '';
        $this->branch = $person->branch ?? '';

        if ($person->car) {
            $this->car = [
                'make'  => $person->car['make'] ?? '',
                'model' => $person->car['model'] ?? '',
                'color' => $person->car['color'] ?? '',
                'plate' => $person->car['plate'] ?? '',
            ];
        }
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:120',
            'nric' => ['required', 'regex:/^\d{12}$/', 'unique:people,nric,' . $this->person->id],
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'gender' => 'nullable|in:male,female',
            'rank' => 'nullable|string|max:50',
            'pk_number' => 'nullable|string|max:50',
            'union_number' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:50',
            'branch' => 'nullable|string|max:50',
            'car' => 'nullable|array',
            'car.make' => 'nullable|string|max:50',
            'car.model' => 'nullable|string|max:50',
            'car.color' => 'nullable|string|max:50',
            'car.plate' => 'nullable|string|max:50',
        ];
    }

    public function update(PersonUpdate $update)
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
        
        $person = $update($this->person, $data);

        session()->flash('success', 'Person updated: ' . $person->name);
        return redirect()->route('people.show', $person);
    }

    public function render()
    {
        return view('livewire.people.edit-person');
    }
}
