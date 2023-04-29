<?php

namespace IBroStudio\ReleaseManager\Contracts;

use IBroStudio\ReleaseManager\DtO\VersionData;

interface VersionManagerContract
{
    public function getVersion(): self;
    public function values(): VersionData;
}
