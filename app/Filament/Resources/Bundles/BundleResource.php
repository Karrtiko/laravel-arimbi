<?php

namespace App\Filament\Resources\Bundles;

use App\Filament\Resources\Bundles\Pages;
use App\Models\Bundle;
use App\Models\Product;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BundleResource extends Resource
{
    protected static ?string $model = Bundle::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Bundle Details')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(string $operation, $state, callable $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                Forms\Components\TextInput::make('slug')
                                    ->hidden()
                                    ->dehydrated()
                                    ->unique(ignoreRecord: true),
                                Forms\Components\Textarea::make('description')
                                    ->columnSpanFull(),
                            ]),
                        Section::make('Products in Bundle')
                            ->schema([
                                Forms\Components\Repeater::make('bundleProducts')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->label('Product')
                                            ->options(Product::pluck('name', 'id'))
                                            ->required()
                                            ->searchable()
                                            ->distinct()
                                            ->live()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                                        Forms\Components\TextInput::make('quantity')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->live(),
                                        Forms\Components\Placeholder::make('unit_price')
                                            ->label('Harga Satuan')
                                            ->content(function (callable $get) {
                                                $product = Product::find($get('product_id'));
                                                return 'Rp ' . number_format($product?->price ?? 0, 0, ',', '.');
                                            }),
                                        Forms\Components\Placeholder::make('subtotal')
                                            ->label('Subtotal')
                                            ->content(function (callable $get) {
                                                $product = Product::find($get('product_id'));
                                                $qty = $get('quantity') ?? 1;
                                                $subtotal = ($product?->price ?? 0) * $qty;
                                                return 'Rp ' . number_format($subtotal, 0, ',', '.');
                                            }),
                                    ])
                                    ->columns(4)
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Product')
                                    ->live(),
                                Forms\Components\Placeholder::make('total_original_price')
                                    ->label('💰 Total Harga Asli (Sebelum Bundle)')
                                    ->content(function (callable $get) {
                                        $bundleProducts = $get('bundleProducts') ?? [];
                                        $total = 0;
                                        foreach ($bundleProducts as $item) {
                                            $product = Product::find($item['product_id'] ?? null);
                                            $qty = $item['quantity'] ?? 1;
                                            $total += ($product?->price ?? 0) * $qty;
                                        }
                                        return 'Rp ' . number_format($total, 0, ',', '.');
                                    }),
                            ]),
                    ])->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Pricing & Stock')
                            ->schema([
                                Forms\Components\Placeholder::make('price_hint')
                                    ->label('💡 Tips')
                                    ->content('Lihat "Total Harga Asli" di samping untuk referensi. Set harga bundle lebih murah untuk menarik pembeli!')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('price')
                                    ->label('Harga Bundle')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),
                                Forms\Components\TextInput::make('stock')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true),
                            ]),
                        Section::make('Media')
                            ->schema([
                                Forms\Components\Repeater::make('media')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\FileUpload::make('file_path')
                                            ->label('Image')
                                            ->image()
                                            ->disk('public')
                                            ->directory('bundles')
                                            ->visibility('public')
                                            ->required(),
                                        Forms\Components\Toggle::make('is_thumbnail')
                                            ->label('Thumbnail?')
                                            ->inline(false),
                                        Forms\Components\TextInput::make('sort_order')
                                            ->numeric()
                                            ->default(0),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Image')
                                    ->orderColumn('sort_order'),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('Thumbnail')
                    ->getStateUsing(function ($record) {
                        $media = $record->media()->orderBy('sort_order')->first();
                        return $media ? 'storage/' . $media->file_path : null;
                    })
                    ->disk('public')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('products_count')
                    ->counts('products')
                    ->label('Products'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListBundles::route('/'),
            'create' => Pages\CreateBundle::route('/create'),
            'edit' => Pages\EditBundle::route('/{record}/edit'),
        ];
    }
}
