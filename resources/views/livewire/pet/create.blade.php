<?php

use Livewire\Volt\Component;
use App\Models\Pet;
use App\Models\Owner;
use Mary\Traits\Toast;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;

new class extends Component {
    use Toast, WithFileUploads;

    public Pet $pet;

    #[Rule('required')]
    public string $name = '';

    #[Rule('required')]
    public string $jenis = '';

    #[Rule('required')]
    public int $owner_id = 0;

    public function mount(): void
    {
        $this->pet = new Pet();
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        // Create Pet
        $pet = Pet::create($data);

        // You can toast and redirect to any route
        $this->success('Pet created with success.', redirectTo: '/pet');
    }

    public function with(): array
    {
        return [
            'owner' => Owner::all(),
            'type' => [['id' => 'Kucing', 'name' => 'Kucing'], ['id' => 'Anjing', 'name' => 'Anjing']],
        ];
    }
}; ?>



<div>
    <x-header title="Tambah Data Hewan" separator />

    <x-form wire:submit="save">
        {{--  Basic section  --}}
        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info from Pet" size="text-2xl" />
            </div>
            <div class="col-span-3 grid gap-3">
                <x-input label="Name" wire:model="name" />
                <x-select label="Jenis" wire:model="jenis"  :options="$type"/>
                <x-choices-offline label="Pemilik" wire:model="owner_id" :options="$owner" option-value="id"
                    option-label="name" placeholder="Pilih Owner" single searchable />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/pet" />
            {{-- The important thing here is `type="submit"` --}}
            {{-- The spinner property is nice! --}}
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
