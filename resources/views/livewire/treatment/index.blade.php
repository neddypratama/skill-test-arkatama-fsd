<?php

use App\Models\Treatment;
use Illuminate\Support\Collection;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';

    public bool $drawer = false;

    public array $sortBy = ['column' => 'name', 'direction' => 'asc'];

    public function updated($property): void
    {
        if (!is_array($property) && $property != '') {
            $this->resetPage();
        }
    }

    public function clear(): void
    {
        $this->reset();
        $this->resetPage();
        $this->success('Filters cleared.', position: 'toast-bottom');
    }

    // Delete action
    public function delete($id): void
    {
        Treatment::find($id)->delete();
        $this->warning("Will delete #$id", 'It is fake.', position: 'toast-bottom');
    }

    // Table headers
    public function headers(): array
    {
        return [['key' => 'id', 'label' => '#', 'class' => 'w-1'], ['key' => 'name', 'label' => 'Name', 'class' => 'w-64'], ['key' => 'description', 'label' => 'Description', 'sortable' => false]];
    }

    public function treatment(): LengthAwarePaginator
    {
        return Treatment::query()->when($this->search, fn(Builder $q) => $q->where('name', 'like', "%$this->search%"))->orderBy(...array_values($this->sortBy))->paginate(5);
    }

    public function with(): array
    {

        return [
            'treatments' => $this->treatment(),
            'headers' => $this->headers(),
        ];
    }
}; ?>

<div>
    <!-- HEADER -->
    <x-header title="Data Treatment" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Create" link="/treatment/create" responsive icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$treatments" :sort-by="$sortBy" with-pagination
            link="treatment/{id}/edit?name={name}">
            @scope('actions', $treatment)
                <x-button icon="o-trash" wire:click="delete({{ $treatment['id'] }})" wire:confirm="Are you sure?" spinner
                    class="btn-ghost btn-sm text-error" />
            @endscope
        </x-table>
    </x-card>
</div>
