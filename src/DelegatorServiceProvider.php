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
        $this->app->bind(Delegation::class, fn ($app) => new Delegation($app));

        if (! isset($_SERVER['LARAVEL_OCTANE'])) {
            app(Delegation::class)->start();

            return;
        }

        Event::listen(fn (OctaneRequestReceived $requestReceived) => app(Delegation::class)->start());
        Event::listen(fn (OctaneRequestTerminated $requestTerminated) => app(Delegation::class)->end());
    }
}
