<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('checkups', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->foreignId('owner_id')->constrained('owners');
            $table->foreignId('pet_id')->constrained('pets');
            $table->foreignId('treatment_id')->constrained('treatments');
            $table->integer('usia');
            $table->decimal('berat', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkups');
    }
};
