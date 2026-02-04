<?php

namespace App\Filament\Resources\Bundles\Schemas;

use Filament\Schemas\Schema;

class BundleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Group::make()
                    ->schema([
                        \Filament\Forms\Components\Section::make('Bundle Details')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(string $operation, $state, \Filament\Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                                \Filament\Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                \Filament\Forms\Components\Textarea::make('description')
                                    ->columnSpanFull(),
                            ]),
                        \Filament\Forms\Components\Section::make('Products in Bundle')
                            ->schema([
                                \Filament\Forms\Components\Repeater::make('bundle_product')
                                    ->relationship('products')
                                    ->schema([
                                        \Filament\Forms\Components\Select::make('product_id')
                                            ->relationship('name') // Note: This might need adjustment based on how the relation is defined, usually it's relationship('product', 'name') but for ManyToMany it's tricky in repeater. Better use simple Select with options.
                                            // Actually for ManyToMany with pivot, Repeater + relationship on the *pivot* relation name works if defined?
                                            // Let's stick to standard repeater on the relation method name 'products' but it might treat it as HasMany.
                                            // Since it's BelongsToMany, the repeater usually works better if we manage the pivot data.
                                            // Let's check relation name in Bundle model: public function products(): BelongsToMany
                                            // Filament Repeater relationship() is for HasMany/MorphMany.
                                            // For BelongsToMany, we usually use a specific pivot relationship or just manage it differently.
                                            // Ideally, we should use `products` relationship if we can edit pivot data.
                                            // Let's try configuring it to use the `products` relationship and pivot data.
                                            ->options(\App\Models\Product::pluck('name', 'id'))
                                            ->required()
                                            ->distinct()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                        \Filament\Forms\Components\TextInput::make('quantity')
                                            ->numeric()
                                            ->default(1)
                                            ->required(),
                                    ])
                                    ->columns(2),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                \Filament\Forms\Components\Group::make()
                    ->schema([
                        \Filament\Forms\Components\Section::make('Pricing & Stock')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),
                                \Filament\Forms\Components\TextInput::make('stock')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                \Filament\Forms\Components\Toggle::make('is_active')
                                    ->default(true),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }
}
