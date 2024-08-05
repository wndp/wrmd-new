<?php

namespace App\Support;

use App\Models\Extension;
use ErrorException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use League\CommonMark\CommonMarkConverter;

class ExtensionManager
{
    /**
     * Find an extension by its namespace.
     */
    public function findByNamespace(string $namespace): Extension
    {
        return Extension::whereNamespace(Str::studly($namespace))->firstOrFail();
    }

    /**
     * Get an extensions markdown details.
     */
    public function getDetails(string $namespace): string
    {
        $readme = app_path('/Extensions/'.Str::studly($namespace).'/Foundation/readme.md');

        if (! file_exists($readme)) {
            return '';
        }

        $converter = new CommonMarkConverter([
            'allow_unsafe_links' => false,
        ]);

        return new HtmlString($converter->convertToHtml(file_get_contents($readme)));
    }

    /**
     * Get all the extensions.
     */
    public function getAll(): Collection
    {
        return Cache::remember('allExtensions', 1440, function () {
            return Extension::orderBy('name')->get();
        });
    }

    /**
     * Get all the public extensions.
     */
    public function getPublic(): Collection
    {
        return Cache::remember('publicExtensions', 1440, function () {
            return Extension::where('is_private', false)->orderBy('name')->get();
        });
    }

    /**
     * Get the 4 most popular extensions.
     */
    public function getMostPopular(): Collection
    {
        return Extension::select('extensions.*')
            ->join('account_extension', 'extensions.id', '=', 'account_extension.extension_id')
            ->where('is_private', false)
            ->groupBy('extension_id')
            ->orderByRaw('count(*) desc')
            ->limit(4)
            ->get();
    }

    /**
     * Get an accounts activated extensions.
     */
    public function getActivated(Team $team): Collection
    {
        return Cache::remember('activatedExtensions.'.$team->id, 30, function () use ($team) {
            return $team->extensions()->orderBy('name')->get();
        });
    }

    /**
     * Activate an extension.
     */
    public function activate(Team $team, int $extensionId): void
    {
        if (! $this->isActivated(Extension::findOrFail($extensionId)->namespace, $team)) {
            $team->extensions()->attach($extensionId);
        }

        Cache::clear('activatedExtensions.'.$team->id);

        $this->activateDependents($team, $extensionId);
    }

    /**
     * Deactivate an extension.
     *
     *
     * @throws \DomainException
     */
    public function deactivate(Team $team, int $extensionId): void
    {
        $activatedParents = Extension::whereJsonContains('dependents', $extensionId)
            ->get()
            ->filter(function ($extension) use ($team) {
                return $this->isActivated($extension->namespace, $team);
            });

        throw_if(
            $activatedParents->isNotEmpty(),
            \DomainException::class,
            'Can not deactivate extension because of active parent: '.$activatedParents->pluck('name')->implode(', ')
        );

        $team->extensions()->detach($extensionId);

        Cache::forget('activatedExtensions.'.$team->id);

        $this->deactivateDependents($team, $extensionId);
    }

    /**
     * Deactivate all extensions.
     */
    public function deactivateAll(Team $team): void
    {
        $team->extensions()->detach();
    }

    /**
     * Determine if the provided extension namespace is activated by the provided account.
     */
    public function isActivated(string $namespace, Team $team = null): bool
    {
        try {
            $team = $team instanceof Account ? $team : Auth::user()->currentAccount;
        } catch (ErrorException $e) {
            return false;
        }

        return $this->getActivated($team)->contains('namespace', $namespace);
    }

    /**
     * Activate an extensions dependencies.
     */
    private function activateDependents(Team $team, int $parentExtensionId): void
    {
        foreach (Arr::wrap(Extension::findOrFail($parentExtensionId)->dependents) as $extensionId) {
            $this->activate($team, $extensionId);
        }
    }

    /**
     * Deactivate an extensions dependencies.
     */
    private function deactivateDependents(Team $team, int $parentExtensionId): void
    {
        foreach (Arr::wrap(Extension::findOrFail($parentExtensionId)->dependents) as $extensionId) {
            $this->deactivate($team, $extensionId);
        }
    }

    /**
     * Bootstrap all the extensions.
     */
    public static function bootstrapAll(): void
    {
        app(self::class)->getAll()->each->register();
    }

    /**
     * Bootstrap all the public extensions.
     */
    public static function bootstrapPublic(): void
    {
        app(self::class)->getPublic()->each->register();
    }

    /**
     * Bootstrap an accounts activated extensions.
     */
    public static function bootstrap(Team $team): void
    {
        app(self::class)->getActivated($team)->each->register();
    }
}
