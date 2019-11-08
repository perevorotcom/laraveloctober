<?php

namespace Perevorotcom\LaravelOctober\Models;

class SeoTags extends \LaravelOctoberModel
{
    use \TranslatableTrait;
    use \AttachmentsTrait;

    public $table = 'perevorot_seo_seo';

    public $backendModel='Perevorot\Seo\Models\Seo';

    public $translatable = [
        'title',
        'description',
        'keywords',
        'og_title',
        'og_sitename',
        'og_description'
    ];
    
    protected $attachments=[
        'og_image',
    ];
}
