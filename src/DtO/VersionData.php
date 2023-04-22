<?php

namespace IBroStudio\ReleaseManager\DtO;

use Spatie\LaravelData\Data;

class VersionData extends Data
{
    public function __construct(
        public int $major,
        public int $minor,
        public int $patch,
        public ?string $prerelease,
        public ?string $buildmetadata,
        public ?string $commit,
    ) {
    }

    public static function fromGit(array $data, string $commit): self
    {
        return new self(
            major: $data['major'][0],
            minor: $data['minor'][0],
            patch: $data['patch'][0],
            prerelease: $data['prerelease'][0],
            buildmetadata: $data['buildmetadata'][0],
            commit: $commit
        );
    }
}
