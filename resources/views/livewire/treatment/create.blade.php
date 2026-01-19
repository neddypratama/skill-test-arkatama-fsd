<?php

use Livewire\Volt\Component;
use App\Models\Treatment;
use Mary\Traits\Toast;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;

new class extends Component {
    use Toast, WithFileUploads;

    public Treatment $treatment;

    #[Rule('required')]
    public string $name = '';

    #[Rule('sometimes')]
    public ?string $description = null;

    public function mount(): void
    {
        $this->Treatment = new Treatment();
    }

    public function save(): void
    {
        // Validate
        $data = $this->validate();

        // Create pemilik
        $treatment = Treatment::create($data);

        // You can toast and redirect to any route
        $this->success('pemilik created with success.', redirectTo: '/treatment');
    }
}; ?>

<div>
    <x-header title="Tambah Data Treatment" separator />

    <x-form wire:submit="save">
        {{--  Basic section  --}}
        <div class="lg:grid grid-cols-5">
            <div class="col-span-2">
                <x-header title="Basic" subtitle="Basic info from pemilik" size="text-2xl" />
            </div>
            <div class="col-span-3 grid gap-3">
                <x-input label="Name" wire:model="name" />
                <x-textarea label="Description" wire:model="description" />
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/treatment" />
            {{-- The important thing here is `type="submit"` --}}
            {{-- The spinner property is nice! --}}
            <x-button label="Create" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
