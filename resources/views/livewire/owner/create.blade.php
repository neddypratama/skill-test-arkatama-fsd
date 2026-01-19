<?php

use Livewire\Volt\Component;
use App\Models\Owner;
use Mary\Traits\Toast;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;

new class extends Component {
    use Toast, WithFileUploads;

    public Owner $owner;

    #[Rule('required')]
    public string $name = '';

    #[Rule('required')]
    public ?string $no_hp = null;

    #[Rule('required')]
    public ?bool $verifikasi_no = false;

    public function mount(): void
    {
        $this->owner = new Owner();
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        // Create pemilik
        $owner = Owner::create($data);

        // You can toast and redirect to any route
        $this->success('pemilik created with success.', redirectTo: '/owner');
    }
}; ?>

<div>
    <x-header title="Tambah Data Owner" separator />

    <x-form wire:submit="save">
        {{--  Basic section  --}}
        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info from pemilik" size="text-2xl" />
            </div>
            <div class="col-span-3 grid gap-3">
                <x-input label="Name" wire:model="name" />
                <x-input label="No HP" wire:model="no_hp" />
                <x-checkbox label="Verifikasi No HP" wire:model="verifikasi_no" />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/owner" />
            {{-- The important thing here is `type="submit"` --}}
            {{-- The spinner property is nice! --}}
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
