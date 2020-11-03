<?php

namespace Models;

class Menu extends \LaraveloctoberModel
{
    public $table = 'perevorot_page_menu';

    public $depth = 0;
    public $level = 0;

    public function pages()
    {
        return $this->hasMany('Perevorotcom\Laraveloctober\Models\Page')->menuDepth($this->depth, $this->level)->menuEnabled();
    }

    public function scopeLabel($query, string $label, array $attributes)
    {
        foreach ($attributes as $attribute => $value) {
            $this->{$attribute} = $value;
        }

        return $query->where('alias', $label)->with('pages')->first()->pages;
    }
}
