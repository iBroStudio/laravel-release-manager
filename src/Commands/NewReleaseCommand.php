<?php

namespace IBroStudio\ReleaseManager\Commands;

use IBroStudio\ReleaseManager\Formatters\CompactVersionFormatter;
use IBroStudio\ReleaseManager\Formatters\FullVersionFormatter;
use IBroStudio\ReleaseManager\ReleaseManager;
use IBroStudio\ReleaseManager\VersionManagers\GitLocalVersionManager;
use Illuminate\Console\Command;

class NewReleaseCommand extends Command
{
    public $signature = 'release:new';

    public $description = 'Create a new release';

    public function handle(): int
    {
        $releaseManager = ReleaseManager::use(GitLocalVersionManager::class);

        $this->comment(
            'Current version is '
            . $releaseManager->getVersion()
                ->format(new CompactVersionFormatter)
        );

        $tag = $this->choice(
            'Choose type of release',
            [
                'Patch' => $releaseManager->getNextPatchVersion(),
                'Minor' => $releaseManager->getNextMinorVersion(),
                'Major' => $releaseManager->getNextMajorVersion(),
            ],
            null,
            $maxAttempts = null,
            $allowMultipleSelections = false
        );

        return self::SUCCESS;
    }
}
