<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Owner extends Model
{
    protected $fillable = ['name', 'no_hp', 'verifikasi_no'];

    public function pet(): HasMany
    {
        return $this->hasMany(Pet::class);
    }

    public function checkups(): HasMany
    {
        return $this->hasMany(Checkups::class);
    }
}
