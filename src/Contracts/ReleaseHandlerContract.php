<?php

namespace IBroStudio\ReleaseManager\Contracts;

use IBroStudio\ReleaseManager\DtO\NewReleaseData;
use IBroStudio\ReleaseManager\DtO\ReleaseData;
use IBroStudio\ReleaseManager\DtO\VersionData;
use IBroStudio\ReleaseManager\Formatters\VersionFormatterContract;

interface ReleaseHandlerContract
{
    public function createRelease(NewReleaseData $newReleaseData): ReleaseData;

    public function fetchLastRelease(): ReleaseData;
}