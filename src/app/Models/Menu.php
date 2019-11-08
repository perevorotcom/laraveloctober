<?php

namespace Perevorotcom\LaravelOctober\Models;

class Menu extends \LaravelOctoberModel
{
    public $table = 'perevorot_page_menu';

    public $depth=0;
    public $level=0;

    public function pages()
    {
        return $this->hasMany('Perevorotcom\LaravelOctober\Models\Page')->menuDepth($this->depth, $this->level)->menuEnabled();
    }

    public function scopeLabel($query, String $label, Array $attributes)
    {
        foreach($attributes as $attribute=>$value){
            $this->{$attribute}=$value;
        }

        return $query->where('alias', $label)->with('pages')->first()->pages;
    }
}
