<?php

namespace IBroStudio\ReleaseManager\Contracts;

use IBroStudio\ReleaseManager\DtO\NewReleaseData;
use IBroStudio\ReleaseManager\DtO\ReleaseData;

interface ReleaseHandlerContract
{
    public function createRelease(NewReleaseData $newReleaseData): ReleaseData;

    public function fetchLastRelease(): ReleaseData;
}
