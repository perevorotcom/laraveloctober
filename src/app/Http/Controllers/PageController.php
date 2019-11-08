<?php

namespace Perevorotcom\LaravelOctober\Http\Controllers;

use Perevorotcom\LaravelOctober\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
