<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Scan extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function customers(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Customer::class,
                'customer_scans',
                'scan_id',
                'customer_id');
    }
}
