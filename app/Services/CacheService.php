<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheService
{
    /**
     * Cache key prefixes
     */
    const CACHE_PREFIX = 'eventhub:';
    const CONGRESS_PREFIX = self::CACHE_PREFIX . 'congress:';
    const SPEAKER_PREFIX = self::CACHE_PREFIX . 'speaker:';
    const SPONSOR_PREFIX = self::CACHE_PREFIX . 'sponsor:';
    const PAPER_PREFIX = self::CACHE_PREFIX . 'paper:';

    /**
     * Cache duration in seconds
     */
    const DURATION_SHORT = 300; // 5 minutos
    const DURATION_MEDIUM = 3600; // 1 hora
    const DURATION_LONG = 86400; // 24 horas

    /**
     * Get or cache congress data
     */
    public static function getCongress($id, $callback, $duration = self::DURATION_MEDIUM)
    {
        $key = self::CONGRESS_PREFIX . $id;
        return Cache::remember($key, $duration, $callback);
    }

    /**
     * Get or cache speakers
     */
    public static function getSpeakers($filters = [], $duration = self::DURATION_SHORT)
    {
        $key = self::SPEAKER_PREFIX . md5(serialize($filters));
        return Cache::remember($key, $duration, function () use ($filters) {
            $query = \App\Models\Speaker::where('is_active', true)
                ->with('congress');

            if (isset($filters['congress_id'])) {
                $query->where('congress_id', $filters['congress_id']);
            }

            if (isset($filters['featured'])) {
                $query->where('is_featured', true);
            }

            return $query->orderBy('sort_order')->get();
        });
    }

    /**
     * Get or cache sponsors
     */
    public static function getSponsors($filters = [], $duration = self::DURATION_SHORT)
    {
        $key = self::SPONSOR_PREFIX . md5(serialize($filters));
        return Cache::remember($key, $duration, function () use ($filters) {
            $query = \App\Models\Sponsor::where('is_active', true)
                ->with('congress');

            if (isset($filters['congress_id'])) {
                $query->where('congress_id', $filters['congress_id']);
            }

            if (isset($filters['type'])) {
                $query->where('sponsor_type', $filters['type']);
            }

            return $query->orderByRaw("FIELD(sponsor_type, 'platinum', 'gold', 'silver', 'bronze', 'partner')")
                ->orderBy('sort_order')
                ->get();
        });
    }

    /**
     * Clear cache for a congress
     */
    public static function clearCongressCache($congressId)
    {
        Cache::forget(self::CONGRESS_PREFIX . $congressId);
        Cache::tags(['congress:' . $congressId])->flush();
    }

    /**
     * Clear all cache
     */
    public static function clearAll()
    {
        Cache::flush();
    }

    /**
     * Get cache statistics
     */
    public static function getStats()
    {
        return [
            'driver' => config('cache.default'),
            'prefix' => config('cache.prefix'),
        ];
    }
}

