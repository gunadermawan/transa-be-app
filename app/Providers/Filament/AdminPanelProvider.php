<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn (): string => Blade::render('<div class="text-center mt-6"><p class="text-sm text-gray-600 dark:text-gray-400">Belum punya akun? <a href="{{ url(\'/register\') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-semibold hover:underline">Daftar Sekarang</a></p></div>'),
        );

        // Add subscription badge in user menu
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn (): string => Blade::render('
                @php
                    $business = auth()->user()->business ?? null;
                    $subscription = $business?->currentSubscription ?? null;
                    $plan = $subscription?->plan ?? null;

                    if ($plan) {
                        $badgeColor = match($plan->name) {
                            \'Trial\' => \'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300\',
                            \'Starter\' => \'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300\',
                            \'Professional\' => \'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300\',
                            default => \'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300\',
                        };

                        $daysLeft = $subscription?->end_date ? now()->diffInDays($subscription->end_date, false) : 0;
                        $daysLeftText = $daysLeft > 0 ? "{$daysLeft} hari lagi" : "Expired";
                    }
                @endphp

                @if($plan)
                    <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Subscription</span>
                            <span class="{{ $badgeColor }} text-xs font-semibold px-2 py-1 rounded-full">
                                {{ $plan->name }}
                            </span>
                        </div>
                        @if($daysLeft > 0 && $daysLeft <= 7)
                            <div class="mt-1 text-xs text-orange-600 dark:text-orange-400">
                                ⚠️ {{ $daysLeftText }}
                            </div>
                        @endif
                    </div>
                @endif
            '),
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->brandName('POS SaaS JagoFlutter Academy')
            ->favicon(asset('images/favicon.svg'))
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // Custom dashboard widgets will be auto-discovered
                // FilamentInfoWidget excluded (welcome card removed)
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'User Management',
                'Business Management',
                'Subscription & Billing',
                'Master Data',
                'Inventory Management',
                'Sales & Transactions',
                'Customer Management',
                'Marketing',
                'Employee Management',
                'Reports & Analytics',
                'System',
                'Settings',
            ]);
    }
}
