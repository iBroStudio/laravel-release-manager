<?php

namespace IBroStudio\ReleaseManager;

use IBroStudio\ReleaseManager\Commands\ReleaseManagerCommand;
use IBroStudio\ReleaseManager\Components\AppVersion;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ReleaseManagerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('release-manager')
            ->hasConfigFile()
            ->hasTranslations()
            ->hasViews()
            ->hasViewComponents('', AppVersion::class)
            //->hasViews()
            //->hasMigration('create_laravel-release-manager_table')
            ->hasCommand(ReleaseManagerCommand::class);
    }
}
