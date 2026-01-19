<?php

use App\Models\Checkups;
use Livewire\Volt\Component;
use Mary\Traits\Toast;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

new class extends Component {
    use Toast, WithPagination;

    public string $search = '';

    public array $sortBy = [
        'column' => 'created_at',
        'direction' => 'desc',
    ];

    public function updated($property): void
    {
        if (!is_array($property)) {
            $this->resetPage();
        }
    }

    public function clear(): void
    {
        $this->reset('search');
        $this->resetPage();
        $this->success('Filter dibersihkan');
    }

    public function delete(int $id): void
    {
        Checkups::findOrFail($id)->delete();
        $this->success('Data berhasil dihapus');
    }

    /* ================= HEADER TABLE ================= */
    public function headers(): array
    {
        return [['key' => 'kode', 'label' => 'Kode'], ['key' => 'owner.name', 'label' => 'Owner'], ['key' => 'pet.name', 'label' => 'Pet'], ['key' => 'treatment.name', 'label' => 'Treatment'], ['key' => 'created_at', 'label' => 'Tanggal Pemeriksaan']];
    }

    /* ================= DATA ================= */
    public function checkups(): LengthAwarePaginator
    {
        return Checkups::query()
            ->with(['pet', 'owner', 'treatment'])
            ->when($this->search, function (Builder $q) {
                $q->where('kode', 'like', "%{$this->search}%")
                    ->orWhereHas('pet', fn($p) => $p->where('name', 'like', "%{$this->search}%"))
                    ->orWhereHas('owner', fn($o) => $o->where('name', 'like', "%{$this->search}%"));
            })
            ->orderBy(...array_values($this->sortBy))
            ->paginate(5);
    }

    public function with(): array
    {
        return [
            'headers' => $this->headers(),
            'checkups' => $this->checkups(),
        ];
    }
};
?>

<div>
    <!-- HEADER -->
    <x-header title="Data Pemeriksaan" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input placeholder="Search..." wire:model.live.debounce="search" clearable icon="o-magnifying-glass" />
        </x-slot:middle>
        <x-slot:actions>
            <x-button label="Create" link="/checkup/create" responsive icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <!-- TABLE  -->
    <x-card shadow>
        <x-table :headers="$headers" :rows="$checkups" :sort-by="$sortBy" with-pagination
            link="checkup/{id}/show?kode={kode}">
            @scope('actions', $row)
                <div class="flex">
                    <x-button icon="o-trash" wire:click="delete({{ $row['id'] }})" wire:confirm="Are you sure?" spinner
                        class="btn-ghost btn-sm text-error" />
                    <x-button icon="o-pencil" link="/checkup/{{ $row['id'] }}/edit?kode={{ $row['kode'] }}"
                        class="btn-ghost btn-sm text-yellow-500" tooltip="Edit" />

                </div>
            @endscope
        </x-table>
    </x-card>
</div>
