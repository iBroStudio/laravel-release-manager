<?php

namespace IBroStudio\ReleaseManager\VersionManagers;

use IBroStudio\Git\DtO\CommitData;
use IBroStudio\Git\Git;
use IBroStudio\Git\Repository;
use IBroStudio\ReleaseManager\Contracts\VersionManagerContract;
use IBroStudio\ReleaseManager\DtO\VersionData;

class GitLocalVersionManager implements VersionManagerContract
{
    private Repository $repository;

    public function __construct(
        private string $repository_path,
        private Git $git,
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

    private function retrieveVersion(): array
    {
        return $this->extractVersion(
            $this->repository->tag()->get()
        );
    }

    private function retrieveLastCommit(): CommitData
    {
        return $this->repository->commits()->last();
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
