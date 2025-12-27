<?php

namespace App\Filament\Resources\OrderPayments\Schemas;

use App\Models\Order;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class OrderPaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Payment Information')
                    ->description('Record payment details for an order')
                    ->schema([
                        Select::make('order_id')
                            ->label('Order')
                            ->options(function () {
                                $business = Auth::user()->business;

                                return Order::query()
                                    ->where('business_id', $business->id)
                                    ->get()
                                    ->mapWithKeys(function ($order) {
                                        return [$order->id => $order->order_number.' - Rp '.number_format($order->total_price, 0, ',', '.').' ('.$order->customer?->name ?? 'Walk-in'.')'];
                                    });
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Select the order for this payment'),

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
                            ->helperText('Method used for this payment'),

                        TextInput::make('amount')
                            ->label('Payment Amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->helperText('Amount paid in this transaction'),

                        TextInput::make('reference_number')
                            ->label('Reference Number')
                            ->placeholder('TRX-123456')
                            ->helperText('Transaction ID or reference number (optional)'),
                    ])
                    ->columns(2),

                Section::make('Payment Status & Notes')
                    ->description('Current status and additional information')
                    ->schema([
                        Select::make('status')
                            ->label('Payment Status')
                            ->required()
                            ->options([
                                'pending' => 'Pending',
                                'completed' => 'Completed',
                                'failed' => 'Failed',
                                'refunded' => 'Refunded',
                            ])
                            ->default('completed')
                            ->native(false)
                            ->helperText('Current status of this payment'),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->placeholder('Additional payment notes or details')
                            ->helperText('Internal notes about this payment (optional)'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
