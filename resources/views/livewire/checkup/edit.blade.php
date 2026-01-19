<?php

use Livewire\Volt\Component;
use App\Models\Owner;
use App\Models\Pet;
use App\Models\Treatment;
use App\Models\Checkups;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public Checkups $checkup;

    public string $data_hewan = '';
    public int $owner_id;
    public int $treatment_id;

    public function mount(Checkups $checkup): void
    {
        $this->checkup = $checkup;

        $pet = $checkup->pet;

        // Prefill input gabungan
        $this->data_hewan = "{$pet->name} {$pet->jenis} {$checkup->usia}Th {$checkup->berat}kg";
        $this->owner_id = $pet->owner_id;
        $this->treatment_id = $checkup->treatment_id;
    }

    public function with(): array
    {
        return [
            'owners' => Owner::where('verifikasi_no', true)->get(),
            'treatments' => Treatment::all(),
        ];
    }

    public function save(): void
    {
        // ===============================
        // 1️⃣ Bersihkan spasi
        // ===============================
        $raw = preg_replace('/\s+/', ' ', trim($this->data_hewan));
        $parts = explode(' ', $raw);

        if (count($parts) < 4) {
            $this->error('Format input hewan tidak valid');
            return;
        }

        [$name, $jenis, $usiaRaw, $beratRaw] = $parts;

        // ===============================
        // 2️⃣ Parsing USIA
        // ===============================
        if (!preg_match('/(\d+)/', strtolower($usiaRaw), $ageMatch)) {
            $this->error('Format usia tidak valid');
            return;
        }
        $usia = (int) $ageMatch[1];

        // ===============================
        // 3️⃣ Parsing BERAT
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
        // 5️⃣ Validasi unik (kecuali diri sendiri)
        // ===============================
        $exists = Pet::where('owner_id', $this->owner_id)->where('name', $name)->where('jenis', $jenis)->where('id', '!=', $this->checkup->pet_id)->exists();

        if ($exists) {
            $this->error('Hewan dengan nama dan jenis yang sama sudah dimiliki owner ini');
            return;
        }

        // ===============================
        // 6️⃣ Update PET
        // ===============================
        $pet = $this->checkup->pet;
        $pet->update([
            'name' => $name,
            'jenis' => $jenis,
            'owner_id' => $this->owner_id,
        ]);

        // ===============================
        // 7️⃣ Update CHECKUP
        // ===============================
        $this->checkup->update([
            'treatment_id' => $this->treatment_id,
            'usia' => $usia,
            'berat' => $berat,
        ]);

        $this->success('Data pemeriksaan berhasil diperbarui', redirectTo: '/checkup');
    }
};
?>

<div>
    <x-header title="Edit Data Pemeriksaan" separator />

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
            <x-button label="Update" icon="o-check" spinner="save" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</div>
