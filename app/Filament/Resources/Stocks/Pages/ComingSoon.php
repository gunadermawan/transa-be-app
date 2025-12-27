<?php

namespace App\Filament\Resources\Stocks\Pages;

use App\Filament\Resources\Stocks\StockResource;
use Filament\Resources\Pages\Page;

class ComingSoon extends Page
{
    protected static string $resource = StockResource::class;

    protected string $view = 'filament.pages.coming-soon';

    public function getTitle(): string
    {
        return 'Stocks - Coming Soon';
    }

    public function getHeading(): string
    {
        return 'Fitur Stocks Segera Hadir!';
    }

    public function getSubheading(): ?string
    {
        return 'Fitur ini akan dibahas secara lengkap di program Academy POS. Segera bergabung untuk mendapatkan akses penuh!';
    }
}
