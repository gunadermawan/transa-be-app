<?php

namespace App\Filament\Resources\ProductVariants\Schemas;

use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ProductVariantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Variant Information')
                    ->description('Create variants for products (e.g., different sizes, colors, or combinations)')
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
                            ->live()
                            ->helperText('Select the parent product for this variant'),

                        TextInput::make('variant_name')
                            ->label('Variant Name')
                            ->required()
                            ->placeholder('e.g., Large - Red, Small - Blue')
                            ->helperText('Descriptive name for this variant')
                            ->columnSpan(2),

                        TextInput::make('sku')
                            ->label('SKU (Stock Keeping Unit)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('PROD-VAR-001')
                            ->helperText('Unique SKU for this variant')
                            ->default(fn () => 'VAR-'.strtoupper(substr(uniqid(), -8))),

                        TextInput::make('barcode')
                            ->label('Barcode')
                            ->unique(ignoreRecord: true)
                            ->placeholder('1234567890123')
                            ->helperText('Barcode for scanning (optional)')
                            ->default(fn () => (string) rand(1000000000000, 9999999999999)),
                    ])
                    ->columns(2),

                Section::make('Pricing & Cost')
                    ->description('Price and cost adjustments relative to base product')
                    ->schema([
                        TextInput::make('price_adjustment')
                            ->label('Price Adjustment')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->helperText('Additional price for this variant (can be negative)')
                            ->placeholder('10000'),

                        TextInput::make('cost_adjustment')
                            ->label('Cost Adjustment')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->helperText('Additional cost for this variant (can be negative)')
                            ->placeholder('5000'),
                    ])
                    ->columns(1),

                Section::make('Variant Attributes')
                    ->description('Define specific attributes for this variant (e.g., Size, Color, Material)')
                    ->schema([
                        KeyValue::make('attributes')
                            ->label('Attributes')
                            ->keyLabel('Attribute Name')
                            ->valueLabel('Value')
                            ->helperText('Add variant-specific attributes (e.g., Size: L, Color: Red)')
                            ->reorderable()
                            ->addActionLabel('Add Attribute'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Image & Display')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Variant Image')
                            ->image()
                            ->imageEditor()
                            ->directory('product-variants')
                            ->maxSize(2048)
                            ->helperText('Upload image specific to this variant (max 2MB)'),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Order in which this variant appears'),

                        Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'out_of_stock' => 'Out of Stock',
                            ])
                            ->default('active')
                            ->native(false)
                            ->helperText('Current status of this variant'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }
}
