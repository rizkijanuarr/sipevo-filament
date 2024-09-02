<?php

namespace App\Models;

use App\Enums\PengaduanStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengaduan extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => \App\Enums\PengaduanStatus::class,
    ];

    public function markAsComplete(): void
    {
        $this->status = \App\Enums\PengaduanStatus::COMPLETED;
        $this->save();
    }

    public function byDefaultPending(): void
    {
        $this->status = \App\Enums\PengaduanStatus::PENDING;
        $this->save();
    }

    public function tanggapans(): HasMany
    {
        return $this->hasMany(\App\Models\Tanggapan::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
