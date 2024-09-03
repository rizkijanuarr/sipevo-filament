<?php

namespace App\Filament\App\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Pengaduan;
use App\Models\Tanggapan;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Resources\Resource;
use Forms\Components\MarkdownEditor;
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
            ->schema(static::getFormSchema());
    }

    protected static function getFormSchema(): array
    {
        return [
            Forms\Components\Placeholder::make('note!')
                ->content('Jika Anda bertanya pengaduan mana yang statusnya belum diselesaikan, tenang saja. Karena semua data yang ada dalam form berikut adalah data yang memang sengaja statusnya masih pending.')
                ->columnSpan(2),
            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\Select::make('pengaduan_id')
                        ->label('Pengaduan')
                        ->options(function () {
                            return \App\Models\Pengaduan::where('status', 'pending')
                                ->orWhere(function ($query) {
                                    $query->where('status', '!=', 'completed')
                                        ->where('status', '!=', 'cancelled');
                                })
                                ->orderBy('created_at', 'desc')
                                ->get()
                                ->pluck('title', 'id');
                        })
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            \Log::info('Selected Pengaduan ID:', ['id' => $state]);
                            $pengaduan = \App\Models\Pengaduan::find($state);
                            if ($pengaduan) {
                                $set('pengaduan_title', $pengaduan->title);
                                $set('pengaduan_description', $pengaduan->description);
                                $set('pengaduan_location', $pengaduan->location);
                                $set('pengaduan_image', $pengaduan->image ? [$pengaduan->image] : null);
                                $set('pengaduan_status', $pengaduan->status->value);
                                \Log::info('Pengaduan data:', $pengaduan->toArray());
                            } else {
                                $set('pengaduan_title', null);
                                $set('pengaduan_description', null);
                                $set('pengaduan_location', null);
                                $set('pengaduan_image', null);
                                $set('pengaduan_status', null);
                                \Log::warning('Pengaduan not found for ID:', ['id' => $state]);
                            }
                        })
                        ->disabled(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord) // Disable in edit context
                        ->columnSpan(1),

                    Forms\Components\Textarea::make('comment')
                        ->required()
                        ->columnSpan(1),
                ]),
            Forms\Components\Grid::make(2) 
                ->schema([
                    Forms\Components\FileUpload::make('image')
                        ->image()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('pengaduan_title')
                        ->label('Title')
                        ->disabled()
                        ->visible(fn($get) => $get('pengaduan_id') !== null)
                        ->columnSpan(1),
                ]),
            Forms\Components\Grid::make(2) 
                ->schema([
                    Forms\Components\Textarea::make('pengaduan_description')
                        ->label('Description')
                        ->disabled()
                        ->visible(fn($get) => $get('pengaduan_id') !== null)
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('pengaduan_location')
                        ->label('Location')
                        ->disabled()
                        ->visible(fn($get) => $get('pengaduan_id') !== null)
                        ->columnSpan(1),
                ]),
            Forms\Components\Grid::make(2)
                ->schema([
                    Forms\Components\FileUpload::make('pengaduan_image')
                        ->image()
                        ->disabled()
                        ->visible(fn($get) => $get('pengaduan_id') !== null)
                        ->columnSpan(1),
                    Forms\Components\Select::make('pengaduan_status')
                        ->label('Status')
                        ->options(function () {
                            $cases = \App\Enums\PengaduanStatus::cases();
                            return array_combine(
                                array_map(fn($case) => $case->value, $cases),
                                array_map(fn($case) => $case->getLabel(), $cases)
                            );
                        })
                        ->visible(fn($get) => $get('pengaduan_id') !== null)
                        ->reactive()
                        ->afterStateUpdated(function ($state, $get, $set) {
                            $pengaduanId = $get('pengaduan_id');
                            if ($pengaduanId) {
                                $pengaduan = \App\Models\Pengaduan::find($pengaduanId);
                                if ($pengaduan) {
                                    $pengaduan->update(['status' => $state]);
                                }
                            }
                        })
                        ->dehydrated(false) // Prevent saving to tanggapans table
                        ->columnSpan(1),
                ]),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar Tanggapan')
                    ->circular(),
                Tables\Columns\TextColumn::make('pengaduan.title')
                    ->label('Nama Pengaduan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pengaduan.status')
                    ->label('Status Pengaduan')
                    ->badge()
                    ->color(fn($state) => $state->getColor()),
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
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil')
                        // TODO: Fix this
                        ->record(function (Tanggapan $record) {
                            // Load related pengaduan data
                            $record->load(['pengaduan' => function ($query) {
                                $query->select('id', 'title', 'description', 'location', 'image', 'status');
                            }]);
                            
                            // Return the record with relevant fields
                            return $record->select('id', 'comment', 'image')->first();
                        })
                        ->form(static::getFormSchema())
                        ->color('gray')
                        ->disabled(fn($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),
                    Tables\Actions\Action::make('mark-as-canceled')
                        ->visible(fn(Tanggapan $record) => $record->pengaduan && $record->pengaduan->status === \App\Enums\PengaduanStatus::PENDING)
                        ->requiresConfirmation()
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (Tanggapan $record) {
                            $pengaduan = $record->pengaduan; 
                            if ($pengaduan) {
                                $pengaduan->update(['status' => \App\Enums\PengaduanStatus::CANCELLED]);
                            }
                        })
                        ->label('Mark as Cancel'),
                    Tables\Actions\Action::make('mark-as-complete')
                        ->visible(fn(Tanggapan $record) => $record->pengaduan->status === \App\Enums\PengaduanStatus::PENDING)
                        ->requiresConfirmation()
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Tanggapan $record) {
                            $pengaduan = $record->pengaduan; 
                            if ($pengaduan) {
                                $pengaduan->update(['status' => \App\Enums\PengaduanStatus::COMPLETED]);
                            }
                        })
                        ->label('Mark as Complete'),
                    Tables\Actions\DeleteAction::make()
                        ->color('danger')
                        ->before(function (Tanggapan $tanggapan) {
                            $tanggapan->delete();
                        }),
                ])
                    ->color('gray'),
                
                    
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
                        $tanggapans = Tanggapan::paginate(10); 
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
