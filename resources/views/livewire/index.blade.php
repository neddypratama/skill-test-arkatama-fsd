<?php

use Livewire\Volt\Component;
use App\Models\Pet;
use App\Models\Owner;
use App\Models\Checkups;
use App\Models\Treatment;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

new class extends Component {
    public string $period = 'month';
    public array $checkupChart = [];
    public array $petTypeChart = [];
    public array $treatmentChart = [];

    public function mount()
    {
        $this->generateCharts();
    }

    public function updatedPeriod()
    {
        $this->generateCharts();
    }

    private function dateRange()
    {
        $now = Carbon::now();

        return match ($this->period) {
            'today' => [$now->startOfDay(), $now->endOfDay()],
            'week' => [$now->startOfWeek(), $now->endOfWeek()],
            'year' => [$now->startOfYear(), $now->endOfYear()],
            default => [$now->startOfMonth(), $now->endOfMonth()],
        };
    }

    private function generateCharts()
    {
        [$start, $end] = $this->dateRange();

        /* ===============================
         * 1️⃣ CHECKUP PER HARI
         * =============================== */
        $checkups = Checkups::whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn($c) => $c->created_at->format('Y-m-d'));

            dd($checkups);

        $labels = [];
        $data = [];

        $period = CarbonPeriod::create(Carbon::parse($start)->startOfDay(), Carbon::parse($end)->endOfDay());

        foreach ($period as $date) {
            $key = $date->format('Y-m-d');

            $labels[] = $date->format('d M');
            $data[] = $checkups->get($key, collect())->count();
        }

        $this->checkupChart = [
            'type' => 'line',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Jumlah Pemeriksaan',
                        'data' => $data,
                        'borderColor' => '#4CAF50',
                        'backgroundColor' => 'rgba(76,175,80,0.2)',
                        'fill' => true,
                        'tension' => 0.3,
                    ],
                ],
            ],
        ];

        /* ===============================
         * 2️⃣ JENIS HEWAN
         * =============================== */
        $pets = Pet::select('jenis')->get()->groupBy('jenis');

        $this->petTypeChart = [
            'type' => 'pie',
            'data' => [
                'labels' => $pets->keys()->toArray(),
                'datasets' => [
                    [
                        'data' => $pets->map->count()->values()->toArray(),
                        'backgroundColor' => $pets->map(fn() => sprintf('#%06X', mt_rand(0, 0xffffff)))->values()->toArray(),
                    ],
                ],
            ],
        ];

        /* ===============================
         * 3️⃣ TREATMENT TERPOPULER
         * =============================== */
        $treatments = Checkups::with('treatment')->selectRaw('treatment_id, COUNT(*) as total')->groupBy('treatment_id')->orderByDesc('total')->take(5)->get();

        $this->treatmentChart = [
            'type' => 'bar',
            'data' => [
                'labels' => $treatments->map(fn($t) => $t->treatment->name)->toArray(),
                'datasets' => [
                    [
                        'label' => 'Jumlah',
                        'data' => $treatments->pluck('total')->toArray(),
                        'backgroundColor' => '#2196F3',
                    ],
                ],
            ],
            'options' => [
                'scales' => [
                    'y' => ['beginAtZero' => true],
                ],
            ],
        ];
    }

    public function with()
    {
        return [
            'totalCheckups' => Checkups::count(),
            'totalPets' => Pet::count(),
            'totalOwners' => Owner::count(),
            'topTreatment' => Treatment::withCount('checkups')->orderByDesc('checkups_count')->first()?->name ?? '-',
        ];
    }
};
?>

<div class="p-4 space-y-6">
    <x-header title="Dashboard Petcare" separator>
        <x-slot:actions>
            <x-select wire:model.live="period" :options="[
                ['id' => 'today', 'name' => 'Hari Ini'],
                ['id' => 'week', 'name' => 'Minggu Ini'],
                ['id' => 'month', 'name' => 'Bulan Ini'],
                ['id' => 'year', 'name' => 'Tahun Ini'],
            ]" option-label="name" option-value="id" />
        </x-slot:actions>
    </x-header>

    <!-- KPI -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <x-card>
            <div class="flex gap-3 items-center">
                <x-icon name="o-clipboard-document" class="w-8 h-8 text-green-500" />
                <div>
                    <p class="text-sm">Total Pemeriksaan</p>
                    <p class="text-xl font-bold">{{ $totalCheckups }}</p>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex gap-3 items-center">
                <x-icon name="o-heart" class="w-8 h-8 text-pink-500" />
                <div>
                    <p class="text-sm">Total Hewan</p>
                    <p class="text-xl font-bold">{{ $totalPets }}</p>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex gap-3 items-center">
                <x-icon name="o-users" class="w-8 h-8 text-blue-500" />
                <div>
                    <p class="text-sm">Total Pemilik</p>
                    <p class="text-xl font-bold">{{ $totalOwners }}</p>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex gap-3 items-center">
                <x-icon name="o-star" class="w-8 h-8 text-yellow-500" />
                <div>
                    <p class="text-sm">Treatment Terfavorit</p>
                    <p class="text-lg font-bold">{{ $topTreatment }}</p>
                </div>
            </div>
        </x-card>
    </div>

    <!-- CHART -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <x-card class="lg:col-span-2">
            <x-slot:title>Pemeriksaan</x-slot:title>
            <x-chart wire:model="checkupChart" />
        </x-card>

        <x-card>
            <x-slot:title>Jenis Hewan</x-slot:title>
            <x-chart wire:model="petTypeChart" />
        </x-card>

        <x-card class="lg:col-span-3">
            <x-slot:title>Treatment Terpopuler</x-slot:title>
            <x-chart wire:model="treatmentChart" />
        </x-card>
    </div>
</div>
