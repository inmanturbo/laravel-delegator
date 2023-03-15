<?php

namespace Inmanturbo\Delegator;

use Inmanturbo\Delegator\Commands\CandidatesArtisanCommand;
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
}
