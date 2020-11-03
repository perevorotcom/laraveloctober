<?php

namespace Providers;

use Artesaos\SEOTools\Providers\SEOToolsServiceProvider;
use Perevorotcom\Laraveloctober\Classes\SEO;
use Perevorotcom\Laraveloctober\Models\SettingsSeo;
use Request;

class SEOServiceProvider extends SEOToolsServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->setDefaults();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();

        $this->app->singleton('seo', function () {
            return new SEO();
        });
    }

    public function provides()
    {
        $provides = parent::provides();

        array_push($provides, 'seo');

        return $provides;
    }

    private function setDefaults()
    {
        try {
            $default = SettingsSeo::instance();

            if (!empty($default)) {
                $keywords = !empty($default->keywords) ? explode(',', $default->keywords) : [];
                $keywords = array_map('trim', $keywords);

                config([
                    'seotools.opengraph.defaults' => [
                        'title' => htmlentities($default->og_title, ENT_QUOTES),
                        'description' => htmlentities($default->og_description, ENT_QUOTES),
                        'url' => Request::url(),
                        'type' => 'website',
                        'site_name' => htmlentities($default->og_sitename, ENT_QUOTES),
                        'images' => [
                            $default->og_image ? $default->og_image->path : '',
                        ],
                    ],
                    'seotools.meta.defaults' => [
                        'title' => htmlentities($default->title, ENT_QUOTES),
                        'description' => htmlentities($default->description, ENT_QUOTES),
                        'keywords' => $keywords,
                        'canonical' => false,
                        'separator' => config('seotools.meta.defaults.separator', ' — '),
                    ],
                ]);
            }
        } catch (\PDOException $e) {
        }
    }
}
