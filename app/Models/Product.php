<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'location_id',
        'brand_id',
        'user_id',
        'product_name',
        'RBP',
        'EUP',
        'kuota_nasional',
        'kuota_lokal',
        'validity',
        'product_type',
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
