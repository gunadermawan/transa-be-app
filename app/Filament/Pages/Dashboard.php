<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string
    {
        $businessName = auth()->user()->business?->name ?? 'Dashboard';

        return 'Dashboard - ' . $businessName;
    }

    public function getHeading(): string
    {
        $businessName = auth()->user()->business?->name ?? 'Dashboard';

        return 'Dashboard - ' . $businessName;
    }
}
