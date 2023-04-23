<?php

namespace IBroStudio\ReleaseManager\Commands;

use IBroStudio\ReleaseManager\DtO\NewReleaseData;
use IBroStudio\ReleaseManager\Formatters\FullVersionFormatter;
use IBroStudio\ReleaseManager\ReleaseManager;
use IBroStudio\ReleaseManager\VersionManagers\GitLocalVersionManager;
use IBroStudio\ReleaseManager\VersionManagers\GitRemoteVersionManager;
use Illuminate\Console\Command;

class DeleteReleaseCommand extends Command
{
    public $signature = 'release:delete';

    public $description = 'Delete the last release';

    public function handle(): int
    {
        $releaseManager = ReleaseManager::use(GitRemoteVersionManager::class);

        $release =  $releaseManager->fetchLastRelease();

        if ($this->confirm("Do you really want to delete the release {$release->tag_name}?")) {
            $releaseManager->deleteRelease($release->id);

            $this->info("Release $release->tag_name was successfully deleted!");
        }

        return self::SUCCESS;
    }
}
