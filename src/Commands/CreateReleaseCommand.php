<?php

namespace IBroStudio\ReleaseManager\Commands;

use IBroStudio\ReleaseManager\DtO\NewReleaseData;
use IBroStudio\ReleaseManager\Formatters\CompactVersionFormatter;
use IBroStudio\ReleaseManager\Formatters\FullVersionFormatter;
use IBroStudio\ReleaseManager\ReleaseManager;
use IBroStudio\ReleaseManager\VersionManagers\GitLocalVersionManager;
use IBroStudio\ReleaseManager\VersionManagers\GitRemoteVersionManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class CreateReleaseCommand extends Command
{
    public $signature = 'release:create';

    public $description = 'Create a new release';

    public function handle(): int
    {
        /*
        ReleaseManager::use(GitRemoteVersionManager::class)
            ->createRelease(
                NewReleaseData::from([
                    'version' => 'v0.1.2',
                    'name' => 'Release manager test#1'
                ])
            );
*/
        $releaseManager = ReleaseManager::use(GitLocalVersionManager::class);

        $this->comment(
            'Current version is '
            . $releaseManager->getVersion()
                ->format(new CompactVersionFormatter)
        );

        $releases = [
            'Patch' => $releaseManager->getNextPatchVersion(),
            'Minor' => $releaseManager->getNextMinorVersion(),
            'Major' => $releaseManager->getNextMajorVersion(),
        ];

        $version = $this->choice(
            'Choose type of release',
            $releases,
            null,
            $maxAttempts = null,
            $allowMultipleSelections = false
        );

        $name = $this->ask(
            question: 'Enter the release name:',
            default: 'Release ' . $releases[$version]
        );

        $this->comment(
            'New version will be '
            . $releases[$version]
            . ' '
            . "'$name'" ?? null
        );

        if ($this->confirm('Do you validate?')) {
            $create = ReleaseManager::use(GitRemoteVersionManager::class)
                ->createRelease(
                    NewReleaseData::from([
                        'version' => $releases[$version],
                        'name' => $name
                    ])
                );

            $this->info("Release {$create->tag_name} was successfully created!");
            $this->info("Url: {$create->url}");
        }

        return self::SUCCESS;
    }

    public function gitPull(?string $path = null): void
    {
        Process::path($path ?? config('release-manager.default.git.repository-path'))
            ->run('git pull origin main')
            ->throw();
    }
}
