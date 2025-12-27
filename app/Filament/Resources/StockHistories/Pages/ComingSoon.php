<?php

namespace App\Filament\Resources\StockHistories\Pages;

use App\Filament\Resources\StockHistories\StockHistoryResource;
use Filament\Resources\Pages\Page;

class ComingSoon extends Page
{
    protected static string $resource = StockHistoryResource::class;

    protected string $view = 'filament.pages.coming-soon';

    public function getTitle(): string
    {
        return 'Stock Histories - Coming Soon';
    }

    public function getHeading(): string
    {
        return 'Fitur Stock Histories Segera Hadir!';
    }

    public function getSubheading(): ?string
    {
        return 'Fitur ini akan dibahas secara lengkap di program Academy POS. Segera bergabung untuk mendapatkan akses penuh!';
    }
}
