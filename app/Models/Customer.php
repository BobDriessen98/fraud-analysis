<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'bsn',
        'first_name',
        'last_name',
        'date_of_birth',
        'phone_number',
        'email',
        'tag',
        'address_street',
        'address_postcode',
        'address_city',
        'ip_address',
        'iban',
        'last_invoice_date',
        'last_login_date_time',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'last_invoice_date' => 'date',
        'last_login_date_time' => 'datetime',
    ];

    public function scans(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                FraudReason::class,
                'customer_scans',
                'customer_id',
                'scan_id');
    }

    public function fraudReasons(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                FraudReason::class,
                'customer_fraud_reasons',
                'customer_id',
                'fraud_reason_id')
            ->withPivot('context');
    }
}
