<?php

namespace IBroStudio\ReleaseManager;

use IBroStudio\ReleaseManager\DtO\CommandsData;
use IBroStudio\ReleaseManager\DtO\VersionConfigData;
use IBroStudio\ReleaseManager\DtO\VersionData;
use IBroStudio\ReleaseManager\Formatters\FullVersionFormatter;
use IBroStudio\ReleaseManager\Formatters\VersionFormatterContract;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class ReleaseManager
{
    //$this->releaseManager->current()->get()->format();
    private ?VersionConfigData $config;

    private ?VersionData $version;

    public function current(?string $path = null): self
    {
        $this->config = VersionConfigData::from(
            path: $path ?? config('release-manager.default.git.repository-path'),
            commands: CommandsData::from(config('release-manager.git.commands.local')),
            matcher: config('release-manager.git.version-matcher')
        );

        return $this;
    }

    public function remote(?string $path = null): self
    {
        $this->config = VersionConfigData::from(
            path: $path ?? config('release-manager.default.git.repository-path'),
            commands: CommandsData::from(config('release-manager.git.commands.remote')),
            matcher: config('release-manager.git.version-matcher')
        );

        return $this;
    }

    public function get(): self
    {
        $this->version = VersionData::fromGit([
            ...$this->retrieveVersion(),
            'commit' => $this->retrieveLastCommit()
        ]);

        return $this;
    }

    public function values(): VersionData
    {
        return $this->version;
    }

    public function format(?VersionFormatterContract $formatter = null): string
    {
        return ($formatter ?? new (config('release-manager.default.formatter')))->format($this->version);
    }

    private function retrieveVersion(): array
    {
        $retrieve = Process::path($this->config->path)
            ->run($this->config->commands->version)
            ->throw();

        return $this->extractVersion($retrieve->output());
    }

    private function retrieveLastCommit(): string
    {
        $retrieve = Process::path($this->config->path)
            ->run($this->config->commands->commit)
            ->throw();

        return Str::before($retrieve->output(), "\t");
    }

    private function extractVersion(string $string): array
    {
        preg_match_all(
            $this->config->matcher,
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