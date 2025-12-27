<?php

namespace App\Filament\Resources\Promotions\Pages;

use App\Filament\Resources\Promotions\PromotionResource;
use Filament\Resources\Pages\Page;

class ComingSoon extends Page
{
    protected static string $resource = PromotionResource::class;

    protected string $view = 'filament.pages.coming-soon';

    public function getTitle(): string
    {
        return 'Promotions - Coming Soon';
    }

    public function getHeading(): string
    {
        return 'Fitur Promotions Segera Hadir!';
    }

    public function getSubheading(): ?string
    {
        return 'Fitur ini akan dibahas secara lengkap di program Academy POS. Segera bergabung untuk mendapatkan akses penuh!';
    }
}
