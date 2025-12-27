<?php

namespace App\Filament\Resources\CashDrawerSessions\Pages;

use App\Filament\Resources\CashDrawerSessions\CashDrawerSessionResource;
use Filament\Resources\Pages\Page;

class ComingSoon extends Page
{
    protected static string $resource = CashDrawerSessionResource::class;

    protected string $view = 'filament.pages.coming-soon';

    public function getTitle(): string
    {
        return 'Cash Drawer Sessions - Coming Soon';
    }

    public function getHeading(): string
    {
        return 'Fitur Cash Drawer Sessions Segera Hadir!';
    }

    public function getSubheading(): ?string
    {
        return 'Fitur ini akan dibahas secara lengkap di program Academy POS. Segera bergabung untuk mendapatkan akses penuh!';
    }
}
