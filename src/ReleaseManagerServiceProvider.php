<?php

namespace IBroStudio\ReleaseManager;

use IBroStudio\ReleaseManager\Commands\CurrentReleaseCommand;
use IBroStudio\ReleaseManager\Commands\DeleteReleaseCommand;
use IBroStudio\ReleaseManager\Commands\CreateReleaseCommand;
use IBroStudio\ReleaseManager\Components\AppVersion;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ReleaseManagerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('release-manager')
            ->hasConfigFile(['release-manager', 'github'])
            ->hasTranslations()
            ->hasViews()
            ->hasViewComponents('', AppVersion::class)
            //->hasMigration('create_laravel-release-manager_table')
            ->hasCommands(
                CurrentReleaseCommand::class,
                CreateReleaseCommand::class,
                DeleteReleaseCommand::class
            );
    }
}
