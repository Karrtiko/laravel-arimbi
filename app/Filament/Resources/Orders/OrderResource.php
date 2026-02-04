<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages;
use App\Models\Order;
use App\Models\Product;
use App\Models\Bundle;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationLabel = 'Transactions';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make('Customer Information')
                            ->schema([
                                Forms\Components\TextInput::make('invoice_number')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->default(function () {
                                        $today = now();
                                        $prefix = 'INV-' . $today->format('dmy');

                                        // Count orders today + 1
                                        $todayCount = Order::whereDate('created_at', $today->toDateString())->count();
                                        $sequence = str_pad($todayCount + 1, 3, '0', STR_PAD_LEFT);

                                        return $prefix . $sequence;
                                    })
                                    ->helperText('Format: INV-DDMMYY### (otomatis, bisa diubah)'),
                                Forms\Components\TextInput::make('customer_name')
                                    ->required(),
                                Forms\Components\TextInput::make('customer_phone')
                                    ->tel(),
                                Forms\Components\TextInput::make('whatsapp_number')
                                    ->label('WhatsApp')
                                    ->tel(),
                            ])->columns(2),
                        Section::make('Receiver Information')
                            ->schema([
                                Forms\Components\TextInput::make('receiver_name'),
                                Forms\Components\TextInput::make('receiver_phone')
                                    ->tel(),
                                Forms\Components\Textarea::make('receiver_address')
                                    ->columnSpanFull(),
                            ])->columns(2),
                        Section::make('Status & Notes')
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'shipped' => 'Shipped (Dikirim)',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('pending')
                                    ->required()
                                    ->live(),
                                Forms\Components\TextInput::make('tracking_number')
                                    ->label('Nomor Resi')
                                    ->placeholder('Contoh: JNE123456789')
                                    ->visible(fn(callable $get) => $get('status') === 'shipped'),
                                Forms\Components\Textarea::make('notes')
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make('Order Items')
                            ->schema([
                                Forms\Components\Repeater::make('items')
                                    ->relationship()
                                    ->schema([
                                        Forms\Components\Select::make('itemable_type')
                                            ->label('Jenis Item')
                                            ->options([
                                                Product::class => 'Product',
                                                Bundle::class => 'Bundle',
                                            ])
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(fn(callable $set) => $set('itemable_id', null)),
                                        Forms\Components\Select::make('itemable_id')
                                            ->label('Pilih Item')
                                            ->options(function (callable $get) {
                                                $type = $get('itemable_type');
                                                if ($type === Product::class) {
                                                    return Product::pluck('name', 'id');
                                                } elseif ($type === Bundle::class) {
                                                    return Bundle::pluck('name', 'id');
                                                }
                                                return [];
                                            })
                                            ->searchable()
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function (callable $get, callable $set, $state) {
                                                // Auto-fill price when item is selected
                                                $type = $get('itemable_type');
                                                if ($type === Product::class) {
                                                    $item = Product::find($state);
                                                } else {
                                                    $item = Bundle::find($state);
                                                }
                                                if ($item) {
                                                    $set('price_at_purchase', $item->price);
                                                }
                                            }),
                                        Forms\Components\Placeholder::make('item_price_info')
                                            ->label('Harga Asli')
                                            ->content(function (callable $get) {
                                                $type = $get('itemable_type');
                                                $id = $get('itemable_id');
                                                if (!$type || !$id)
                                                    return '-';

                                                if ($type === Product::class) {
                                                    $item = Product::find($id);
                                                } else {
                                                    $item = Bundle::find($id);
                                                }
                                                return 'Rp ' . number_format($item?->price ?? 0, 0, ',', '.');
                                            }),
                                        Forms\Components\TextInput::make('quantity')
                                            ->label('Qty')
                                            ->numeric()
                                            ->default(1)
                                            ->required()
                                            ->live()
                                            ->minValue(1),
                                        Forms\Components\TextInput::make('price_at_purchase')
                                            ->label('Harga/Unit (bisa diubah)')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->required()
                                            ->live(),
                                        Forms\Components\Placeholder::make('subtotal_info')
                                            ->label('Subtotal')
                                            ->content(function (callable $get) {
                                                $price = (float) ($get('price_at_purchase') ?? 0);
                                                $qty = (int) ($get('quantity') ?? 1);
                                                return 'Rp ' . number_format($price * $qty, 0, ',', '.');
                                            }),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addActionLabel('+ Tambah Item')
                                    ->live()
                                    ->afterStateUpdated(function (callable $get, callable $set) {
                                        // Auto-calculate total price (items + shipping)
                                        $items = $get('items') ?? [];
                                        $subtotal = 0;
                                        foreach ($items as $item) {
                                            $price = (float) ($item['price_at_purchase'] ?? 0);
                                            $qty = (int) ($item['quantity'] ?? 1);
                                            $subtotal += $price * $qty;
                                        }
                                        $shipping = (float) ($get('shipping_cost') ?? 0);
                                        $set('total_price', $subtotal + $shipping);
                                    }),
                                Forms\Components\Placeholder::make('calculated_total')
                                    ->label('💰 Subtotal Items')
                                    ->content(function (callable $get) {
                                        $items = $get('items') ?? [];
                                        $total = 0;
                                        foreach ($items as $item) {
                                            $price = (float) ($item['price_at_purchase'] ?? 0);
                                            $qty = (int) ($item['quantity'] ?? 1);
                                            $total += $price * $qty;
                                        }
                                        return 'Rp ' . number_format($total, 0, ',', '.');
                                    }),
                                Forms\Components\TextInput::make('shipping_cost')
                                    ->label('Ongkos Kirim')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->live()
                                    ->afterStateUpdated(function (callable $get, callable $set) {
                                        // Recalculate total when shipping changes
                                        $items = $get('items') ?? [];
                                        $subtotal = 0;
                                        foreach ($items as $item) {
                                            $price = (float) ($item['price_at_purchase'] ?? 0);
                                            $qty = (int) ($item['quantity'] ?? 1);
                                            $subtotal += $price * $qty;
                                        }
                                        $shipping = (float) ($get('shipping_cost') ?? 0);
                                        $set('total_price', $subtotal + $shipping);
                                    }),
                                Forms\Components\TextInput::make('total_price')
                                    ->label('TOTAL')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->default(0)
                                    ->helperText('Subtotal + Ongkir (bisa diubah manual)'),
                            ]),
                    ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('whatsapp_number')
                    ->label('WhatsApp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn($state): string => match ($state?->value ?? $state) {
                        'pending' => 'gray',
                        'processing' => 'warning',
                        'shipped' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('tracking_number')
                    ->label('No. Resi')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
                \Filament\Actions\Action::make('printInvoice')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn(Order $record) => route('invoice.show', $record))
                    ->openUrlInNewTab(),
                \Filament\Actions\Action::make('whatsapp')
                    ->label('Chat WA')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->url(function (Order $record) {
                        $statusKey = $record->status?->value ?? $record->status ?? 'pending';
                        $template = \App\Models\MessageTemplate::getByStatus($statusKey);

                        if ($template) {
                            $message = $template->parseForOrder($record);
                        } else {
                            $message = "Halo {$record->customer_name}, pesanan kamu sedang kami proses.";
                        }

                        $encodedMessage = urlencode($message);
                        return "https://wa.me/{$record->whatsapp_number}?text={$encodedMessage}";
                    })
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                    \Filament\Actions\BulkAction::make('printBulk')
                        ->label('Print Invoice')
                        ->icon('heroicon-o-printer')
                        ->action(function (\Illuminate\Support\Collection $records) {
                            $ids = $records->pluck('id')->join(',');
                            return redirect()->to(route('invoice.bulk', ['ids' => $ids]));
                        })
                        ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
