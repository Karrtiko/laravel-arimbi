<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\Pages;
use App\Models\MessageTemplate;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MessageTemplateResource extends Resource
{
    protected static ?string $model = MessageTemplate::class;

    protected static ?string $navigationLabel = 'Template Pesan WA';

    protected static ?int $navigationSort = 100;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Template Pesan')
                    ->schema([
                        Forms\Components\TextInput::make('status_key')
                            ->label('Status Key')
                            ->disabled()
                            ->helperText('Tidak bisa diubah'),
                        Forms\Components\TextInput::make('status_label')
                            ->label('Nama Status')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->helperText('Catatan untuk admin tentang template ini'),
                        Forms\Components\Textarea::make('template')
                            ->label('Template Pesan')
                            ->required()
                            ->rows(15)
                            ->helperText('Placeholder yang tersedia: [Nama], [NomorOrder], [Total], [NomorResi], [ListBarang]')
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('placeholder_help')
                            ->label('📝 Panduan Placeholder')
                            ->content(<<<HTML
                                <div class="text-sm space-y-1">
                                    <p><strong>[Nama]</strong> = Nama customer</p>
                                    <p><strong>[NomorOrder]</strong> = Nomor invoice</p>
                                    <p><strong>[Total]</strong> = Total harga (Rp x.xxx.xxx)</p>
                                    <p><strong>[NomorResi]</strong> = Nomor resi pengiriman</p>
                                    <p><strong>[ListBarang]</strong> = Daftar barang pesanan</p>
                                </div>
                            HTML)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status_key')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'processing' => 'warning',
                        'shipped' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status_label')
                    ->label('Nama Status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('template')
                    ->label('Preview')
                    ->limit(80),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessageTemplates::route('/'),
            'edit' => Pages\EditMessageTemplate::route('/{record}/edit'),
        ];
    }

    // Disable create - templates are seeded
    public static function canCreate(): bool
    {
        return false;
    }
}
