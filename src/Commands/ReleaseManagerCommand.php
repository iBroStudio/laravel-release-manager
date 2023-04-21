<?php

namespace IBroStudio\ReleaseManager\Commands;

use Illuminate\Console\Command;

class ReleaseManagerCommand extends Command
{
    public $signature = 'laravel-release-manager';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
