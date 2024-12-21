<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class RankThreshold extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'rank',
        'left_points_threshold',
        'right_points_threshold',
        'pv_requirement',
        'level_from',
        'level_to',
    ];

    
}
