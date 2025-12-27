<?php

namespace App\Filament\Resources\ProductVariants\Pages;

use App\Filament\Resources\ProductVariants\ProductVariantResource;
use Filament\Resources\Pages\Page;

class ComingSoon extends Page
{
    protected static string $resource = ProductVariantResource::class;

    protected string $view = 'filament.pages.coming-soon';

    public function getTitle(): string
    {
        return 'Product Variants - Coming Soon';
    }

    public function getHeading(): string
    {
        return 'Fitur Product Variants Segera Hadir!';
    }

    public function getSubheading(): ?string
    {
        return 'Fitur ini akan dibahas secara lengkap di program Academy POS. Segera bergabung untuk mendapatkan akses penuh!';
    }
}
