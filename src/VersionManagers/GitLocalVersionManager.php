<?php

namespace IBroStudio\ReleaseManager\VersionManagers;

use IBroStudio\ReleaseManager\Contracts\VersionManagerContract;
use IBroStudio\ReleaseManager\DtO\CommandsData;
use IBroStudio\ReleaseManager\DtO\VersionConfigData;
use IBroStudio\ReleaseManager\DtO\VersionData;
use IBroStudio\ReleaseManager\Formatters\VersionFormatterContract;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class GitLocalVersionManager implements VersionManagerContract
{
    public function __construct(
        private ?string $path = null,
        public ?VersionData $version = null,
    ) {}

    public function getVersion(?string $path = null): self
    {
        $this->path = $path ?? config('release-manager.default.git.repository-path');

        $this->version = VersionData::fromGit(
            $this->retrieveVersion(),
            $this->retrieveLastCommit()
        );

        return $this;
    }

    public function values(): VersionData
    {
        return $this->version;
    }

    private function retrieveVersion(): array
    {
        $retrieve = Process::path($this->path)
            ->run(config('release-manager.git.commands.local.version'))
            ->throw();

        return $this->extractVersion($retrieve->output());
    }

    private function retrieveLastCommit(): string
    {
        $retrieve = Process::path($this->path)
            ->run(config('release-manager.git.commands.local.commit'))
            ->throw();

        return Str::before($retrieve->output(), "\t");
    }

    private function extractVersion(string $string): array
    {
        preg_match_all(
            config('release-manager.git.version-matcher'),
            $string,
            $matches
        );

        if (empty($matches[0])) {
            dd('Unable to find git tags');
//            throw new GitTagNotFound('Unable to find git tags in this repository that matches the git.version.matcher pattern in version.yml');
        }

        return $matches;
    }
}