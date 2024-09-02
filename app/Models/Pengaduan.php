<?php

namespace App\Models;

use App\Models\User;
use App\Models\Category;
use App\Models\Tanggapan;
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

    public function tanggapans(): HasMany
    {
        return $this->hasMany(Tanggapan::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
