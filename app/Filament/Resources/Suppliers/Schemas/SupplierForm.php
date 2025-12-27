<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('business_id')
                    ->default(fn () => auth()->user()?->business_id),

                Section::make('Informasi Supplier')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Supplier')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($get, $set, $state) {
                                if (empty($get('code'))) {
                                    $set('code', 'SUP-'.strtoupper(Str::random(6)));
                                }
                            })
                            ->placeholder('PT. Supplier Makmur')
                            ->helperText('Nama lengkap perusahaan supplier'),

                        TextInput::make('code')
                            ->label('Kode Supplier')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->default(fn () => 'SUP-'.strtoupper(Str::random(6)))
                            ->placeholder('SUP-ABC123')
                            ->helperText('Kode unik untuk identifikasi supplier')
                            ->alphaDash(),

                        Select::make('status')
                            ->label('Status')
                            ->required()
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'blocked' => 'Blocked',
                            ])
                            ->default('active')
                            ->native(false)
                            ->helperText('Status aktif/tidak aktif supplier'),
                    ])
                    ->columns(1),

                Section::make('Kontak & Komunikasi')
                    ->schema([
                        TextInput::make('contact_person')
                            ->label('Nama Kontak Person')
                            ->maxLength(255)
                            ->placeholder('John Doe')
                            ->helperText('Nama PIC (Person In Charge)'),

                        TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('081234567890')
                            ->helperText('Nomor telepon yang dapat dihubungi')
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->placeholder('supplier@example.com')
                            ->helperText('Email untuk komunikasi resmi'),

                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Jl. Contoh No. 123, Jakarta')
                            ->helperText('Alamat kantor/gudang supplier'),
                    ])
                    ->columns(2),

                Section::make('Ketentuan Pembayaran & Catatan')
                    ->schema([
                        Select::make('payment_terms')
                            ->label('Ketentuan Pembayaran')
                            ->options([
                                'cash' => 'Tunai/Cash',
                                'tempo_7' => 'Tempo 7 Hari',
                                'tempo_14' => 'Tempo 14 Hari',
                                'tempo_30' => 'Tempo 30 Hari',
                            ])
                            ->native(false)
                            ->placeholder('Pilih ketentuan pembayaran')
                            ->helperText('Ketentuan waktu pembayaran dari supplier'),

                        Textarea::make('notes')
                            ->label('Catatan Tambahan')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Catatan penting tentang supplier...')
                            ->helperText('Informasi tambahan yang perlu diingat tentang supplier ini'),
                    ])
                    ->columns(1),
            ]);
    }
}
