<?php

namespace App\Filament\App\Resources\PengaduanResource\Pages;

use Filament\Actions;
use App\Models\Pengaduan;
use App\Enums\PengaduanStatus;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\App\Resources\PengaduanResource;

class ListPengaduans extends ListRecords
{

    protected static string $resource = PengaduanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $statuses = collect([
            'all' => ['label' => 'All', 'badgeColor' => 'primary', 'status' => null],
            PengaduanStatus::PENDING->name => ['label' => 'Pending', 'badgeColor' => 'warning', 'status' => PengaduanStatus::PENDING],
            PengaduanStatus::COMPLETED->name => ['label' => 'Completed', 'badgeColor' => 'success', 'status' => PengaduanStatus::COMPLETED],
            PengaduanStatus::CANCELLED->name => ['label' => 'Cancelled', 'badgeColor' => 'danger', 'status' => PengaduanStatus::CANCELLED],
        ]);

        return $statuses->mapWithKeys(function ($data, $key) {
            $badgeCount = is_null($data['status'])
                ? Pengaduan::count()
                : Pengaduan::where('status', $data['status'])->count();

            return [$key => Tab::make($data['label'])
                ->badge($badgeCount)
                ->modifyQueryUsing(fn($query) => is_null($data['status']) ? $query : $query->where('status', $data['status']))
                ->badgeColor($data['badgeColor'])];
        })->toArray();
    }
}
