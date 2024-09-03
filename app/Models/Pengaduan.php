<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengaduan extends Model
{
    use HasFactory;

    // created
    // TODO: Ini belum clean code, wajib explore lagi ya!
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (is_null($model->status)) {
                $model->status = \App\Enums\PengaduanStatus::PENDING;
            }
        });
    }

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
