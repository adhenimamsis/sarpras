<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\AparIssueTable;
use App\Filament\Widgets\AparStatusOverview;
use App\Filament\Widgets\AssetStatsOverview;
use App\Filament\Widgets\KerusakanOverview;
use App\Filament\Widgets\LaporanChart;
use App\Filament\Widgets\UtilityChartWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')

            // --- AKSES & OTENTIKASI ---
            ->login()
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')

            // --- BRANDING & TAMPILAN ---
            ->brandName('SimSarpras Bendan')
            ->brandLogo(fn () => view('components.logo-pkm'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => Color::Blue,
                'gray' => Color::Slate,
                'danger' => Color::Rose,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
                'info' => Color::Sky,
            ])
            ->font('Poppins')
            ->darkMode(false)

            // --- LAYOUT & UX ---
            ->sidebarFullyCollapsibleOnDesktop()
            ->maxContentWidth(MaxWidth::Full)
            ->spa()
            ->unsavedChangesAlerts()
            ->sidebarWidth('280px')

            // --- DISCOVERY ---
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])

            // --- WIDGETS ---
            ->widgets([
                AssetStatsOverview::class,
                KerusakanOverview::class,
                AparStatusOverview::class,
                LaporanChart::class,
                UtilityChartWidget::class,
                AparIssueTable::class,
            ])

            // --- PENGATURAN NAVIGASI (SOLUSI: Hapus ->icon() di sini) ---
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Sarana & Prasarana'), // Ikon dihapus agar bisa pakai ikon di tiap Resource
                NavigationGroup::make()
                    ->label('Utilitas & MFK'),
                NavigationGroup::make()
                    ->label('Pusat Laporan'),
                NavigationGroup::make()
                    ->label('Sistem Pengaturan')
                    ->collapsed(),
            ])

            // --- STYLE HOOKS ---
            ->renderHook(
                'panels::body.start',
                fn () => $this->getCustomUIStyles(),
            )

            // --- MIDDLEWARE ---
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
            ]);
    }

    /**
     * UI Customization: Glassmorphism & Emerald UI Enhancements.
     */
    protected function getCustomUIStyles(): string
    {
        return Blade::render('
            <style>
                .fi-layout {
                    background:
                        radial-gradient(circle at 10% -15%, rgba(29, 78, 216, 0.08), transparent 34%),
                        radial-gradient(circle at 92% 0%, rgba(79, 70, 229, 0.05), transparent 25%),
                        linear-gradient(180deg, #f7f9fc 0%, #f2f6fb 100%) !important;
                }

                .fi-sidebar {
                    background: #ffffff !important;
                    border-right: 1px solid #dbe5f0 !important;
                    box-shadow: 0 10px 24px -22px rgba(15, 23, 42, 0.35);
                }

                .fi-topbar {
                    background: rgba(255, 255, 255, 0.9) !important;
                    border-bottom: 1px solid #dbe5f0 !important;
                    backdrop-filter: blur(4px);
                }

                .fi-btn {
                    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
                    border-radius: 0.75rem !important;
                }

                .fi-btn:active {
                    transform: scale(0.98);
                }

                .fi-section,
                .fi-wi-stats-overview-stat,
                .fi-ta-ctn,
                .fi-fo-section {
                    border-radius: 0.9rem !important;
                    border: 1px solid #dbe5f0 !important;
                    background: #ffffff !important;
                    box-shadow: 0 14px 26px -24px rgba(15, 23, 42, 0.28);
                }

                .fi-sidebar .fi-sidebar-item-btn,
                .fi-sidebar .fi-sidebar-item-label {
                    color: #334155 !important;
                }

                .fi-sidebar .fi-sidebar-group-label {
                    color: #64748b !important;
                }

                .fi-sidebar .fi-sidebar-item-btn {
                    border-radius: 0.75rem !important;
                    border: 1px solid transparent !important;
                }

                .fi-sidebar .fi-sidebar-item-btn:hover {
                    background: #f8fafc !important;
                    color: #0f172a !important;
                }

                .fi-sidebar .fi-sidebar-item-active .fi-sidebar-item-btn,
                .fi-sidebar .fi-sidebar-item-active .fi-sidebar-item-label {
                    background: rgba(29, 78, 216, 0.08) !important;
                    border: 1px solid rgba(29, 78, 216, 0.2) !important;
                    color: #0f172a !important;
                }

                .fi-ta-header-cell,
                .fi-ta-text-item-label,
                .fi-section-header-heading {
                    color: #0f172a !important;
                }
            </style>
        ');
    }
}
