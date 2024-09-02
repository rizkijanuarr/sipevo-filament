<?php

namespace App\Filament\App\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Pengaduan;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\App\Resources\PengaduanResource\Pages;
use App\Filament\App\Resources\PengaduanResource\RelationManagers;

class PengaduanResource extends Resource
{
    use \App\Traits\HasNavigationBadge;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $model = Pengaduan::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->image(),
                Forms\Components\TextInput::make('status')
                    ->maxLength(255)
                    ->afterStateUpdated(function ($state, $component, $form) {
                        $record = $form->getRecord();
                        $record->byDefaultPending();

                        // Memperbarui state field secara eksplisit
                        $form->state['status'] = \App\Enums\PengaduanStatus::PENDING;
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                ->circular(),
                Tables\Columns\TextColumn::make('category.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state) => $state->getColor()),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->color('gray'),
                    Tables\Actions\EditAction::make()
                        ->color('gray'),
                    Tables\Actions\Action::make('edit-transaction')
                        ->visible(fn(Pengaduan $record) => $record->status === \App\Enums\PengaduanStatus::PENDING)
                        ->label('Edit Transaction')
                        ->icon('heroicon-o-pencil'),
                    Tables\Actions\Action::make('mark-as-complete')
                        ->visible(fn(Pengaduan $record) => $record->status === \App\Enums\PengaduanStatus::PENDING)
                        ->requiresConfirmation()
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn(Pengaduan $record) => $record->markAsComplete())
                        ->label('Mark as Complete'),
                    Tables\Actions\Action::make('divider')->label('')->disabled(),
                    Tables\Actions\DeleteAction::make()
                        ->before(function (Pengaduan $pengaduan) {
                            // Hapus tanggapan
                            $pengaduan->tanggapans()->delete();
                            // Hapus pengaduan
                            $pengaduan->delete();
                        }),
                ])
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                // Tombol untuk ekspor ke Excel
                Tables\Actions\ExportAction::make()
                    ->label('Export Excel')
                    ->fileDisk('public')
                    ->color('success')
                    ->icon('heroicon-o-document-text')
                    ->exporter(\App\Filament\Exports\PengaduanExporter::class),

                // Tombol untuk ekspor ke CSV
                Tables\Actions\ExportAction::make('exportCsv')
                    ->label('Export CSV')
                    ->fileDisk('public')
                    ->color('warning')
                    ->icon('heroicon-o-document')
                    ->exporter(\App\Filament\Exports\PengaduanExporter::class),

                // Tombol untuk ekspor ke PDF
                Tables\Actions\Action::make('print')
                    ->label('Export PDF')
                    ->button()
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->action(function () {
                        $pengaduans = Pengaduan::paginate(10); // Adjust the pagination as needed
                        // dd($pengaduan); // Debugging purpose

                        $pdf = Pdf::loadView('pdf.pengaduan.print-pengaduan', [
                            'pengaduans' => $pengaduans,
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'pengaduan-' . now()->format('Y-m-d_H-i-s') . '.pdf');
                    }),
                    // Tables\Actions\ImportAction::make()
                    //     ->label('Import Post')
                    //     ->color('info')
                    //     ->button()
                    //     ->icon('heroicon-o-document-arrow-down')
                    //     ->importer(PostImporter::class),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePengaduans::route('/'),
        ];
    }
}
