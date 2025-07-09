<?php

namespace Modules;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Nwidart\Modules\Facades\Module;
use Symfony\Component\Finder\Finder;
use ReflectionClass;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ->plugins([
                FilamentShieldPlugin::make()
            ])
            ->authMiddleware([
                Authenticate::class,
            ])->navigationGroups([
                'School',
                'Members',
            ]);

        $this->extendDiscoverResources($panel);

        return $panel;
    }

    protected function extendDiscoverResources(Panel $panel): void
    {
        $resources = [];

        foreach (Module::allEnabled() as $module) {
            $resourcePath = $module->getExtraPath('Filament/Resources');
            $namespace = "Modules\\{$module->getName()}\\Filament\\Resources";

            if (! is_dir($resourcePath)) {
                continue;
            }

            $files = Finder::create()
                ->files()
                ->in($resourcePath)
                ->depth('== 0')
                ->name('*.php');

            foreach ($files as $file) {
                $class = "{$namespace}\\" . $file->getBasename('.php');

                if (! class_exists($class)) {
                    continue;
                }

                try {
                    $reflection = new ReflectionClass($class);

                    if (
                        $reflection->isSubclassOf(\Filament\Resources\Resource::class) &&
                        ! $reflection->isAbstract()
                    ) {
                        $resources[] = $class;
                    }
                } catch (\Throwable $e) {
                    Log::warning("Filament resource autoload failed for [{$class}]: {$e->getMessage()}");
                }
            }
        }

        $panel->resources($resources);
    }
}
