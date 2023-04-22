<?php

namespace IBroStudio\ReleaseManager\Commands;

use IBroStudio\ReleaseManager\Formatters\FullVersionFormatter;
use IBroStudio\ReleaseManager\ReleaseManager;
use Illuminate\Console\Command;

class CurrentReleaseCommand extends Command
{
    public $signature = 'release:current';

    public $description = 'Get the current version';

    public function __construct(
        private ReleaseManager $releaseManager
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info(
            $this->releaseManager->current()->get()->format(new FullVersionFormatter)
        );

        return self::SUCCESS;
    }
}
