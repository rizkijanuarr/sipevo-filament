<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tanggapan extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => \App\Enums\PengaduanStatus::class,
    ];

    public function markAsCanceled(): void
    {
        $this->status = \App\Enums\PengaduanStatus::CANCELLED;
        $this->save();
    }

    public function markAsComplete(): void
    {
        $this->status = \App\Enums\PengaduanStatus::COMPLETED;
        $this->save();
    }

    public function pengaduan(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Pengaduan::class, 'pengaduan_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
