<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = ['name', 'jenis', 'owner_id'];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function checkups()
    {
        return $this->hasMany(Checkups::class);
    }
}
