<?php

namespace Perevorotcom\Laraveloctober\Classes;

use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\SEOTools;
use Blade;
use Illuminate\Support\Str;
use Localization;
use Perevorotcom\Laraveloctober\Models\SeoExternal;
use Perevorotcom\Laraveloctober\Models\SeoTags;
use Request;

class SEO extends SEOTools
{
    private $external = [
        'head' => [],
        'body_top' => [],
        'body_bottom' => [],
        'h1' => [],
    ];

    private $route;

    public $possibleUrl;
    public $templateData = [];

    public function head()
    {
        $this->parseExternal();
        $this->parseTags();

        return SEOTools::generate().$this->output('head');
    }

    public function bodyTop()
    {
        return $this->output('body_top');
    }

    public function bodyBottom()
    {
        return $this->output('body_bottom');
    }

    public function h1()
    {
        $this->parseTags();
        
        return $this->output('h1');
    }

    private function parseTags()
    {
        if (!$this->route) {
            $this->route = !empty(Request::route()) ? Request::route()->getName() : null;
        }

        $tags = SeoTags::where(function ($q) {
            $q->where(function ($q) {
                if ($this->route) {
                    $q->where('seo_url_type', 0);
                    $q->where(function ($q) {
                        $q->where('route', $this->route);
                        $q->orWhere('route', '');
                    });
                }
            })->orWhere(function ($q) {
                $q->where('seo_url_type', 1);
                $q->whereIn('url_mask', $this->getPossibleUrl());
            });
        })->where('is_active', '=', true)->orderByRaw("FIELD(url_mask, '".implode("', '", $this->getPossibleUrl())."')")->get();

        $url = Request::path();

        $url = Request::path();
        $url = '/'.trim($url, '/');

        if (Localization::getDefaultLocale() != Localization::getCurrentLocale()) {
            $url = substr($url, 3);
        }

        foreach ($tags as $item) {
            if ($item->seo_url_type == 1 && $item->url_mask == $url) {
                $tag = $item;
            }
        }

        if (empty($tag)) {
            $tag = $tags->first();
        }

        if (!empty($tag)) {
            if (!empty(trim($tag->title))) {
                if (config('seotools.clearDefaults', false)) {
                    config(['seotools.meta.defaults.title' => false]);
                }

                SEOMeta::setTitle($this->parseTemplate($tag->title));
            }

            if (!empty(trim($tag->description))) {
                if (config('seotools.clearDefaults', false)) {
                    config(['seotools.meta.defaults.description' => false]);
                }
                SEOMeta::setDescription($this->parseTemplate($tag->description));
            }

            $keywords = !empty($tag->keywords) ? explode(',', $this->parseTemplate($tag->keywords)) : [];
            $keywords = array_map('trim', $keywords);

            if (!empty($keywords)) {
                if (config('seotools.clearDefaults', false)) {
                    config(['seotools.meta.defaults.keywords' => false]);
                }

                SEOMeta::setKeywords($keywords);
            }

            if (!empty(trim($tag->canonical))) {
                SEOMeta::setCanonical($tag->canonical);
            }

            if (!empty(trim($tag->og_title))) {
                if (config('seotools.clearDefaults', false)) {
                    config(['seotools.opengraph.defaults.title' => false]);
                }

                SEOTools::opengraph()->setTitle($this->parseTemplate($tag->og_title));
            }

            if (!empty(trim($tag->og_description))) {
                if (config('seotools.clearDefaults', false)) {
                    config(['seotools.opengraph.defaults.og_description' => false]);
                }

                SEOTools::opengraph()->setDescription($this->parseTemplate($tag->og_description));
            }

            if ($tag->image) {
                $image = $this->parseTemplate($tag->image);

                if (!Str::startsWith($image, 'http')) {
                    $image = config('app.url').$image;
                }

                SEOTools::opengraph()->addImage($image);
            } elseif ($tag->og_image) {
                SEOTools::opengraph()->addImage($tag->og_image->path);
            }

            if (!empty(trim($tag->meta_tags))) {
                array_push($this->external['head'], $tag->meta_tags);
            }

            if (!empty($tag->h1)) {
                array_push($this->external['h1'], $this->parseTemplate($tag->h1));
            }
        }
    }

    private function parseExternal()
    {
        if (!$this->route) {
            $this->route = !empty(Request::route()) ? Request::route()->getName() : null;
        }

        $external = SeoExternal::enabled()->where(function ($q) {
            $q->where(function ($q) {
                if ($this->route) {
                    $q->where('seo_url_type', 0);
                    $q->where(function ($q) {
                        $q->where('route', $this->route);
                        $q->orWhere('route', '');
                    });
                }
            })->orWhere(function ($q) {
                $q->where('seo_url_type', 1);
                $q->whereIn('url_mask', $this->getPossibleUrl());
            });
        })->get();

        if ($external) {
            foreach ($external as $item) {
                foreach (['head', 'body_top', 'body_bottom'] as $type) {
                    if (!empty(trim($item->{$type}))) {
                        array_push($this->external[$type], trim($item->{$type}));
                    }
                }
            }
        }
    }

    public function setRoute($routeName)
    {
        $this->route = $routeName;
    }

    public function setData($data)
    {
        $this->templateData = $data;
    }

    private function output($type)
    {
        return implode('', $this->external[$type]);
    }

    private function getPossibleUrl()
    {
        if ($this->possibleUrl) {
            return $this->possibleUrl;
        }

        $url = Request::path();
        $url = '/'.trim($url, '/');

        if (Localization::getDefaultLocale() != Localization::getCurrentLocale()) {
            $url = substr($url, 3);
        }

        $urls = [$url];
        $segments = preg_split("/[\/,-]+/", $url);
        $str = str_split($url);

        $dividers = [
            '/' => [],
            '-' => [],
        ];

        foreach ($str as $k => $one) {
            if (in_array($one, ['/', '-'])) {
                $dividers[$one][] = $k;
            }
        }

        $i = sizeof($segments);

        array_pop($segments);

        while ($i > 1) {
            $urls[] = implode('/', $segments).'/*';
            array_pop($segments);
            --$i;
        }

        foreach ($urls as $key => $url) {
            foreach (['/', '-'] as $divider) {
                foreach ($dividers[$divider] as $char_position) {
                    if (strlen($url) >= $char_position) {
                        $url = substr_replace($url, $divider, $char_position, 1);
                    }
                }

                $urls[$key] = addslashes($url);
            }
        }

        $this->possibleUrl = $urls;

        return $urls;
    }

    public function parseTemplate($template)
    {
        return !empty(trim($template)) ? preg_replace('!\s+!', ' ', trim(str_replace(["\n", "\r"], ['', ''], $this->renderBladeTemplate($template, $this->templateData)))) : '';
    }

    public function renderBladeTemplate($__string, $__data)
    {
        $php = Blade::compileString($__string);

        $obLevel = ob_get_level();
        ob_start();
        extract($__data, EXTR_SKIP);

        try {
            eval('?'.'>'.$php);
        } catch (Exception $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }
            throw $e;
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }
            throw new FatalThrowableError($e);
        }

        return ob_get_clean();
    }
}
