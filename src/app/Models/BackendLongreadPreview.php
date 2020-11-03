<?php

namespace App\Models;

class BackendLongreadPreview extends \LaraveloctoberModel
{
    use \LongreadTrait;

    protected $table = 'perevorot_longread_preview';

    protected $longread = [
        'longread',
    ];
}
