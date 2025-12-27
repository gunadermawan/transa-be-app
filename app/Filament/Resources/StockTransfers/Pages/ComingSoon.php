<?php

namespace App\Filament\Resources\StockTransfers\Pages;

use App\Filament\Resources\StockTransfers\StockTransferResource;
use Filament\Resources\Pages\Page;

class ComingSoon extends Page
{
    protected static string $resource = StockTransferResource::class;

    protected string $view = 'filament.pages.coming-soon';

    public function getTitle(): string
    {
        return 'Stock Transfers - Coming Soon';
    }

    public function getHeading(): string
    {
        return 'Fitur Stock Transfers Segera Hadir!';
    }

    public function getSubheading(): ?string
    {
        return 'Fitur ini akan dibahas secara lengkap di program Academy POS. Segera bergabung untuk mendapatkan akses penuh!';
    }
}
