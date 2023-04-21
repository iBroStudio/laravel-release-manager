<?php

namespace IBroStudio\ReleaseManager\Formatters;

use IBroStudio\ReleaseManager\DtO\CommandsData;
use IBroStudio\ReleaseManager\DtO\FormatterConfigData;
use IBroStudio\ReleaseManager\DtO\VersionConfigData;
use IBroStudio\ReleaseManager\DtO\VersionData;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

abstract class AbstractVersionFormatter implements VersionFormatterContract
{
    protected ?FormatterConfigData $config = null;

    public function config(
        ?string $versionLabel = null,
        ?bool $displayAppLabel = null,
        ?bool $displayLastCommit = null): static
    {
        $this->config = FormatterConfigData::from(
            versionLabel: $versionLabel ?? config('release-manager.formatters.' . static::class . '.version-label'),
            displayAppLabel: $displayAppLabel ?? config('release-manager.formatters.' . static::class . '.display-app-label'),
            displayLastCommit: $displayLastCommit ?? config('release-manager.formatters.' . static::class . '.display-last-commit'),
        );

        return $this;
    }

    abstract public function format(VersionData $version): string;
}