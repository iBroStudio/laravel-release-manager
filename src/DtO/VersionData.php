<?php

namespace IBroStudio\ReleaseManager\DtO;

use Spatie\LaravelData\Data;

class VersionData extends Data
{
    public function __construct(
        public string $label,
        public int $major,
        public int $minor,
        public int $patch,
        public ?string $prerelease,
        public ?string $buildmetadata,
        public ?string $commit,
    ) {}

    public static function fromGit(array $data): self
    {
        return new self(
            label: __('version-prepend'),
            major: $data['major'][0],
            minor: $data['minor'][0],
            patch: $data['patch'][0],
            prerelease: $data['prerelease'][0],
            buildmetadata: $data['buildmetadata'][0],
            commit: $data['commit']
        );
    }
}