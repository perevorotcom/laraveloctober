<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Localization;
use Route;

class Page extends \LaraveloctoberModel
{
    use \TranslatableTrait;
    use \LongreadTrait;
    use \AttachmentsTrait;

    public $table = 'perevorot_page_page';
    public $backendModel = 'Perevorot\Page\Models\Page';

    const PAGE_TYPE_STATIC = 1;
    const PAGE_TYPE_ALIAS = 2;
    const PAGE_TYPE_EXTERNAL = 3;
    const PAGE_TYPE_ROUTE = 4;

    public $translatable = [
        ['title', 'primary' => true],
    ];

    protected $longread = [
        'longread',
    ];

    public function scopeEnabled($query)
    {
        return $query->where('is_disabled', false)->orderBy('nest_left', 'ASC');
    }

    public function scopeMenuEnabled($query)
    {
        return $query->enabled()->where('is_hidden', false);
    }

    public function scopeMenuDepth($query, $depth, $level)
    {
        if (!empty($level)) {
            $query->where('nest_depth', '<', $depth + $level);
        }

        $query->where('nest_depth', '>=', $depth);

        return $query;
    }

    public function alias()
    {
        return $this->hasOne('Perevorotcom\Laraveloctober\Models\Page', 'id', 'alias_page_id');
    }

    public function route()
    {
        return $this->morphTo();
    }

    public function getHasChildrenAttribute($value)
    {
        return $this->nest_right > $this->nest_left + 1;
    }

    public function getChildrenAttribute($value)
    {
        return $this->hasChildren ? $this->menuEnabled()->where('nest_left', '>', $this->nest_left)->where('nest_right', '<', $this->nest_right)->get() : [];
    }

    public function getAllChildrenAttribute($value)
    {
        return $this->hasChildren ? $this->where('nest_left', '>', $this->nest_left)->where('nest_right', '<', $this->nest_right)->get() : [];
    }

    public function getUrlAttribute($value)
    {
        $url = '';

        switch ($this->type) {
            case self::PAGE_TYPE_STATIC:
                $url = !empty($this->attributes['url']) ? Localization::getLocalizedURL(null, $this->attributes['url']) : '';
                break;

            case self::PAGE_TYPE_ALIAS:
                if ($this->id != $this->alias_page_id && !empty($this->alias)) {
                    $url = $this->alias->url;
                }
                break;

            case self::PAGE_TYPE_EXTERNAL:
                $url = $this->url_external;
                break;

            case self::PAGE_TYPE_ROUTE:
                if (Route::current() && $this->route_name && !$this->route_id) {
                    $parameters = Route::current()->parameters;

                    if (strpos(Route::getRoutes()->getByName($this->route_name)->uri, '{') === false) {
                        $url = localized_route_url($this->route_name);
                    } elseif (Route::currentRouteName() == $this->route_name && !empty($parameters)) {
                        $url = localized_route_url($this->route_name, (object) $parameters);
                    } else {
                        $url = localized_route_url($this->route_name, (object) []);
                    }
                } elseif ($this->route_type && $this->route_id) {
                    $url = localized_route_url($this->route_type, [$this->route]);
                } elseif ($this->route_type) {
                    $url = localized_route_url($this->route_type);
                } else {
                    $url = '';
                }

                break;
        }

        return $url;
    }

    public function getIsActiveAttribute($value)
    {
        if (!empty($this->attributes['active'])) {
            return true;
        }

        $fullUrl = request()->fullUrl();

        return $this->url == $fullUrl || Arr::first($this->allChildren, function ($child) {
            return $child->isActive;
        });
    }

    public function getPathAttribute()
    {
        $defaultLocale = Localization::getDefaultLocale();
        $locales = Localization::getSupportedLanguagesKeys();

        if (Localization::hideDefaultLocaleInURL()) {
            $locales = array_diff($locales, [$defaultLocale]);
        }

        $path = trim(request()->path(), '/').'/';

        foreach ($locales as $locale) {
            if (Str::startsWith($path, $locale.'/')) {
                $path = substr($path, 3);
            }
        }

        $path = trim($path, '/');

        return '/'.$path;
    }

    public function getCurrentAttribute()
    {
        $slug = !empty($this->route->parameters['slug']) ? $this->route->parameters['slug'] : '';

        $page = Page::enabled()->where(function ($q) use ($slug) {
            $q->where('route_name', Route::currentRouteName());
            $q->whereRaw('(route_id '.($slug ? '="'.$slug.'"' : ' IS NULL OR route_id=0').')');
        })->orWhere(function ($q) {
            $q->whereRaw('BINARY url=?', [$this->path]);
        })->first();

        return $page ? $page : false;
    }
}
