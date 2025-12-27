<?php

namespace App\Filament\Resources\Stocks\Schemas;

use App\Models\Outlet;
use App\Models\Product;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class StockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Stock Information')
                    ->description('Manage stock quantity for products at specific outlets')
                    ->schema([
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
                            ->live()
                            ->helperText('Select the outlet where this stock will be managed'),

                        Select::make('product_id')
                            ->label('Product')
                            ->options(function () {
                                $business = Auth::user()->business;

                                return Product::query()
                                    ->where('business_id', $business->id)
                                    ->get()
                                    ->mapWithKeys(function ($product) {
                                        return [$product->id => $product->name.' - '.$product->sku];
                                    });
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('Select the product to manage stock for'),

                        TextInput::make('quantity')
                            ->label('Stock Quantity')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->suffix('units')
                            ->helperText('Current stock quantity for this product at the selected outlet'),
                    ])
                    ->columns(1),
            ]);
    }
}
