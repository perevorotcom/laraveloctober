<?php

namespace Perevorotcom\Laraveloctober\Http\Controllers;

class PageController extends Controller
{
    public function homepage()
    {
        parent::__construct();

        return view('pages/page');
    }

    public function page()
    {
        parent::__construct();

        return view('pages/page');
    }
}
