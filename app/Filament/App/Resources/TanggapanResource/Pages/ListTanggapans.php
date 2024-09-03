<?php

namespace App\Filament\App\Resources\TanggapanResource\Pages;

use Filament\Actions;
use App\Models\Pengaduan;
use App\Models\Tanggapan;
use App\Enums\PengaduanStatus;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\App\Resources\TanggapanResource;



class ListTanggapans extends ListRecords
{
    protected static string $resource = TanggapanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // public function getTabs(): array
    // {
    //     $statuses = collect([
    //         'all' => ['label' => 'All', 'badgeColor' => 'primary', 'status' => null],
    //         PengaduanStatus::PENDING->name => ['label' => 'Pending', 'badgeColor' => 'warning', 'status' => PengaduanStatus::PENDING],
    //         PengaduanStatus::COMPLETED->name => ['label' => 'Completed', 'badgeColor' => 'success', 'status' => PengaduanStatus::COMPLETED],
    //         PengaduanStatus::CANCELLED->name => ['label' => 'Cancelled', 'badgeColor' => 'danger', 'status' => PengaduanStatus::CANCELLED],
    //     ]);

    //     return $statuses->mapWithKeys(function ($data, $key) {
    //         // Menghitung jumlah badge dengan join ke tabel pengaduans dan filter berdasarkan status
    //         $badgeCount = is_null($data['status'])
    //             ? Tanggapan::join('pengaduans', 'tanggapans.pengaduan_id', '=', 'pengaduans.id')->count()
    //             : Tanggapan::join('pengaduans', 'tanggapans.pengaduan_id', '=', 'pengaduans.id')
    //                 ->where('pengaduans.status', $data['status'])->count();

    //         return [$key => Tab::make($data['label'])
    //             ->badge($badgeCount)
    //             ->modifyQueryUsing(function ($query) use ($data) {
    //                 $query->join('pengaduans', 'tanggapans.pengaduan_id', '=', 'pengaduans.id');
    //                 if (!is_null($data['status'])) {
    //                     $query->where('pengaduans.status', $data['status']);
    //                 }
    //                 return $query->orderBy('tanggapans.created_at', 'desc'); // Hanya gunakan satu klausa `orderBy` dengan prefix yang jelas
    //             })
    //             ->badgeColor($data['badgeColor'])];
    //     })->toArray();
    // }


}
