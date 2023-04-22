<?php

namespace IBroStudio\ReleaseManager\Contracts;

use IBroStudio\ReleaseManager\DtO\VersionData;
use IBroStudio\ReleaseManager\Formatters\VersionFormatterContract;

interface VersionManagerContract
{
    public function getVersion(?string $path = null): self;
    public function values(): VersionData;
}