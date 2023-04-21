<?php

namespace IBroStudio\ReleaseManager\DtO;

use Spatie\LaravelData\Data;

class FormatterConfigData extends Data
{
    public function __construct(
        public string $versionLabel,
        public bool $displayAppLabel,
        public bool $displayLastCommit,
    ) {}

    public static function fromMultiple(string $versionLabel, bool $displayAppLabel, bool $displayLastCommit): self
    {
        return new self($versionLabel, $displayAppLabel, $displayLastCommit);
    }
}