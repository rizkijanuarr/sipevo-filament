<?php

namespace App\Filament\App\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Tanggapan;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\App\Resources\TanggapanResource\Pages;
use App\Filament\App\Resources\TanggapanResource\RelationManagers;

class TanggapanResource extends Resource
{
    use \App\Traits\HasNavigationBadge;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $model = Tanggapan::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pengaduan_id')
                    ->relationship('pengaduan', 'title'),
                Forms\Components\Textarea::make('comment')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->image(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('pengaduan.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image'),
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
                    Tables\Actions\Action::make('divider')->label('')->disabled(),
                    Tables\Actions\DeleteAction::make()
                        ->before(function (Tanggapan $tanggapan) {
                            $tanggapan->delete();
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
                    ->exporter(\App\Filament\Exports\TanggapanExporter::class),

                // Tombol untuk ekspor ke CSV
                Tables\Actions\ExportAction::make('exportCsv')
                    ->label('Export CSV')
                    ->fileDisk('public')
                    ->color('warning')
                    ->icon('heroicon-o-document')
                    ->exporter(\App\Filament\Exports\TanggapanExporter::class),

                // Tombol untuk ekspor ke PDF
                Tables\Actions\Action::make('print')
                    ->label('Export PDF')
                    ->button()
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->action(function () {
                        $tanggapans = Tanggapan::paginate(10); // Adjust the pagination as needed
                        // dd($tanggapans); // Debugging purpose

                        $pdf = Pdf::loadView('pdf.tanggapan.print-tanggapan', [
                            'tanggapans' => $tanggapans,
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'tanggapan-' . now()->format('Y-m-d_H-i-s') . '.pdf');
                    }),
                //     Tables\Actions\ImportAction::make()
                //         ->label('Import Post')
                //         ->color('info')
                //         ->button()
                //         ->icon('heroicon-o-document-arrow-down')
                //         ->importer(PostImporter::class),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTanggapans::route('/'),
        ];
    }
}
