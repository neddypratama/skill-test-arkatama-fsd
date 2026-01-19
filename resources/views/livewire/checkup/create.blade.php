<?php

use Livewire\Volt\Component;
use App\Models\Owner;
use App\Models\Pet;
use App\Models\Treatment;
use App\Models\Checkups;
use Mary\Traits\Toast;
use Livewire\Attributes\Rule;
use Illuminate\Support\Str;

new class extends Component {
    use Toast;

    #[Rule('required')]
    public string $data_hewan = '';

    #[Rule('required|exists:owners,id')]
    public int $owner_id = 0;

    #[Rule('required|exists:treatments,id')]
    public int $treatment_id = 0;

    public function with(): array
    {
        return [
            // hanya owner dengan no hp terverifikasi
            'owners' => Owner::where('verifikasi_no', true)->get(),
            'treatments' => Treatment::all(),
        ];
    }

    public function save(): void
    {
        $this->validate();

        // ===============================
        // 1️⃣ Bersihkan spasi berlebih
        // ===============================
        $raw = preg_replace('/\s+/', ' ', trim($this->data_hewan));
        $parts = explode(' ', $raw);

        if (count($parts) < 4) {
            $this->error('Format input hewan tidak valid');
            return;
        }

        [$name, $jenis, $usiaRaw, $beratRaw] = $parts;

        // ===============================
        // 2️⃣ Parsing USIA (fleksibel)
        // ===============================
        if (!preg_match('/(\d+)/', strtolower($usiaRaw), $ageMatch)) {
            $this->error('Format usia tidak valid');
            return;
        }
        $usia = (int) $ageMatch[1];

        // ===============================
        // 3️⃣ Parsing BERAT (fleksibel)
        // ===============================
        $beratClean = str_replace(',', '.', strtolower($beratRaw));
        $beratClean = preg_replace('/[^0-9.]/', '', $beratClean);

        if (!is_numeric($beratClean)) {
            $this->error('Format berat tidak valid');
            return;
        }
        $berat = (float) $beratClean;

        // ===============================
        // 4️⃣ Normalisasi
        // ===============================
        $name = strtoupper($name);
        $jenis = strtoupper($jenis);

        // ===============================
        // 5️⃣ Validasi unik hewan per owner
        // ===============================
        $exists = Pet::where('owner_id', $this->owner_id)->where('name', $name)->where('jenis', $jenis)->exists();

        if ($exists) {
            $this->error('Hewan dengan nama dan jenis yang sama sudah dimiliki owner ini');
            return;
        }

        // ===============================
        // 6️⃣ Generate KODE HEWAN (HHMMXXXXYYYY)
        // ===============================
        $time = now()->format('Hi');
        $ownerId = str_pad($this->owner_id, 4, '0', STR_PAD_LEFT);
        $petCount = Pet::where('owner_id', $this->owner_id)->count() + 1;
        $petNumber = str_pad($petCount, 4, '0', STR_PAD_LEFT);

        $kodeHewan = $time . $ownerId . $petNumber;

        // ===============================
        // 7️⃣ Simpan PET
        // ===============================
        $pet = Pet::create([
            'owner_id' => $this->owner_id,
            'name' => $name,
            'jenis' => $jenis,
        ]);

        $checkup = Checkups::create([
            'owner_id' => $this->owner_id,
            'treatment_id' => $this->treatment_id,
            'pet_id' => $pet->id,
            'kode' => $kodeHewan,
            'usia' => $usia,
            'berat' => $berat,
        ]);

        $this->success('Data hewan dan Pemeriksaan berhasil disimpan', redirectTo: '/checkup');
    }
};
?>

<div>
    <x-header title="Tambah Data Pemeriksaan" separator />

    <x-form wire:submit="save">
        <div class="grid gap-3">
            <x-input label="Data Hewan" wire:model.defer="data_hewan" placeholder="Milo Kucing 2Th 4.5kg"
                hint="Format: NAMA JENIS USIA BERAT" />

            <x-choices-offline label="Owner" wire:model="owner_id" :options="$owners" option-value="id" option-label="name"
                placeholder="Pilih Pemilik (Terverifikasi)" single searchable />

            <x-choices-offline label="Treatment" wire:model="treatment_id" :options="$treatments" option-value="id"
                option-label="name" placeholder="Pilih Treatment" single searchable />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" link="/checkup" />
            <x-button label="Simpan" icon="o-paper-airplane" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
