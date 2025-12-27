<?php

namespace App\Filament\Resources\PurchaseOrders\Pages;

use App\Filament\Resources\PurchaseOrders\PurchaseOrderResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approve')
                ->label('Approve PO')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn ($record) => in_array($record->status, ['draft', 'pending']))
                ->requiresConfirmation()
                ->modalHeading('Approve Purchase Order')
                ->modalDescription('Apakah Anda yakin ingin meng-approve PO ini? Setelah approved, Anda bisa melakukan penerimaan barang.')
                ->modalSubmitActionLabel('Ya, Approve')
                ->action(function ($record) {
                    $record->update(['status' => 'approved']);

                    Notification::make()
                        ->success()
                        ->title('PO Berhasil Diapprove')
                        ->body('Purchase Order '.$record->po_number.' telah diapprove.')
                        ->send();
                }),

            Action::make('receive_goods')
                ->label('Terima Barang')
                ->icon('heroicon-o-inbox-arrow-down')
                ->color('success')
                ->visible(fn ($record) => in_array($record->status, ['approved', 'pending']))
                ->form([
                    DatePicker::make('received_date')
                        ->label('Tanggal Terima')
                        ->required()
                        ->default(now())
                        ->native(false)
                        ->displayFormat('d/m/Y'),

                    Repeater::make('items')
                        ->label('Item yang Diterima')
                        ->schema([
                            TextInput::make('product_name')
                                ->label('Produk')
                                ->disabled(),

                            TextInput::make('quantity_ordered')
                                ->label('Qty Order')
                                ->disabled()
                                ->suffix('pcs'),

                            TextInput::make('quantity_received')
                                ->label('Qty Diterima')
                                ->required()
                                ->numeric()
                                ->minValue(0)
                                ->default(fn ($state) => $state['quantity_ordered'] ?? 0)
                                ->suffix('pcs'),
                        ])
                        ->columns(3)
                        ->default(function ($record) {
                            return $record->items->map(function ($item) {
                                return [
                                    'id' => $item->id,
                                    'product_name' => $item->product->name,
                                    'quantity_ordered' => $item->quantity_ordered,
                                    'quantity_received' => $item->quantity_received,
                                ];
                            })->toArray();
                        })
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false),
                ])
                ->action(function (array $data, $record) {
                    // Update items
                    foreach ($data['items'] as $itemData) {
                        $item = $record->items()->find($itemData['id']);
                        if ($item) {
                            $item->update([
                                'quantity_received' => $itemData['quantity_received'],
                            ]);

                            // Update stock - tambahkan qty yang diterima
                            $stock = \App\Models\Stock::firstOrCreate(
                                [
                                    'product_id' => $item->product_id,
                                    'outlet_id' => $record->outlet_id,
                                ],
                                [
                                    'quantity' => 0,
                                ]
                            );

                            $stock->increment('quantity', $itemData['quantity_received']);
                            $stock->refresh(); // Refresh to get updated quantity

                            // Create stock history
                            \App\Models\StockHistory::create([
                                'stock_id' => $stock->id,
                                'user_id' => auth()->id(),
                                'outlet_id' => $record->outlet_id,
                                'type' => 'in',
                                'quantity' => $itemData['quantity_received'],
                                'current_stock' => $stock->quantity,
                                'reference' => 'PO: '.$record->po_number,
                                'note' => 'Goods received from Purchase Order '.$record->po_number,
                            ]);
                        }
                    }

                    // Update PO
                    $record->update([
                        'received_date' => $data['received_date'],
                        'received_by' => auth()->id(),
                        'status' => 'received',
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Barang Berhasil Diterima')
                        ->body('Purchase Order '.$record->po_number.' telah diterima dan stock sudah diupdate.')
                        ->send();
                }),

            Action::make('add_payment')
                ->label('Bayar ke Supplier')
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->visible(fn ($record) => $record->status === 'received' && $record->remaining_amount > 0)
                ->form([
                    TextInput::make('payment_number')
                        ->label('Nomor Pembayaran')
                        ->required()
                        ->default(fn () => 'PAY-'.date('Ymd').'-'.strtoupper(Str::random(4)))
                        ->maxLength(50),

                    DatePicker::make('payment_date')
                        ->label('Tanggal Bayar')
                        ->required()
                        ->default(now())
                        ->native(false)
                        ->displayFormat('d/m/Y'),

                    TextInput::make('amount')
                        ->label('Jumlah Bayar')
                        ->required()
                        ->numeric()
                        ->prefix('Rp')
                        ->default(fn ($record) => $record->remaining_amount)
                        ->maxValue(fn ($record) => $record->remaining_amount)
                        ->minValue(1)
                        ->helperText(fn ($record) => 'Sisa yang harus dibayar: Rp '.number_format($record->remaining_amount, 0, ',', '.')),

                    Select::make('payment_method')
                        ->label('Metode Pembayaran')
                        ->required()
                        ->options([
                            'cash' => 'Tunai/Cash',
                            'transfer' => 'Transfer Bank',
                            'cheque' => 'Cek/Giro',
                        ])
                        ->native(false),

                    TextInput::make('reference')
                        ->label('Referensi')
                        ->placeholder('Nomor transfer/giro')
                        ->maxLength(100)
                        ->helperText('Nomor bukti transfer atau nomor giro (opsional)'),

                    Textarea::make('notes')
                        ->label('Catatan')
                        ->rows(2)
                        ->placeholder('Catatan pembayaran...'),
                ])
                ->action(function (array $data, $record) {
                    \App\Models\SupplierPayment::create([
                        'purchase_order_id' => $record->id,
                        'business_id' => $record->business_id,
                        'supplier_id' => $record->supplier_id,
                        'paid_by' => auth()->id(),
                        'payment_number' => $data['payment_number'],
                        'payment_date' => $data['payment_date'],
                        'amount' => $data['amount'],
                        'payment_method' => $data['payment_method'],
                        'reference' => $data['reference'] ?? null,
                        'notes' => $data['notes'] ?? null,
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Pembayaran Berhasil')
                        ->body('Pembayaran Rp '.number_format($data['amount'], 0, ',', '.').' telah dicatat.')
                        ->send();
                }),

            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
