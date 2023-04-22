<?php

namespace IBroStudio\ReleaseManager\DtO;

use Spatie\LaravelData\Data;

class VersionConfigData extends Data
{
    public function __construct(
        public string $path,
        public CommandsData $commands,
        public string $matcher,
    ) {
    }

    public static function fromMultiple(string $path, CommandsData $commands, string $matcher): self
    {
        return new self($path, $commands, $matcher);
    }
}
