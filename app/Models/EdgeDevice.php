<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Webkul\Support\Models\Company;

class EdgeDevice extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = [
        'public_id',
        'company_id',
        'name',
        'type',
        'status',
        'abilities',
        'token_version',
        'last_seen_at',
        'expires_at',
        'revoked_at',
        'metadata',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $device): void {
            $device->public_id ??= (string) Str::uuid();
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isUsable(): bool
    {
        return $this->status === 'active'
            && $this->revoked_at === null
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    protected function casts(): array
    {
        return [
            'abilities'    => 'array',
            'metadata'     => 'array',
            'last_seen_at' => 'datetime',
            'expires_at'   => 'datetime',
            'revoked_at'   => 'datetime',
        ];
    }
}
