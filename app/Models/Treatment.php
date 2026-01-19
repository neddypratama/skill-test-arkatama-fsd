<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    protected $fillable = ['name', 'description'];

    public function checkups()
    {
        return $this->hasOne(Checkups::class);
    }
}
