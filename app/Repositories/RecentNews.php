<?php

namespace App\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RecentNews
{
    /**
     * Get the recent WRMD news.
     */
    public static function collect(): Collection
    {
        return Cache::remember('recentNews', Carbon::now()->addHours(2), function () {
            try {
                $posts = Http::get('https://www.wildneighborsdp.org/stories-and-updates?category=WRMD&format=json')
                    ->collect()
                    ->get('items');

                return (new Collection($posts))
                    ->transform(function ($post) {
                        $publishedAt = Carbon::createFromTimestampMs($post['publishOn']);

                        return [
                            'id' => $post['id'],
                            'title' => $post['title'],
                            'excerpt' => strip_tags($post['excerpt']),
                            'featured_image' => $post['assetUrl'],
                            'url' => "https://www.wildneighborsdp.org{$post['fullUrl']}",
                            'author' => $post['author']['displayName'],
                            'tags' => $post['tags'] ?? [],
                            'published_at' => $publishedAt->toDateTimeString(),
                            'published_at_for_humans' => $publishedAt->toFormattedDayDateString(),
                        ];
                    });
            } catch (\Exception $e) {
                return new Collection();
            }
        });
    }
}
