<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\Pages;
use App\Models\InvoiceSetting;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceSettingResource extends Resource
{
    protected static ?string $model = InvoiceSetting::class;

    protected static ?string $navigationLabel = 'Pengaturan Invoice';

    protected static ?int $navigationSort = 101;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Edit Pengaturan')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Key')
                            ->disabled(),
                        Forms\Components\TextInput::make('label')
                            ->label('Label')
                            ->required(),
                        Forms\Components\Textarea::make('value')
                            ->label('Nilai')
                            ->rows(3)
                            ->helperText('Isi sesuai kebutuhan'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Pengaturan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('key')
                    ->label('Key')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('value')
                    ->label('Nilai')
                    ->limit(50),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'color' => 'info',
                        'textarea' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('sort_order')
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoiceSettings::route('/'),
            'edit' => Pages\EditInvoiceSetting::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
