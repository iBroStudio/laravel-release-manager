<?php

namespace IBroStudio\ReleaseManager\VersionManagers;

use GrahamCampbell\GitHub\GitHubManager;
use IBroStudio\ReleaseManager\Contracts\VersionManagerContract;
use IBroStudio\ReleaseManager\DtO\RepositoryData;
use IBroStudio\ReleaseManager\DtO\VersionData;
use IBroStudio\ReleaseManager\Formatters\VersionFormatterContract;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class GitRemoteVersionManager implements VersionManagerContract
{
    private ?RepositoryData $repositoryData = null;

    public function __construct(
        private GitHubManager $github,
        private ?string $path = null,
        public ?VersionData $version = null,
    )
    {

    }

    public function getVersion(?string $path = null): self
    {
        $this->initRepoData($path);

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

    public function format(?VersionFormatterContract $formatter = null): string
    {
        return ($formatter ?? new (config('release-manager.default.formatter')))->format($this->version);
    }

    private function initRepoData(?string $path = null): void
    {
        $retrieve = Process::path($path ?? config('release-manager.default.git.repository-path'))
            ->run('git config --local -l')
            ->throw();

        $gitConfig = collect(explode("\n", $retrieve->output()))
            ->filter(function (string $line) {
                return Str::contains($line, 'remote.origin.url');
            })
            ->first();

        preg_match('/:(?<username>.*)\/(?<repository>.*)\.git/', $gitConfig, $matches);

        $repository = $this->github
            ->repo()
            ->show($matches['username'], $matches['repository']);

        $this->repository = RepositoryData::from([
            'name' => $repository['name'],
            'owner' => $repository['owner']['login'],
            'branch' => $repository['default_branch']
        ]);
    }

    private function retrieveVersion(): array
    {
        $releases = $this->github
            ->repo()
            ->releases()
            ->all($this->repository->owner, $this->repository->name);

        if (! count($releases)) {
            dd('No releases');
        }

        return $this->extractVersion($releases[0]['tag_name']);
    }

    private function retrieveLastCommit(): string
    {
        $commits = $this->github
            ->repo()
            ->commits()
            ->all($this->repository->owner, $this->repository->name, array('sha' => $this->repository->branch));

        return $commits[0]['sha'];
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