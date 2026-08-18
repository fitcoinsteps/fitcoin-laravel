<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FitcoinTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'steps_converted',
        'fitcoins_earned',
        'conversion_date',
    ];

    protected $casts = [
        'conversion_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}