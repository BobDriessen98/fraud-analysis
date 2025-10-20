<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FraudReason extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
    ];

    public $timestamps = false;

    public function customers(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Customer::class,
                'customer_fraud_reasons',
                'fraud_reason_id',
                'customer_id')
            ->withPivot('context');
    }
}
