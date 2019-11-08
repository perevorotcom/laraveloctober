<?php

namespace Perevorotcom\LaravelOctober\Models;

class SeoExternal extends \LaravelOctoberModel
{
    public $table = 'perevorot_seo_external';

    public function scopeEnabled($query)
    {
        return $query->where($this->table.'.is_active', true);
    }
}
