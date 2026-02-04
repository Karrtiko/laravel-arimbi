<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Schemas\Schema;
use App\Models\Order;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Group::make()
                    ->schema([
                        \Filament\Forms\Components\Section::make('Order Details')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('invoice_number')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->default(fn() => 'INV-' . strtoupper(uniqid())),
                                \Filament\Forms\Components\TextInput::make('customer_name')
                                    ->required(),
                                \Filament\Forms\Components\TextInput::make('customer_phone')
                                    ->tel(),
                                \Filament\Forms\Components\TextInput::make('whatsapp_number')
                                    ->label('WhatsApp')
                                    ->tel(),
                                \Filament\Forms\Components\TextInput::make('total_price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0),
                                \Filament\Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('pending')
                                    ->required(),
                                \Filament\Forms\Components\Textarea::make('notes')
                                    ->columnSpanFull(),
                            ])->columns(2),
                        \Filament\Forms\Components\Section::make('Receiver Information')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('receiver_name'),
                                \Filament\Forms\Components\TextInput::make('receiver_phone')
                                    ->tel(),
                                \Filament\Forms\Components\Textarea::make('receiver_address')
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])->columnSpan(['lg' => 2]),

                \Filament\Forms\Components\Group::make()
                    ->schema([
                        \Filament\Forms\Components\Section::make('Order Items')
                            ->schema([
                                \Filament\Forms\Components\Repeater::make('items')
                                    ->relationship()
                                    ->schema([
                                        \Filament\Forms\Components\Select::make('itemable_type')
                                            ->label('Item Type')
                                            ->options([
                                                \App\Models\Product::class => 'Product',
                                                \App\Models\Bundle::class => 'Bundle',
                                            ])
                                            ->required()
                                            ->live(),
                                        \Filament\Forms\Components\Select::make('itemable_id')
                                            ->label('Select Item')
                                            ->options(function (\Filament\Forms\Get $get) {
                                                $type = $get('itemable_type');
                                                if ($type === \App\Models\Product::class) {
                                                    return \App\Models\Product::pluck('name', 'id');
                                                } elseif ($type === \App\Models\Bundle::class) {
                                                    return \App\Models\Bundle::pluck('name', 'id');
                                                }
                                                return [];
                                            })
                                            ->searchable()
                                            ->required(),
                                        \Filament\Forms\Components\TextInput::make('quantity')
                                            ->numeric()
                                            ->default(1)
                                            ->required(),
                                        \Filament\Forms\Components\TextInput::make('price_at_purchase')
                                            ->label('Price/Unit')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->required(),
                                    ])
                                    ->columns(1)
                                    ->defaultItems(0)
                                    ->addActionLabel('Add Item'),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }
}
