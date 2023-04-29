<?php

namespace IBroStudio\ReleaseManager\Commands;

use IBroStudio\ReleaseManager\Formatters\FullVersionFormatter;
use IBroStudio\ReleaseManager\ReleaseManager;
use IBroStudio\ReleaseManager\VersionManagers\GitLocalVersionManager;
use IBroStudio\ReleaseManager\VersionManagers\GitRemoteVersionManager;
use Illuminate\Console\Command;

class CurrentReleaseCommand extends Command
{
    public $signature = 'release:current';

    public $description = 'Get the current version';

    public function handle(): int
    {
        $this->info(
            ReleaseManager::use(GitRemoteVersionManager::class)
                ->getVersion()
                ->format(new FullVersionFormatter)
        );

        return self::SUCCESS;
    }
}
