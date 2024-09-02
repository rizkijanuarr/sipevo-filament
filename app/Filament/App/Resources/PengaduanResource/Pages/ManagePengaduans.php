<?php

namespace App\Filament\App\Resources\PengaduanResource\Pages;

use App\Filament\App\Resources\PengaduanResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePengaduans extends ManageRecords
{
    protected static string $resource = PengaduanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
