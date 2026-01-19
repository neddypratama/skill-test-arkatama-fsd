<?php

use Livewire\Volt\Component;
use App\Models\Checkups;

new class extends Component {
    public Checkups $checkup;

    public function mount(Checkups $checkup): void
    {
        $this->checkup = $checkup->load(['pet.owner', 'treatment']);
    }
};
?>

<div>
    <x-header title="Detail Pemeriksaan {{ $checkup->kode }}" separator progress-indicator />

    <x-card>

        {{-- INFORMASI PEMERIKSAAN --}}
        <div class="p-7 mt-2 rounded-lg shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="mb-3">Kode Pemeriksaan</p>
                    <p class="font-semibold">{{ $checkup->kode }}</p>
                </div>

                <div>
                    <p class="mb-3">Tanggal</p>
                    <p class="font-semibold">
                        {{ $checkup->created_at->format('d-m-Y H:i') }}
                    </p>
                </div>

                <div>
                    <p class="mb-3">Treatment</p>
                    <p class="font-semibold">
                        {{ $checkup->treatment?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="mb-3">Pemilik</p>
                    <p class="font-semibold">
                        {{ $checkup->pet?->owner?->name ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- DETAIL HEWAN --}}
        <div class="p-7 mt-4 rounded-lg shadow-md">
            <p class="mb-4 font-semibold">Detail Hewan</p>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="mb-1 text-gray-500">Nama Hewan</p>
                    <p class="font-semibold">
                        {{ $checkup->pet?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="mb-1 text-gray-500">Jenis</p>
                    <p class="font-semibold">
                        {{ $checkup->pet?->jenis ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="mb-1 text-gray-500">Usia</p>
                    <p class="font-semibold">
                        {{ $checkup->usia }} Tahun
                    </p>
                </div>

                <div>
                    <p class="mb-1 text-gray-500">Berat</p>
                    <p class="font-semibold">
                        {{ $checkup->berat }} Kg
                    </p>
                </div>
            </div>
        </div>

    </x-card>

    <div class="mt-6 flex gap-3">
        <x-button label="Kembali" link="/checkup" />
    </div>
</div>
