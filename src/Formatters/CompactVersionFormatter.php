<?php

namespace IBroStudio\ReleaseManager\Formatters;

use IBroStudio\ReleaseManager\DtO\VersionData;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class CompactVersionFormatter extends AbstractVersionFormatter
{
    public function format(VersionData $version): string
    {
        if (is_null($this->config)) {
            $this->config();
        }

        return Str::of($this->config->versionLabel)
            ->when($this->config->displayAppLabel, function (Stringable $string) {
                return $string
                    ->prepend(' ')
                    ->prepend(__(config('release-manager.app-label')));
            })
            ->append($version->major)
            ->append('.')
            ->append($version->minor)
            ->append('.')
            ->append($version->patch)
            ->when($version->prerelease, function (Stringable $string) use ($version) {
                return $string->append(' - ')
                    ->append($version->prerelease)
                    ->append(' ')
                    ->append($version->buildmetadata);
            })
            ->when($this->config->displayLastCommit && $version->commit,
                function (Stringable $string) use ($version) {
                    return $string
                        ->append('-')
                        ->append(
                            Str::substr($version->commit->hash, 0, config('release-manager.git.commit-length'))
                        );
                }
            )
            ->value();
    }
}
