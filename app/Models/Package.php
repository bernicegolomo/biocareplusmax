<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'actual_pv',
        'bonus_pv',
        'content',
        'status',
    ];
    
    public function members()
    {
        return $this->belongsToMany(Member::class, 'members_packages', 'package_id', 'member_id')
                    ->withTimestamps();
    }
}
