<?php

namespace Perevorotcom\LaravelOctober\Models;

class SettingsSeo extends SystemSetting
{
    public $instance='perevorot_seo_settings';

    public $backendModel='Perevorot\Seo\Models\Settings';

    public $translatable=[
        'title',
        'description',
        'keywords',
        'og_title',
        'og_sitename',
        'og_description'
    ];

    public $attachments=[
        'og_image'
    ];
}
