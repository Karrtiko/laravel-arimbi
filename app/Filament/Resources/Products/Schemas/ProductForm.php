<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Group::make()
                    ->schema([
                        \Filament\Forms\Components\Section::make('General Information')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(string $operation, $state, \Filament\Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                                \Filament\Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->hidden()
                                    ->dehydrated()
                                    ->unique(ignoreRecord: true),
                                \Filament\Forms\Components\Select::make('category_id')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                \Filament\Forms\Components\Select::make('country_id')
                                    ->relationship('country', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->label('Origin Country'),
                                \Filament\Forms\Components\Textarea::make('description')
                                    ->columnSpanFull(),
                            ]),
                        \Filament\Forms\Components\Section::make('Pricing & Inventory')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->step(0.01),
                                \Filament\Forms\Components\TextInput::make('stock')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                \Filament\Forms\Components\Toggle::make('is_active')
                                    ->default(true),
                            ])->columns(2),
                    ])->columnSpan(['lg' => 2]),

                \Filament\Forms\Components\Group::make()
                    ->schema([
                        \Filament\Forms\Components\Section::make('Attributes')
                            ->schema([
                                \Filament\Forms\Components\KeyValue::make('attributes')
                                    ->label('Product Specs')
                                    ->keyLabel('Property')
                                    ->valueLabel('Value'),
                            ]),
                        \Filament\Forms\Components\Section::make('Media')
                            ->schema([
                                \Filament\Forms\Components\Repeater::make('media')
                                    ->relationship()
                                    ->schema([
                                        \Filament\Forms\Components\FileUpload::make('file_path')
                                            ->image()
                                            ->disk('public')
                                            ->directory('products')
                                            ->required(),
                                        \Filament\Forms\Components\Toggle::make('is_thumbnail')
                                            ->inline(false),
                                        \Filament\Forms\Components\TextInput::make('sort_order')
                                            ->numeric()
                                            ->default(0),
                                    ])
                                    ->orderColumn('sort_order')
                                    ->collapseAllAction(fn($action) => $action->label('Collapse All'))
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }
}
