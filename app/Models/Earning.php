<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{
    use HasFactory;
    protected $fillable = ['member_id', 'type', 'value', 'description' , 'settlement_member_id', 'transaction_id'];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
