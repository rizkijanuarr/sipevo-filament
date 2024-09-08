<?php

namespace App\Filament\App\Resources\StatsOverviewResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $createdFrom = $this->tableFilters['created_at']['created_from'] ?? null;
        $createdTo = $this->tableFilters['created_at']['created_until'] ?? null;

        $pengaduanCompletedCount = \App\Models\Pengaduan::where('status', \App\Enums\PengaduanStatus::COMPLETED)
            ->when($createdFrom && $createdTo, fn($query) => $query->whereDate('created_at', '>=', $createdFrom)->whereDate('created_at', '<=', $createdTo))
            ->count();

        $pengaduanPendingCount = \App\Models\Pengaduan::where('status', \App\Enums\PengaduanStatus::PENDING)
            ->when($createdFrom && $createdTo, fn($query) => $query->whereDate('created_at', '>=', $createdFrom)->whereDate('created_at', '<=', $createdTo))
            ->count();

        $pengaduanCanceledCount = \App\Models\Pengaduan::where('status', \App\Enums\PengaduanStatus::CANCELLED)
            ->when($createdFrom && $createdTo, fn($query) => $query->whereDate('created_at', '>=', $createdFrom)->whereDate('created_at', '<=', $createdTo))
            ->count();

        return [
            Stat::make('Total Users', \App\Models\User::count()),
            Stat::make('Total Pengaduan', \App\Models\Pengaduan::count()),
            Stat::make('Total Tanggapan', \App\Models\Tanggapan::count()),
            Stat::make('Pengaduan Baru', $pengaduanPendingCount),
            Stat::make('Pengaduan Terselesaikan', $pengaduanCompletedCount),
            Stat::make('Pengaduan Gagal', $pengaduanCanceledCount),
        ];
    }
}
