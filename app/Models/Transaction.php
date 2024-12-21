<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'member_id',
        'type',
        'amount',
        'pv',
        'payment_method',
        'payment_method_id',
        'status',
    ];
}
