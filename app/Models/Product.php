<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'location_id',
        'brand_id',
        'product_name',
        'rbp',
        'eup',
        'yield',
        'kuota_nasional',
        'kuota_lokal',
        'total_kuota',
        'validity',
        'product_type',
        'flag_type',
        'denom', 
    ];
    
    public function Location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
    public function Brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
