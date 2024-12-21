<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    use HasFactory;
    protected $fillable = [
        'token',
        'amount',
        'usedby',
        'usedfor',
        'used_date',
        'status',
    ];
}
