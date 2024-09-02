<?php

namespace App\Filament\Exports;

use App\Models\Pengaduan;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Enums\ExportFormat;

class PengaduanExporter extends Exporter
{
    protected static ?string $model = Pengaduan::class;

    public function getFormats(): array
    {
        return [
            ExportFormat::Xlsx,
            ExportFormat::Csv,
        ];
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('category.name'),
            ExportColumn::make('user.name'),
            ExportColumn::make('title'),
            ExportColumn::make('description'),
            ExportColumn::make('location'),
            ExportColumn::make('status'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your pengaduan export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
