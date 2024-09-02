<?php

namespace App\Filament\App\Resources\TanggapanResource\Pages;

use App\Filament\App\Resources\TanggapanResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTanggapans extends ManageRecords
{
    protected static string $resource = TanggapanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
