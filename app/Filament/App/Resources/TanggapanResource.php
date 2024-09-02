<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\TanggapanResource\Pages;
use App\Filament\App\Resources\TanggapanResource\RelationManagers;
use App\Models\Tanggapan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name'),
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
            ->columns([
                Tables\Columns\TextColumn::make('pengaduan.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
                // Tables\Actions\Action::make('print')
                //     ->label('Export PDF')
                //     ->button()
                //     ->icon('heroicon-o-document-text')
                //     ->color('danger')
                //     ->action(function () {
                //         $posts = Post::paginate(10); // Adjust the pagination as needed
                //         // dd($posts); // Debugging purpose

                //         $pdf = Pdf::loadView('pdf.print-post', [
                //             'posts' => $posts,
                //         ]);

                //         return response()->streamDownload(function () use ($pdf) {
                //             echo $pdf->stream();
                //         }, 'posts-' . now()->format('Y-m-d_H-i-s') . '.pdf');
                //     }),
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
