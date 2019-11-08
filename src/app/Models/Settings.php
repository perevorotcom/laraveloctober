<?php

namespace App\Models;

use Perevorotcom\LaravelOctober\Models\SystemSetting;

class Settings extends SystemSetting
{
    public $instance='common';

    public $backendModel='Perevorot\Settings\Models\Common';

    // public $translatable=[
    //     'address',
    // ];

    // public $attachments=[
    //     'logo'
    // ];
}
