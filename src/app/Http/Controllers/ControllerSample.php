<?php

namespace App\Http\Controllers;

use Perevorotcom\LaravelOctober\Http\Controllers\Controller as BaseController;
use Perevorotcom\LaravelOctober\Models\Menu;

class Controller extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->setCommonData([
            'menu' => Menu::label('mainmenu', [
                'depth' => 1
            ])
        ]);
    }
}
