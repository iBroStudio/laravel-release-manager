<?php

namespace IBroStudio\ReleaseManager\VersionManagers;

use GrahamCampbell\GitHub\GitHubManager;
use IBroStudio\ReleaseManager\Contracts\ReleaseHandlerContract;
use IBroStudio\ReleaseManager\Contracts\VersionManagerContract;
use IBroStudio\ReleaseManager\DtO\NewReleaseData;
use IBroStudio\ReleaseManager\DtO\ReleaseData;
use IBroStudio\ReleaseManager\DtO\RepositoryData;
use IBroStudio\ReleaseManager\DtO\VersionData;
use IBroStudio\ReleaseManager\Exceptions\BadVersionManagerException;
use IBroStudio\ReleaseManager\Formatters\VersionFormatterContract;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class GitRemoteVersionManager implements VersionManagerContract, ReleaseHandlerContract
{
    private ?RepositoryData $repository = null;

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
        return ($formatter ?? new (config('release-manager.default.formatter')))
            ->format($this->version);
    }

    public function createRelease(NewReleaseData $newReleaseData, ?string $path = null): ReleaseData
    {
        $this->initRepoData($path);

        $release = $this->github
            ->repo()
            ->releases()
            ->create(
                username: $this->repository->owner,
                repository: $this->repository->name,
                params: [
                    'tag_name' => $newReleaseData->version,
                    'name' => $newReleaseData->name,
                    'generate_release_notes' => config('release-manager.automatically_generate_release_notes')
                ]
            );

        return ReleaseData::from($release);
    }

    public function fetchLastRelease(?string $path = null): ReleaseData
    {
        $this->initRepoData($path);

        $releases = $this->github
            ->repo()
            ->releases()
            ->all(
                username: $this->repository->owner,
                repository: $this->repository->name
            );

        if (! count($releases)) {
            dd('No releases');
        }

        return ReleaseData::from($releases[0]);
    }

    public function deleteRelease(int $id): void
    {
        $this->github
            ->repo()
            ->releases()
            ->remove(
                username: $this->repository->owner,
                repository: $this->repository->name,
                id: $id
            );
    }

    private function initRepoData(?string $path = null): void
    {
        if (is_null($this->repository)) {
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
                ->show(
                    username: $matches['username'],
                    repository: $matches['repository']
                );

            $this->repository = RepositoryData::from([
                'name' => $repository['name'],
                'owner' => $repository['owner']['login'],
                'branch' => $repository['default_branch']
            ]);
        }
    }

    private function retrieveVersion(): array
    {
        $release = $this->fetchLastRelease();

        return $this->extractVersion($release->tag_name);
    }

    private function retrieveLastCommit(): string
    {
        $commits = $this->github
            ->repo()
            ->commits()
            ->all(
                username: $this->repository->owner,
                repository: $this->repository->name,
                params: [
                    'sha' => $this->repository->branch
                ]
        );

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