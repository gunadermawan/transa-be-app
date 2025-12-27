<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Customer;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Order Information')
                    ->description('Basic order details')
                    ->schema([
                        TextInput::make('order_number')
                            ->label('Order Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('ORD-001')
                            ->helperText('Unique order number')
                            ->default(fn () => 'ORD-'.strtoupper(substr(uniqid(), -8))),

                        Select::make('outlet_id')
                            ->label('Outlet')
                            ->options(function () {
                                $business = Auth::user()->business;

                                return Outlet::query()
                                    ->where('business_id', $business->id)
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Select outlet for this order'),

                        Select::make('customer_id')
                            ->label('Customer')
                            ->options(function () {
                                $business = Auth::user()->business;

                                return Customer::query()
                                    ->where('business_id', $business->id)
                                    ->get()
                                    ->mapWithKeys(function ($customer) {
                                        return [$customer->id => $customer->name.' ('.$customer->phone.')'];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder('Walk-in Customer')
                            ->helperText('Select customer (optional for walk-in)'),

                        Select::make('cashier_id')
                            ->label('Cashier')
                            ->options(function () {
                                $business = Auth::user()->business;

                                return User::query()
                                    ->where('business_id', $business->id)
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(fn () => Auth::id())
                            ->helperText('Staff who processed this order'),
                    ])
                    ->columns(2),

                Section::make('Order Items')
                    ->description('Add products to this order')
                    ->schema([
                        Repeater::make('items')
                            ->relationship('items')
                            ->live()
                            ->afterStateUpdated(function ($get, $set, $state) {
                                // Calculate totals from items
                                $items = $state ?? [];
                                $totalItems = 0;
                                $subTotal = 0;

                                foreach ($items as $item) {
                                    $quantity = (float) ($item['quantity'] ?? 0);
                                    $total = (float) ($item['total'] ?? 0);

                                    $totalItems += $quantity;
                                    $subTotal += $total;
                                }

                                // Update order summary fields
                                $set('total_items', $totalItems);
                                $set('sub_total', $subTotal);

                                // Calculate total price (sub_total + tax - discount)
                                $tax = (float) ($get('tax') ?? 0);
                                $discount = (float) ($get('discount') ?? 0);
                                $totalPrice = $subTotal + $tax - $discount;

                                $set('total_price', $totalPrice);
                            })
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->options(function () {
                                        $business = Auth::user()->business;

                                        return Product::query()
                                            ->where('business_id', $business->id)
                                            ->get()
                                            ->mapWithKeys(function ($product) {
                                                return [$product->id => $product->name.' (Rp '.number_format($product->price, 0, ',', '.').')'];
                                            });
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function ($set, $state, $get) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            if ($product) {
                                                $set('price', $product->price);
                                                $set('quantity', 1);
                                                $set('total', $product->price);

                                                // Calculate and update summary
                                                $items = $get('../../items') ?? [];
                                                $totalItems = 0;
                                                $subTotal = 0;

                                                foreach ($items as $item) {
                                                    $quantity = (float) ($item['quantity'] ?? 0);
                                                    $total = (float) ($item['total'] ?? 0);
                                                    $totalItems += $quantity;
                                                    $subTotal += $total;
                                                }

                                                $set('../../total_items', $totalItems);
                                                $set('../../sub_total', $subTotal);

                                                $tax = (float) ($get('../../tax') ?? 0);
                                                $discount = (float) ($get('../../discount') ?? 0);
                                                $set('../../total_price', $subTotal + $tax - $discount);
                                            }
                                        }
                                    })
                                    ->reactive()
                                    ->columnSpan(2),

                                TextInput::make('quantity')
                                    ->label('Qty')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($get, $set, $state) {
                                        $quantity = (float) ($state ?: 1);
                                        $price = (float) ($get('price') ?: 0);
                                        $set('total', $quantity * $price);

                                        // Update summary
                                        $items = $get('../../items') ?? [];
                                        $totalItems = 0;
                                        $subTotal = 0;

                                        foreach ($items as $item) {
                                            $qty = (float) ($item['quantity'] ?? 0);
                                            $total = (float) ($item['total'] ?? 0);
                                            $totalItems += $qty;
                                            $subTotal += $total;
                                        }

                                        $set('../../total_items', $totalItems);
                                        $set('../../sub_total', $subTotal);

                                        $tax = (float) ($get('../../tax') ?? 0);
                                        $discount = (float) ($get('../../discount') ?? 0);
                                        $set('../../total_price', $subTotal + $tax - $discount);
                                    })
                                    ->reactive()
                                    ->columnSpan(1),

                                TextInput::make('price')
                                    ->label('Price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($get, $set, $state) {
                                        $price = (float) ($state ?: 0);
                                        $quantity = (float) ($get('quantity') ?: 1);
                                        $set('total', $quantity * $price);

                                        // Update summary
                                        $items = $get('../../items') ?? [];
                                        $totalItems = 0;
                                        $subTotal = 0;

                                        foreach ($items as $item) {
                                            $qty = (float) ($item['quantity'] ?? 0);
                                            $total = (float) ($item['total'] ?? 0);
                                            $totalItems += $qty;
                                            $subTotal += $total;
                                        }

                                        $set('../../total_items', $totalItems);
                                        $set('../../sub_total', $subTotal);

                                        $tax = (float) ($get('../../tax') ?? 0);
                                        $discount = (float) ($get('../../discount') ?? 0);
                                        $set('../../total_price', $subTotal + $tax - $discount);
                                    })
                                    ->reactive()
                                    ->columnSpan(1),

                                TextInput::make('total')
                                    ->label('Total')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->readOnly()
                                    ->default(0)
                                    ->reactive()
                                    ->columnSpan(2),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->addActionLabel('Add Product')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['product_id'] ? Product::find($state['product_id'])?->name : null),
                    ]),

                Section::make('Order Summary')
                    ->description('Price breakdown and totals')
                    ->schema([
                        TextInput::make('total_items')
                            ->label('Total Items')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->helperText('Total quantity of items (auto-calculated)'),

                        TextInput::make('sub_total')
                            ->label('Subtotal')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->readOnly()
                            ->helperText('Subtotal before tax and discount (auto-calculated)'),

                        TextInput::make('tax')
                            ->label('Tax')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($get, $set, $state) {
                                $subTotal = (float) ($get('sub_total') ?? 0);
                                $tax = (float) ($state ?? 0);
                                $discount = (float) ($get('discount') ?? 0);
                                $totalPrice = $subTotal + $tax - $discount;
                                $set('total_price', $totalPrice);
                            })
                            ->helperText('Tax amount'),

                        TextInput::make('discount')
                            ->label('Discount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($get, $set, $state) {
                                $subTotal = (float) ($get('sub_total') ?? 0);
                                $tax = (float) ($get('tax') ?? 0);
                                $discount = (float) ($state ?? 0);
                                $totalPrice = $subTotal + $tax - $discount;
                                $set('total_price', $totalPrice);
                            })
                            ->helperText('Discount amount'),

                        TextInput::make('total_price')
                            ->label('Total Price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->readOnly()
                            ->helperText('Final total price (auto-calculated)'),
                    ])
                    ->columns(2),

                Section::make('Payment Information')
                    ->description('Payment details and status')
                    ->schema([
                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->required()
                            ->options([
                                'cash' => 'Cash',
                                'debit_card' => 'Debit Card',
                                'credit_card' => 'Credit Card',
                                'e_wallet' => 'E-Wallet',
                                'bank_transfer' => 'Bank Transfer',
                                'qris' => 'QRIS',
                            ])
                            ->default('cash')
                            ->native(false)
                            ->helperText('How customer paid'),

                        Select::make('payment_status')
                            ->label('Payment Status')
                            ->required()
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Paid',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->default('pending')
                            ->native(false)
                            ->live()
                            ->helperText('Current payment status'),

                        TextInput::make('cash_received')
                            ->label('Cash Received')
                            ->numeric()
                            ->prefix('Rp')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($get, $set, $state) {
                                $cashReceived = (float) ($state ?? 0);
                                $totalPrice = (float) ($get('total_price') ?? 0);
                                $change = $cashReceived - $totalPrice;
                                $set('change', $change > 0 ? $change : 0);
                            })
                            ->helperText('Amount received from customer (for cash)'),

                        TextInput::make('change')
                            ->label('Change')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->readOnly()
                            ->helperText('Change returned to customer (auto-calculated)'),
                    ])
                    ->columns(2),

                Section::make('Order Status & Notes')
                    ->description('Order fulfillment status and additional notes')
                    ->schema([
                        Select::make('status')
                            ->label('Order Status')
                            ->required()
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('pending')
                            ->native(false)
                            ->helperText('Current order status'),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->placeholder('Additional notes about this order')
                            ->helperText('Internal notes (optional)'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
