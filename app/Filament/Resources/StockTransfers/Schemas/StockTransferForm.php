<?php

namespace App\Filament\Resources\StockTransfers\Schemas;

use App\Models\Outlet;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class StockTransferForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Transfer Information')
                    ->description('Manage stock transfer between outlets')
                    ->schema([
                        Hidden::make('business_id')
                            ->default(fn () => Auth::user()->business_id),

                        Hidden::make('requested_by')
                            ->default(fn () => Auth::id()),

                        TextInput::make('transfer_number')
                            ->label('Transfer Number')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn () => 'TRF-'.strtoupper(substr(uniqid(), -8)))
                            ->helperText('Auto-generated transfer number'),

                        Select::make('from_outlet_id')
                            ->label('From Outlet (Source)')
                            ->options(function () {
                                $business = Auth::user()->business;

                                return Outlet::query()
                                    ->where('business_id', $business->id)
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->helperText('Select the outlet to transfer stock from'),

                        Select::make('to_outlet_id')
                            ->label('To Outlet (Destination)')
                            ->options(function () {
                                $business = Auth::user()->business;

                                return Outlet::query()
                                    ->where('business_id', $business->id)
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->different('from_outlet_id')
                            ->helperText('Select the outlet to transfer stock to'),

                        DatePicker::make('transfer_date')
                            ->label('Transfer Date')
                            ->required()
                            ->default(now())
                            ->native(false)
                            ->helperText('Date when transfer is requested'),

                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->placeholder('Additional notes or instructions for this transfer...')
                            ->helperText('Optional notes about this transfer'),
                    ])
                    ->columns(1),

                Section::make('Transfer Items')
                    ->description('Add products to transfer')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->options(function () {
                                        $business = Auth::user()->business;

                                        return Product::query()
                                            ->where('business_id', $business->id)
                                            ->get()
                                            ->mapWithKeys(function ($product) {
                                                return [$product->id => $product->name.' ('.$product->sku.')'];
                                            });
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->columnSpan(2),

                                TextInput::make('quantity_requested')
                                    ->label('Quantity')
                                    ->required()
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->suffix('units')
                                    ->columnSpan(1),

                                Textarea::make('notes')
                                    ->label('Item Notes')
                                    ->rows(2)
                                    ->placeholder('Notes for this item...')
                                    ->columnSpan(3),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Add Product')
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => Product::find($state['product_id'])?->name ?? 'New Item'),
                    ])
                    ->columns(1),
            ]);
    }
}
