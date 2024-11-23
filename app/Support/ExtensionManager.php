<?php

namespace App\Support;

use App\Enums\Extension;
use App\Models\Team;
use Carbon\Carbon;
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
     * Get all the extensions.
     */
    // public static function getAll(): Collection
    // {
    //     return Extension::cases();
    // }

    /**
     * Get all the public extensions.
     */
    public static function getPublic(): array
    {
        return Arr::where(Extension::cases(), fn ($extension) => $extension->public());
    }

    /**
     * Get all the public extensions.
     */
    public static function getNonPublic(): array
    {
        return Arr::where(Extension::cases(), fn ($extension) => ! $extension->public());
    }

    /**
     * Get the 4 most popular extensions.
     */
    public static function getMostPopular(): Collection
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
    public static function getActivated(Team $team): Collection
    {
        return Cache::remember(
            'activatedExtensions.'.$team->id,
            Carbon::now()->addMinutes(30),
            fn () => $team->extensions
        );
    }

    /**
     * Activate an extension.
     */
    public static function activate(Team $team, Extension $extension, bool $activateDependents = true): void
    {
        if (! static::isActivated($extension, $team)) {
            $team->extensions()->create([
                'extension' => $extension->value,
            ]);
        }

        Cache::clear('activatedExtensions.'.$team->id);

        if ($activateDependents) {
            static::activateDependents($team, $extension);
        }
    }

    /**
     * Deactivate an extension.
     *
     *
     * @throws \DomainException
     */
    public static function deactivate(Team $team, Extension $extension): void
    {
        $activatedParents = Collection::make(Extension::cases())
            ->filter(
                fn ($filterExtension) => static::isActivated($filterExtension, $team) && in_array($extension, $filterExtension->dependencies())
            );

        throw_if(
            $activatedParents->isNotEmpty(),
            \DomainException::class,
            __('Can not deactivate extension because of active parent extension: :extension', [
                'extension' => $activatedParents->map(fn ($extension) => $extension->label())->implode(', ')
            ])
        );

        $team->extensions()->where('extension', $extension->value)->delete();

        Cache::forget('activatedExtensions.'.$team->id);

        static::deactivateDependents($team, $extension);
    }

    /**
     * Deactivate all extensions.
     */
    // public static function deactivateAll(Team $team): void
    // {
    //     $team->extensions()->detach();
    // }

    /**
     * Determine if the provided extension slug is activated by the provided account.
     */
    public static function isActivated(Extension $extension, Team $team = null): bool
    {
        try {
            $team = $team instanceof Team ? $team : Auth::user()->currentTeam;
        } catch (ErrorException $e) {
            return false;
        }

        return static::getActivated($team)->contains('extension', $extension->value);
    }

    /**
     * Activate an extensions dependencies.
     */
    private static function activateDependents(Team $team, Extension $parentExtension): void
    {
        foreach ($parentExtension->dependencies() as $extension) {
            static::activate($team, $extension);
        }
    }

    /**
     * Deactivate an extensions dependencies.
     */
    private static function deactivateDependents(Team $team, Extension $parentExtension): void
    {
        foreach ($parentExtension->dependencies() as $extension) {
            static::deactivate($team, $extension);
        }
    }
}
