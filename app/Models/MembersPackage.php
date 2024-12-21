<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembersPackage extends Model
{
    use HasFactory;
    protected $fillable = [
        'member_id',
        'package_id',
        'transaction_id',
        'amount',
        'subcribe_date',
    ];
}
