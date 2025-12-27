<?php

namespace App\Filament\Resources\PurchaseOrders\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PurchaseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('business_id')
                    ->default(fn () => auth()->user()?->business_id),

                Hidden::make('created_by')
                    ->default(fn () => auth()->user()?->id),

                Section::make('Informasi Purchase Order')
                    ->schema([
                        TextInput::make('po_number')
                            ->label('Nomor PO')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->default(fn () => 'PO-'.date('Ymd').'-'.strtoupper(Str::random(4)))
                            ->placeholder('PO-20250101-ABCD')
                            ->helperText('Nomor unik purchase order')
                            ->alphaDash(),

                        TextInput::make('reference')
                            ->label('Referensi/Invoice Supplier')
                            ->maxLength(100)
                            ->placeholder('INV-SUP-001')
                            ->helperText('Nomor invoice atau referensi dari supplier (opsional)'),

                        DatePicker::make('order_date')
                            ->label('Tanggal Order')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->helperText('Tanggal pembuatan purchase order'),

                        Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'draft' => 'Draft',
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'received' => 'Received',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('approved')
                            ->native(false)
                            ->helperText('Status purchase order'),
                    ])
                    ->columns(2),

                Section::make('Supplier & Outlet')
                    ->schema([
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->required()
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih supplier')
                            ->helperText('Supplier tempat order barang')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->label('Nama Supplier'),
                                TextInput::make('code')
                                    ->label('Kode')
                                    ->default(fn () => 'SUP-'.strtoupper(Str::random(6))),
                                TextInput::make('phone')
                                    ->tel()
                                    ->label('Telepon'),
                                TextInput::make('email')
                                    ->email()
                                    ->label('Email'),
                            ]),

                        Select::make('outlet_id')
                            ->label('Outlet Tujuan')
                            ->required()
                            ->relationship('outlet', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih outlet')
                            ->helperText('Outlet yang akan menerima barang'),
                    ])
                    ->columns(1),

                Section::make('Item Purchase Order')
                    ->schema([
                        Repeater::make('items')
                            ->label('Items')
                            ->relationship('items')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Produk')
                                    ->required()
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->placeholder('Pilih produk')
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state) {
                                            $product = \App\Models\Product::find($state);
                                            if ($product) {
                                                $set('unit_cost', $product->cost ?? 0);
                                            }
                                        }
                                    }),

                                TextInput::make('quantity_ordered')
                                    ->label('Qty Order')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($get, $set) {
                                        $qty = (float) $get('quantity_ordered') ?: 0;
                                        $cost = (float) $get('unit_cost') ?: 0;
                                        $set('total_cost', $qty * $cost);

                                        // Update total_amount
                                        $items = $get('../../items') ?? [];
                                        $total = 0;
                                        foreach ($items as $item) {
                                            $total += (float) ($item['total_cost'] ?? 0);
                                        }
                                        $set('../../total_amount', $total);
                                    }),

                                TextInput::make('unit_cost')
                                    ->label('Harga Satuan')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($get, $set) {
                                        $qty = (float) $get('quantity_ordered') ?: 0;
                                        $cost = (float) $get('unit_cost') ?: 0;
                                        $set('total_cost', $qty * $cost);

                                        // Update total_amount
                                        $items = $get('../../items') ?? [];
                                        $total = 0;
                                        foreach ($items as $item) {
                                            $total += (float) ($item['total_cost'] ?? 0);
                                        }
                                        $set('../../total_amount', $total);
                                    }),

                                TextInput::make('total_cost')
                                    ->label('Total')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(0)
                                    ->readOnly()
                                    ->dehydrated(),

                                Textarea::make('notes')
                                    ->label('Catatan Item')
                                    ->rows(2)
                                    ->placeholder('Catatan untuk item ini...')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Tambah Item')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => \App\Models\Product::find($state['product_id'])?->name ?? 'Item Baru')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $items = $get('items') ?? [];
                                $total = 0;
                                foreach ($items as $item) {
                                    $total += (float) ($item['total_cost'] ?? 0);
                                }
                                $set('total_amount', $total);
                            })
                            ->deleteAction(
                                fn ($action) => $action->after(function (Get $get, Set $set) {
                                    $items = $get('items') ?? [];
                                    $total = 0;
                                    foreach ($items as $item) {
                                        $total += (float) ($item['total_cost'] ?? 0);
                                    }
                                    $set('total_amount', $total);
                                })
                            ),
                    ])
                    ->columns(1),

                Section::make('Catatan & Total')
                    ->schema([
                        TextInput::make('total_amount')
                            ->label('Total Amount')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->readOnly()
                            ->helperText('Total akan dihitung otomatis dari item yang ditambahkan')
                            ->dehydrated(),

                        Textarea::make('notes')
                            ->label('Catatan Tambahan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Catatan penting tentang purchase order ini...')
                            ->helperText('Informasi tambahan yang perlu diingat'),
                    ])
                    ->columns(1),

                Section::make('Riwayat Pembayaran')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('payment_history')
                            ->label('')
                            ->content(function ($record) {
                                if (! $record || ! $record->exists) {
                                    return 'Belum ada pembayaran. Simpan PO terlebih dahulu untuk mulai mencatat pembayaran.';
                                }

                                $payments = $record->payments()->orderBy('payment_date', 'desc')->get();

                                if ($payments->isEmpty()) {
                                    return 'Belum ada pembayaran untuk PO ini.';
                                }

                                $html = '<div class="space-y-2">';
                                $html .= '<div class="overflow-x-auto">';
                                $html .= '<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">';
                                $html .= '<thead class="bg-gray-50 dark:bg-gray-800">';
                                $html .= '<tr>';
                                $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">No. Pembayaran</th>';
                                $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>';
                                $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Jumlah</th>';
                                $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Metode</th>';
                                $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Referensi</th>';
                                $html .= '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Catatan</th>';
                                $html .= '</tr>';
                                $html .= '</thead>';
                                $html .= '<tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">';

                                foreach ($payments as $payment) {
                                    $html .= '<tr>';
                                    $html .= '<td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">'.$payment->payment_number.'</td>';
                                    $html .= '<td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">'.date('d M Y', strtotime($payment->payment_date)).'</td>';
                                    $html .= '<td class="px-4 py-2 text-sm font-semibold text-green-600 dark:text-green-400">Rp '.number_format($payment->amount, 0, ',', '.').'</td>';
                                    $html .= '<td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">'.ucfirst($payment->payment_method).'</td>';
                                    $html .= '<td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">'.($payment->reference ?? '-').'</td>';
                                    $html .= '<td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">'.($payment->notes ?? '-').'</td>';
                                    $html .= '</tr>';
                                }

                                $html .= '</tbody>';
                                $html .= '</table>';
                                $html .= '</div>';

                                // Summary
                                $totalPaid = $payments->sum('amount');
                                $remaining = $record->total_amount - $totalPaid;

                                $html .= '<div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">';
                                $html .= '<div class="grid grid-cols-3 gap-4 text-sm">';
                                $html .= '<div><span class="text-gray-600 dark:text-gray-400">Total PO:</span> <span class="font-semibold text-gray-900 dark:text-gray-100">Rp '.number_format($record->total_amount, 0, ',', '.').'</span></div>';
                                $html .= '<div><span class="text-gray-600 dark:text-gray-400">Total Dibayar:</span> <span class="font-semibold text-green-600 dark:text-green-400">Rp '.number_format($totalPaid, 0, ',', '.').'</span></div>';
                                $html .= '<div><span class="text-gray-600 dark:text-gray-400">Sisa Hutang:</span> <span class="font-semibold '.($remaining > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400').'">Rp '.number_format($remaining, 0, ',', '.').'</span></div>';
                                $html .= '</div>';
                                $html .= '</div>';

                                $html .= '</div>';

                                return new \Illuminate\Support\HtmlString($html);
                            }),
                    ])
                    ->visible(fn ($record) => $record && $record->exists && $record->status === 'received')
                    ->collapsible()
                    ->collapsed(false),

                Section::make('Timeline & Penerimaan')
                    ->schema([
                        DatePicker::make('expected_date')
                            ->label('Tanggal Estimasi Tiba')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->minDate(fn (Get $get) => $get('order_date'))
                            ->helperText('Estimasi tanggal barang akan tiba'),

                        DatePicker::make('received_date')
                            ->label('Tanggal Diterima')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->hidden(fn (Get $get) => $get('status') !== 'received')
                            ->helperText('Tanggal aktual barang diterima'),

                        Select::make('received_by')
                            ->label('Diterima Oleh')
                            ->relationship('receiver', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih user')
                            ->hidden(fn (Get $get) => $get('status') !== 'received')
                            ->helperText('User yang menerima barang'),
                    ])
                    ->columns(3),
            ]);
    }
}
