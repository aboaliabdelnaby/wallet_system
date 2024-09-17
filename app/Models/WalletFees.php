<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletFees extends Model
{
    use HasFactory;
    protected $fillable = [
        'percentage',
        'start_amount'
    ];
}
