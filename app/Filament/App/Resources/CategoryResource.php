<?php

namespace App\Filament\App\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\App\Resources\CategoryResource\Pages;
use App\Filament\App\Resources\CategoryResource\RelationManagers;

class CategoryResource extends Resource
{
    use \App\Traits\HasNavigationBadge;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    protected static ?string $model = Category::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->badge(),
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
                        ->before(function (Category $category) {
                            // Hapus relasi pengaduan
                            foreach ($category->pengaduans as $pengaduan) {
                                // Hapus relasi tanggapan
                                $pengaduan->tanggapans()->delete();
                                $pengaduan->delete();
                            }
                            // Hapus kategori
                            $category->delete();
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
                    ->exporter(\App\Filament\Exports\CategoryExporter::class),

                // Tombol untuk ekspor ke CSV
                Tables\Actions\ExportAction::make('exportCsv')
                    ->label('Export CSV')
                    ->fileDisk('public')
                    ->color('warning')
                    ->icon('heroicon-o-document')
                    ->exporter(\App\Filament\Exports\CategoryExporter::class)
                    // TODO: Belum bisa berjalan dengan baik dengan Jobs Que Monitor!
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        \App\Jobs\UsersCsvExportJob::dispatch($records, 'users.csv');
                        Notification::make()
                            ->title('Export is ready')
                            ->body('Your export is ready. You can download it from the exports page.')
                            ->success()
                            ->seconds(5)
                            ->icon('heroicon-o-hashtag')
                            ->send();
                    }),

                // Tombol untuk ekspor ke PDF
                Tables\Actions\Action::make('print')
                    ->label('Export PDF')
                    ->button()
                    ->icon('heroicon-o-document-text')
                    ->color('danger')
                    ->action(function () {
                        $categories = Category::paginate(10); // Adjust the pagination as needed
                        // dd($categories); // Debugging purpose

                        $pdf = Pdf::loadView('pdf.category.print-category', [
                            'categories' => $categories,
                        ]);

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, 'categories-' . now()->format('Y-m-d_H-i-s') . '.pdf');
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
            'index' => Pages\ManageCategories::route('/'),
        ];
    }
}
