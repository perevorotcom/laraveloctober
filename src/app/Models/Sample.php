<?php

namespace App\Models;

class Sample extends \LaravelOctoberModel
{
    use \LongreadTrait;
    use \TranslatableTrait;
    use \AttachmentsTrait;

    public $table = 'table_name...';
    public $backendModel='Perevorot\Models...';

    protected $longread=[
        'longread',
    ];

    protected $translatable=[
        ['title', 'primary'=>true],
        'description'
    ];

    protected $attachments=[
        'image',
    ];
}
