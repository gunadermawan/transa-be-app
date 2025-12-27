<?php

namespace App\Filament\Resources\OrderPayments\Pages;

use App\Filament\Resources\OrderPayments\OrderPaymentResource;
use Filament\Resources\Pages\Page;

class ComingSoon extends Page
{
    protected static string $resource = OrderPaymentResource::class;

    protected string $view = 'filament.pages.coming-soon';

    public function getTitle(): string
    {
        return 'Order Payments - Coming Soon';
    }

    public function getHeading(): string
    {
        return 'Fitur Order Payments Segera Hadir!';
    }

    public function getSubheading(): ?string
    {
        return 'Fitur ini akan dibahas secara lengkap di program Academy POS. Segera bergabung untuk mendapatkan akses penuh!';
    }
}
