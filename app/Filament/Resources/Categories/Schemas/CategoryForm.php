<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('business_id')
                    ->default(fn () => auth()->user()?->business_id),

                Section::make('Informasi Kategori')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Kategori')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Makanan, Minuman, Elektronik')
                            ->helperText('Nama kategori untuk mengelompokkan produk')
                            ->autofocus(),
                    ])
                    ->columns(1),
            ]);
    }
}
