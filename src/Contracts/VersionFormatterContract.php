<?php

namespace IBroStudio\ReleaseManager\Contracts;

use IBroStudio\ReleaseManager\DtO\VersionData;

interface VersionFormatterContract
{
    public function config(
        ?string $versionLabel = null,
        ?bool $displayAppLabel = null,
        ?bool $displayLastCommit = null): static;

    public function format(VersionData $version): string;
}
