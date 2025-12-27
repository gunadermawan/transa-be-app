<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('business_id')
                    ->default(fn () => auth()->user()?->business_id),

                Section::make('Informasi Produk')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Kopi Americano, Nasi Goreng')
                            ->helperText('Nama produk yang akan ditampilkan')
                            ->autofocus()
                            ->columnSpan(2),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->required()
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Kategori')
                                    ->required(),
                            ])
                            ->helperText('Pilih kategori produk'),

                        Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Tidak Aktif',
                                'out_of_stock' => 'Stok Habis',
                            ])
                            ->default('active')
                            ->native(false)
                            ->helperText('Status ketersediaan produk'),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->placeholder('Deskripsi detail tentang produk...')
                            ->helperText('Informasi tambahan tentang produk')
                            ->columnSpan(2),

                        FileUpload::make('image')
                            ->label('Gambar Produk')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                                '4:3',
                            ])
                            ->directory('products')
                            ->maxSize(2048)
                            ->helperText('Upload gambar produk (maksimal 2MB)')
                            ->columnSpan(1),

                        ColorPicker::make('color')
                            ->label('Warna Label')
                            ->helperText('Warna untuk identifikasi visual produk'),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Harga & Biaya')
                    ->schema([
                        TextInput::make('price')
                            ->label('Harga Jual')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('50000')
                            ->helperText('Harga jual ke pelanggan')
                            ->minValue(0),

                        TextInput::make('cost')
                            ->label('Harga Modal')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('30000')
                            ->helperText('Harga modal/pembelian produk')
                            ->minValue(0),

                        Select::make('unit_type')
                            ->label('Satuan')
                            ->required()
                            ->options([
                                'pcs' => 'Pcs (Pieces)',
                                'kg' => 'Kg (Kilogram)',
                                'gr' => 'Gr (Gram)',
                                'ltr' => 'Ltr (Liter)',
                                'ml' => 'Ml (Mililiter)',
                                'box' => 'Box',
                                'pack' => 'Pack',
                                'lusin' => 'Lusin',
                            ])
                            ->default('pcs')
                            ->native(false)
                            ->searchable()
                            ->helperText('Satuan pengukuran produk'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Manajemen Stok')
                    ->schema([
                        Toggle::make('is_stock_managed')
                            ->label('Kelola Stok')
                            ->helperText('Aktifkan jika produk ini memerlukan pengelolaan stok')
                            ->default(true)
                            ->live()
                            ->columnSpan(3),

                        TextInput::make('stock_minimum')
                            ->label('Stok Minimum')
                            ->required()
                            ->numeric()
                            ->default(5)
                            ->placeholder('5')
                            ->helperText('Batas minimum stok sebelum peringatan')
                            ->minValue(0)
                            ->visible(fn ($get) => $get('is_stock_managed')),

                        TextInput::make('reorder_point')
                            ->label('Titik Pemesanan Ulang')
                            ->required()
                            ->numeric()
                            ->default(10)
                            ->placeholder('10')
                            ->helperText('Stok yang memicu pemesanan ulang otomatis')
                            ->minValue(0)
                            ->visible(fn ($get) => $get('is_stock_managed')),

                        TextInput::make('optimal_stock_level')
                            ->label('Level Stok Optimal')
                            ->numeric()
                            ->placeholder('100')
                            ->helperText('Target stok ideal yang harus dijaga')
                            ->minValue(0)
                            ->visible(fn ($get) => $get('is_stock_managed')),
                    ])
                    ->columns(3)
                    ->collapsible(),

                Section::make('Kode Produk')
                    ->schema([
                        TextInput::make('sku')
                            ->label('SKU (Stock Keeping Unit)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('PRD-001')
                            ->helperText('Kode unik untuk identifikasi produk (akan auto-generate jika kosong)')
                            ->maxLength(100)
                            ->default(fn () => 'PRD-'.strtoupper(substr(uniqid(), -6))),

                        TextInput::make('barcode')
                            ->label('Barcode')
                            ->unique(ignoreRecord: true)
                            ->placeholder('1234567890123')
                            ->helperText('Barcode untuk scanning produk (akan auto-generate jika kosong)')
                            ->maxLength(100)
                            ->default(fn () => (string) rand(1000000000000, 9999999999999)),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Pengaturan Lanjutan')
                    ->schema([
                        Select::make('product_type')
                            ->label('Tipe Produk')
                            ->required()
                            ->options([
                                'physical' => 'Produk Fisik',
                                'digital' => 'Produk Digital',
                                'service' => 'Jasa/Layanan',
                            ])
                            ->default('physical')
                            ->native(false)
                            ->helperText('Jenis produk yang dijual'),

                        Toggle::make('is_featured')
                            ->label('Produk Unggulan')
                            ->helperText('Tandai sebagai produk unggulan/favorit')
                            ->default(false),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
