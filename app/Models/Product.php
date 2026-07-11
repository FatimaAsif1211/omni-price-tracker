<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Define table name matching migration [cite: 209]
    protected $table = 'products';

    // Allow fields to be populated via bulk array processing
    protected $fillable = [
        'product_name',
        'store_name',
        'price',
        'product_link',
        'image_url',
        'fetched_at'
    ];
}