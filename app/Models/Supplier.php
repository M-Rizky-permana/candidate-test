<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function layups(): HasMany
    {
        return $this->hasMany(CltLayup::class);
    }

    public function layers(): HasManyThrough
    {
        return $this->hasManyThrough(
            CltLayer::class,
            CltLayup::class,
            'supplier_id',
            'layup_id'
        );
    }
}
