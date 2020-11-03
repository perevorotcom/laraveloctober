<?php

namespace Perevorotcom\Laraveloctober\Models;

class Settings extends SystemSetting
{
    public $instance = 'common';

    public $backendModel = 'Perevorot\Settings\Models\Common';

    // public $translatable=[
    //     'address',
    // ];

    // public $attachments=[
    //     'logo'
    // ];
}
