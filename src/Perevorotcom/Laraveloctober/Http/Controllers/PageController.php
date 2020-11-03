<?php

namespace Perevorotcom\Laraveloctober\Http\Controllers;

class PageController extends Controller
{
    public function homepage()
    {
        return $this->view('pages/page');
    }

    public function page()
    {
        return $this->view('pages/page');
    }
}
