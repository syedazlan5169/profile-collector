<?php

namespace App\Livewire\People;

use App\Actions\People\PersonDelete;
use App\Actions\People\PersonRestore;
use App\Models\Person;
use Livewire\Component;
use Livewire\WithPagination;

class PeopleIndex extends Component
{
    use WithPagination;

    public string $q = '';
    public bool $showTrashed = false;
    public ?int $pendingDeleteId = null;
    public bool $showDeleteModal = false;

    public function updatingQ() { $this->resetPage(); }
    public function updatedShowTrashed() { $this->resetPage(); }

    public function restore(int $id, PersonRestore $restore)
    {
        $restore($id);
        session()->flash('success', 'Person restored successfully.');
    }

    public function confirmDelete(int $personId)
    {
        $this->pendingDeleteId = $personId;
        $this->showDeleteModal = true;
    }

    public function delete(PersonDelete $delete)
    {
        $person = Person::findOrFail($this->pendingDeleteId);

        try {
            $delete($person); // soft delete by default
            session()->flash('success', $person->name . ' deleted.');
        } catch (\Throwable $e) {
            // Optionally handle FK errors nicely
            session()->flash('error', 'Unable to delete this record.');
        }

        $this->reset(['pendingDeleteId', 'showDeleteModal']);
        // Refresh list
        $this->resetPage();
    }

    public function render()
    {
        $query = Person::query()
            ->when($this->q !== '', function ($builder) {
                $builder->where(function ($query) {
                    $query->where('name', 'like', '%'.$this->q.'%')
                        ->orWhere('nric', 'like', '%'.$this->q.'%');
                });
            });

        // Include soft-deleted when toggled
        if ($this->showTrashed) {
            $query->withTrashed()
                // show deleted first, then newest
                ->orderByDesc('deleted_at')
                ->orderByDesc('id');
        } else {
            // normal ordering
            $query->orderByDesc('id');
        }

        $people = $query->paginate(10);

        return view('livewire.people.people-index', compact('people'));
    }

}

