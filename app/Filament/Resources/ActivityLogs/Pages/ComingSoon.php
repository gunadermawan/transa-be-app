<?php

namespace App\Filament\Resources\ActivityLogs\Pages;

use App\Filament\Resources\ActivityLogs\ActivityLogResource;
use Filament\Resources\Pages\Page;

class ComingSoon extends Page
{
    protected static string $resource = ActivityLogResource::class;

    protected string $view = 'filament.pages.coming-soon';

    public function getTitle(): string
    {
        return 'Activity Logs - Coming Soon';
    }

    public function getHeading(): string
    {
        return 'Fitur Activity Logs Segera Hadir!';
    }

    public function getSubheading(): ?string
    {
        return 'Fitur ini akan dibahas secara lengkap di program Academy POS. Segera bergabung untuk mendapatkan akses penuh!';
    }
}
