<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockistPackage extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'position',
        'commission',
        'restock_commission',
        'sponsor_commission',
        'pickup_restock_commission',
    ];
}
