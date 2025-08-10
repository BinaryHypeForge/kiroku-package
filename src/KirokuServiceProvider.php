<?php

namespace BinaryHype\Kiroku;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use BinaryHype\Kiroku\Commands\KirokuTestCommand;

class KirokuServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('kiroku')
            ->hasConfigFile()
//            ->hasViews()
//            ->hasMigration('create_kiroku_table')
            ->hasCommand(KirokuTestCommand::class);
    }
}
