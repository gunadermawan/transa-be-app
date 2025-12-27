<?php

namespace App\Filament\Resources\CashTransactions\Pages;

use App\Filament\Resources\CashTransactions\CashTransactionResource;
use Filament\Resources\Pages\Page;

class ComingSoon extends Page
{
    protected static string $resource = CashTransactionResource::class;

    protected string $view = 'filament.pages.coming-soon';

    public function getTitle(): string
    {
        return 'Cash Transactions - Coming Soon';
    }

    public function getHeading(): string
    {
        return 'Fitur Cash Transactions Segera Hadir!';
    }

    public function getSubheading(): ?string
    {
        return 'Fitur ini akan dibahas secara lengkap di program Academy POS. Segera bergabung untuk mendapatkan akses penuh!';
    }
}
