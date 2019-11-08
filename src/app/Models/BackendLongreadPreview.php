<?php

namespace Perevorotcom\LaravelOctober\Models;

class BackendLongreadPreview extends \LaravelOctoberModel
{
    use \LongreadTrait;

    protected $table = 'perevorot_longread_preview';

    protected $longread=[
        'longread',
    ];

}
