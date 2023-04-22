<?php

namespace IBroStudio\ReleaseManager;

use GrahamCampbell\GitHub\GitHubManager;
use IBroStudio\ReleaseManager\Contracts\VersionManagerContract;
use IBroStudio\ReleaseManager\DtO\CommandsData;
use IBroStudio\ReleaseManager\DtO\VersionConfigData;
use IBroStudio\ReleaseManager\DtO\VersionData;
use IBroStudio\ReleaseManager\Formatters\CompactVersionFormatter;
use IBroStudio\ReleaseManager\Formatters\VersionFormatterContract;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class ReleaseManager
{
    private function __construct(
        private string                 $driver,
        private VersionManagerContract $versionManager,
    ) {}

    public static function use(
        string $driver,
    ): static {
        return new static(
            driver: $driver,
            versionManager: app()->makeWith($driver, ['version' => VersionData::optional(null)]),
        );
    }

    public function getVersion(?string $path = null): self
    {
        $this->versionManager->getVersion($path);

        return $this;
    }

    public function properties(): VersionData
    {
        return $this->versionManager->version;
    }

    public function format(?VersionFormatterContract $formatter = null): string
    {
        return ($formatter ?? new (config('release-manager.default.formatter')))
            ->format($this->versionManager->version);
    }

    public function getNextPatchVersion(?string $path = null): string
    {
        if (is_null($this->versionManager->version)) {
            $this->versionManager->getVersion($path);
        }

        return (new CompactVersionFormatter)
            ->config(
                versionLabel: 'v',
                displayAppLabel: false,
                displayLastCommit: false
            )
            ->format(
                VersionData::from([
                    'major' => $this->versionManager->version->major,
                    'minor'=> $this->versionManager->version->minor,
                    'patch'=> $this->versionManager->version->patch + 1,
                ])
            );
    }

    public function getNextMinorVersion(?string $path = null): string
    {
        if (is_null($this->versionManager->version)) {
            $this->versionManager->getVersion($path);
        }

        return (new CompactVersionFormatter)
            ->config(
                versionLabel: 'v',
                displayAppLabel: false,
                displayLastCommit: false
            )
            ->format(
                VersionData::from([
                    'major' => $this->versionManager->version->major,
                    'minor'=> $this->versionManager->version->minor + 1,
                    'patch'=> 0,
                ])
            );
    }

    public function getNextMajorVersion(?string $path = null): string
    {
        if (is_null($this->versionManager->version)) {
            $this->versionManager->getVersion($path);
        }

        return (new CompactVersionFormatter)
            ->config(
                versionLabel: 'v',
                displayAppLabel: false,
                displayLastCommit: false
            )
            ->format(
                VersionData::from([
                    'major' => $this->versionManager->version->major + 1,
                    'minor'=> 0,
                    'patch'=> 0,
                ])
            );
    }
}

/*
repo ibone local

create release => new version from release manager
github webhook ?

repo fork local => git pull --rebase origin upstream
repo fork local => git pull origin main
repo fork local => git push origin main
=> ploi webhook to deploy new version on production

themes
repo local
create release => new version from release manager
github webhook
repo fork local => composer update theme
repo fork local => git push origin main
=> ploi webhook to deploy new version on production
 */
