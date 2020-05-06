<?php

namespace Perevorotcom\Laraveloctober\Models;

class SeoExternal extends \LaraveloctoberModel
{
    public $table = 'perevorot_seo_external';

    public function scopeEnabled($query)
    {
        return $query->where($this->table.'.is_active', true);
    }
}
