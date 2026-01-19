<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checkups extends Model
{
    protected $fillable = ['kode', 'owner_id', 'pet_id', 'treatment_id', 'usia', 'berat'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }
}
