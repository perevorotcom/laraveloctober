<?php

namespace Perevorotcom\Laraveloctober\Services;

use Illuminate\Http\Request;
use Spatie\ResponseCache\CacheProfiles\CacheProfile;
use Spatie\ResponseCache\Hasher\RequestHasher;

class ResponceCacheHasher implements RequestHasher
{
    protected $cacheProfile;

    public function __construct(CacheProfile $cacheProfile)
    {
        $this->cacheProfile = $cacheProfile;
    }

    public function getHashFor(Request $request): string
    {
        $locale = app()->getLocale();

        return 'responsecache-'.md5(
            "{$request->getHost()}-{$request->getRequestUri()}-{$request->getMethod()}-{$locale}/".$this->cacheProfile->useCacheNameSuffix($request)
        );
    }
}
