<?php

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\Pages;
use App\Models\GeneralSetting;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GeneralSettingResource extends Resource
{
    protected static ?string $model = GeneralSetting::class;

    protected static ?string $navigationLabel = 'Pengaturan Umum';

    protected static ?int $navigationSort = 100;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

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
                        Forms\Components\TextInput::make('group')
                            ->label('Group')
                            ->disabled(),

                        // Dynamic Value Fields
                        Forms\Components\TextInput::make('value')
                            ->label('Nilai')
                            ->hidden(fn($record) => $record?->type !== 'text' && $record?->type !== 'number')
                            ->required(fn($record) => $record?->type === 'text' || $record?->type === 'number'),

                        Forms\Components\Textarea::make('value')
                            ->label('Nilai')
                            ->rows(3)
                            ->hidden(fn($record) => $record?->type !== 'textarea')
                            ->required(fn($record) => $record?->type === 'textarea')
                            ->helperText('Isi sesuai kebutuhan'),

                        Forms\Components\FileUpload::make('value')
                            ->label('Upload Gambar')
                            ->image()
                            ->disk('public')
                            ->directory('settings')
                            ->hidden(fn($record) => $record?->type !== 'image')
                            ->downloadable()
                            ->openable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('group')
                    ->label('Group')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'contact' => 'success',
                        'stock' => 'warning',
                        'display' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->label('Pengaturan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Nilai')
                    ->limit(50),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'contact' => 'Contact',
                        'stock' => 'Stock',
                        'display' => 'Display',
                    ]),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGeneralSettings::route('/'),
            'edit' => Pages\EditGeneralSetting::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
