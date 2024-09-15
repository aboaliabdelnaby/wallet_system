<?php

namespace App\Models;

use App\Http\Enum\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'amount',
        'amount',
        'user_id',
    ];
    protected $casts = [
        'type' => TransactionTypeEnum::class,
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
}
