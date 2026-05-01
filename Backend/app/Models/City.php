<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = ['name', 'name_ar'];

    public function regions(): HasMany
    {
        return $this->hasMany(Region::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }
}
