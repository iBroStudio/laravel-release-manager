<?php

namespace IBroStudio\ReleaseManager\Formatters;

use IBroStudio\ReleaseManager\DtO\VersionData;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class FullVersionFormatter extends AbstractVersionFormatter
{
    public function format(VersionData $version): string
    {
        return Str::of(config('release-manager.formatters.'.self::class.'.version-label'))
            ->when(config('release-manager.formatters.'.self::class.'.display-app-label'), function (Stringable $string) {
                return $string
                    ->prepend(' ')
                    ->prepend(__(config('release-manager.app-label')));
            })
            ->append(' ')
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
            ->when(config('release-manager.formatters.'.self::class.'.display-last-commit') && $version->commit,
                function (Stringable $string) use ($version) {
                    return $string->append(' (')
                        ->append(__('commit-label'))
                        ->append(' ')
                        ->append(
                            Str::substr($version->commit, 0, config('release-manager.git.commit-length'))
                        )
                        ->append(')');
                }
            )
            ->value();
    }
}
