<?php

namespace IBroStudio\ReleaseManager\DtO;

use Spatie\LaravelData\Data;

class ReleaseData extends Data
{
    public function __construct(
        public int $id,
        public string $tag_name,
        public string $name,
        public string $url,
    ) {
    }
}
