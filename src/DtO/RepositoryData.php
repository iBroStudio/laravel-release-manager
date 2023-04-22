<?php

namespace IBroStudio\ReleaseManager\DtO;

use Spatie\LaravelData\Data;

class RepositoryData extends Data
{
    public function __construct(
        public string $name,
        public string $owner,
        public string $branch,
    ) {
    }
}
