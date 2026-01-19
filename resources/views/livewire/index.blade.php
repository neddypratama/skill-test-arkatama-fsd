<?php

use Livewire\Volt\Component;
use App\Models\Owner;
use App\Models\Pet;
use App\Models\Treatment;
use App\Models\Checkups;

new class extends Component {
    public function with(): array
    {
        return [
            'totalOwners' => Owner::count(),
            'totalPets' => Pet::count(),
            'totalTreatments' => Treatment::count(),
            'totalCheckups' => Checkups::count(),

            'latestCheckups' => Checkups::with(['pet.owner', 'treatment'])
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }
};
?>
<div>
    <x-header title="Dashboard PetCare+" subtitle="Ringkasan Data Klinik Hewan" separator />

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

        <x-card class="text-center">
            <p class="text-sm text-gray-500">Total Pemilik</p>
            <p class="text-3xl font-bold">{{ $totalOwners }}</p>
        </x-card>

        <x-card class="text-center">
            <p class="text-sm text-gray-500">Total Hewan</p>
            <p class="text-3xl font-bold">{{ $totalPets }}</p>
        </x-card>

        <x-card class="text-center">
            <p class="text-sm text-gray-500">Total Treatment</p>
            <p class="text-3xl font-bold">{{ $totalTreatments }}</p>
        </x-card>

        <x-card class="text-center">
            <p class="text-sm text-gray-500">Total Pemeriksaan</p>
            <p class="text-3xl font-bold">{{ $totalCheckups }}</p>
        </x-card>

    </div>

    {{-- PEMERIKSAAN TERBARU --}}
    <x-card>
        <p class="mb-4 font-semibold">Pemeriksaan Terbaru</p>

        @forelse ($latestCheckups as $checkup)
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 p-4 border-b text-sm">
                <div>
                    <p class="text-gray-500">Kode</p>
                    <p class="font-semibold">{{ $checkup->kode }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Pemilik</p>
                    <p class="font-semibold">
                        {{ $checkup->pet?->owner?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Hewan</p>
                    <p class="font-semibold">
                        {{ $checkup->pet?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Treatment</p>
                    <p class="font-semibold">
                        {{ $checkup->treatment?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Tanggal</p>
                    <p class="font-semibold">
                        {{ $checkup->created_at->format('d-m-Y') }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500">Belum ada data pemeriksaan.</p>
        @endforelse
    </x-card>
</div>
