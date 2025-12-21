<?php

/*
 * Copyright © 2025 Ahmad SHARIF and Wael MOUSTAFA
 * All rights reserved.
 * This software is proprietary and confidential.
 * This program is confidential and proprietary to the copyright owners. Reproduction or disclosure, in whole or in part, or the
 * production of derivative works therefrom without the express permission of the copyright owners is prohibited.
 */

namespace Asharif88\FilamentPlotly;

use Asharif88\FilamentPlotly\Commands\FilamentPlotlyCommand;
use Asharif88\FilamentPlotly\Testing\TestsFilamentPlotly;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Livewire\Features\SupportTesting\Testable;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentPlotlyServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-plotly';

    public static string $viewNamespace = 'filament-plotly';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasViews()
            ->hasTranslations()
            ->hasCommands($this->getCommands());
    }

    public function packageRegistered(): void {}

    public function packageBooted(): void
    {
        Blade::componentNamespace('Asharif88\\FilamentPlotly\\Components', 'filament-plotly');
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        if (app()->runningInConsole()) {
            foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
                $this->publishes([
                    $file->getRealPath() => base_path("stubs/filament-plotly/{$file->getFilename()}"),
                ], 'filament-plotly-stubs');
            }
        }

        // Testing
        Testable::mixin(new TestsFilamentPlotly);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'asharif88/filament-plotly';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            AlpineComponent::make('filament-plotly', __DIR__ . '/../resources/dist/filament-plotly.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
            FilamentPlotlyCommand::class,
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }
}
