<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PackageBonusRate extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'package_id',
        'level',
        'bonus_rate',
    ];

    
}
