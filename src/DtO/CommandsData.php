<?php

namespace IBroStudio\ReleaseManager\DtO;

use Spatie\LaravelData\Data;

class CommandsData extends Data
{
    public function __construct(
        public string $version,
        public string $commit,
    ) {}
}