<?php

namespace Perevorotcom\LaravelOctober\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Perevorotcom\LaravelOctober\Models\Page;
use App\Models\Settings;
use Route;
use SEO;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $page;
    public $path;
    public $settings;
    public $layoutCommonData=[];

    public function __construct()
    {
        $page = new Page();

        $this->path = $page->path;
        $this->page = $page->current;
        $this->settings = Settings::instance();

        $this->layoutCommonData=[
            'page' => $this->page,
            'path' => $this->path,
            'settings' => $this->settings
        ];

        if ($this->page) {
            SEO::setData([
                'page' => $this->page
            ]);
        }elseif (Route::currentRouteName()=='page') {
            abort(404);
        }
    }

    public function view(String $partial, Array $params=[])
    {
        return view($partial, array_merge($params, $this->layoutCommonData));
    }

    public function setCommonData($variable, $value=false)
    {
        if(is_array($variable)) {
                 $this->layoutCommonData=array_merge($this->layoutCommonData, $variable);
        } else {
            $this->layoutCommonData[$variable]=$value;
        }
    }
}
