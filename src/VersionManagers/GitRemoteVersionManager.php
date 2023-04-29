<?php

namespace IBroStudio\ReleaseManager\VersionManagers;

use GrahamCampbell\GitHub\GitHubManager;
use IBroStudio\Git\DtO\CommitData;
use IBroStudio\Git\Git;
use IBroStudio\Git\Repository;
use IBroStudio\ReleaseManager\Contracts\ReleaseHandlerContract;
use IBroStudio\ReleaseManager\Contracts\VersionFormatterContract;
use IBroStudio\ReleaseManager\Contracts\VersionManagerContract;
use IBroStudio\ReleaseManager\DtO\NewReleaseData;
use IBroStudio\ReleaseManager\DtO\ReleaseData;
use IBroStudio\ReleaseManager\DtO\RepositoryData;
use IBroStudio\ReleaseManager\DtO\VersionData;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class GitRemoteVersionManager implements VersionManagerContract, ReleaseHandlerContract
{
    private Repository $repository;

    public function __construct(
        private string $repository_path,
        private Git $git,
        private GitHubManager $github,
        public ?VersionData $version = null,
    ) {
        $this->repository = $this->git->open($this->repository_path);
    }

    public function getVersion(): self
    {
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

    public function fetchLastRelease(): ReleaseData
    {
        $releases = $this->repository->releases()->all();

        if (! count($releases)) {
            dd('No releases');
        }

        return ReleaseData::from($releases[0]);
    }

    public function deleteRelease(ReleaseData $release): void
    {
        dd($release);
        $this->github
            ->repo()
            ->releases()
            ->remove(
                username: $this->repository->owner,
                repository: $this->repository->name,
                id: $id
            );
    }

    private function retrieveVersion(): array
    {
        $release = $this->fetchLastRelease();

        return $this->extractVersion($release->tag_name);
    }
/*
    private function retrieveLastCommit(): CommitData
    {
        return $this->repository->commits()->last();
    }
    */
    private function retrieveLastCommit(): CommitData
    {
        return $this->repository
            ->remote()
            ->commits()
            ->last();
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
