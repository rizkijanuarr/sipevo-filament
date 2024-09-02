<?php

namespace App\Filament\Exports;

use App\Models\Category;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Enums\ExportFormat;

class CategoryExporter extends Exporter
{
    protected static ?string $model = Category::class;

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
            ExportColumn::make('name'),
            ExportColumn::make('slug'),
            ExportColumn::make('created_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your category export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
