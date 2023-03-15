<?php

namespace Inmanturbo\Delegator;

use Illuminate\Support\Facades\Event;
use Inmanturbo\Delegator\Commands\CandidatesArtisanCommand;
use Laravel\Octane\Events\RequestReceived as OctaneRequestReceived;
use Laravel\Octane\Events\RequestTerminated as OctaneRequestTerminated;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DelegatorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-delegator')
            ->hasConfigFile()
            ->hasCommand(CandidatesArtisanCommand::class);
    }

    public function packageBooted(): void
    {
        $this->app->bind(Delegator::class, fn ($app) => new Delegator($app));

        if (! isset($_SERVER['LARAVEL_OCTANE'])) {
            app(Delegator::class)->start();

            return;
        }

        Event::listen(fn (OctaneRequestReceived $requestReceived) => app(Delegator::class)->start());
        Event::listen(fn (OctaneRequestTerminated $requestTerminated) => app(Delegator::class)->end());
    }
}
