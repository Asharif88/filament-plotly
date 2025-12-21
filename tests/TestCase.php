<?php

namespace Asharif88\FilamentPlotly\Tests;

use Asharif88\FilamentPlotly\FilamentPlotlyServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        static::$latestResponse = null;

        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            FilamentPlotlyServiceProvider::class,
            ActionsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            SchemasServiceProvider::class,
            SupportServiceProvider::class,
            WidgetsServiceProvider::class,
            LivewireServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('view.paths', [
            __DIR__ . '/views',
            resource_path('views'),
        ]);

        $app['config']->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('session.driver', 'array');
    }
}
