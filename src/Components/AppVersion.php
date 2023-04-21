<?php

namespace IBroStudio\ReleaseManager\Components;

use IBroStudio\ReleaseManager\Formatters\CompactVersionFormatter;
use IBroStudio\ReleaseManager\Formatters\FullVersionFormatter;
use IBroStudio\ReleaseManager\ReleaseManager;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppVersion extends Component
{
    public function __construct(
        private ReleaseManager $releaseManager,
        public ?string $formatter = null
    ) {}

    public function currentVersion(): string
    {
        $formatter = match($this->formatter) {
            'compact' => new CompactVersionFormatter,
            'full' =>  new FullVersionFormatter,
            default => new (config('release-manager.default.formatter')),
        };

        $config = collect(['versionLabel', 'displayAppLabel', 'displayLastCommit'])
            ->filter(function (string $param) {
                return $this->attributes->has(Str::snake($param, '-'));
            })
            ->mapWithKeys(function (string $param) {
                return [$param => $param === 'versionLabel' ? $this->attributes->get('version-label') : true];
            })
            ->all();

        return $this->releaseManager
            ->current()
            ->get()
            ->format(
                $formatter
                    ->config(...$config)
            );
    }

    public function render(): View
    {
        return view('release-manager::components.app-version');
    }
}