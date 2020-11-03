<?php

namespace Perevorotcom\Laraveloctober\Http\Controllers;

use Perevorotcom\Laraveloctober\Http\Controllers\Controller as BaseController;
use Perevorotcom\Laraveloctober\Models\Menu;

class Controller extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this->setCommonData([
            'menu' => Menu::label('mainmenu', [
                'depth' => 1,
            ]),
        ]);
    }
}
