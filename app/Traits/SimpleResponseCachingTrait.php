<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait SimpleResponseCachingTrait
{
    public function __call($method, $parameters)
    {
        parent::__call($method, $parameters);

        if (in_array($method, ['store', 'update', 'destroy'])) {
            $this->cacheForget();
        }
    }

    protected function cacheForget(): void
    {
        Cache::forget($this->getCacheKey());
    }

    protected function getCacheKey(mixed $param = ''): string
    {
        return self::CACHE_KEY . $param;
    }

    public function clearCache(mixed $param = ''): void
    {
        $this->clearCacheByKey($this->getCacheKey($param));
    }

    public function clearCacheByKey(string $key): void
    {
        Cache::delete($key);
    }
}
