<?php

if (! function_exists('localized_url')) {
    function localized_url($url)
    {
        return Localization::getLocalizedURL(null, $url);
    }
}

if (! function_exists('localized_route_url')) {
    function localized_route_url($route, $model = null)
    {
        $attributes=[];

        if(!empty($model) && is_array($model)) {
            foreach($model as $item) {
                array_push($attributes, !empty($item->slug) ? $item->slug : @$item->{$item->primaryKey});
            }
        } elseif(!empty($model)) {
            $attributes[]=!empty($model->slug) ? $model->slug : @$model->{$model->primaryKey};
        }

        return Localization::getLocalizedURL(null, route($route, $attributes));
    }
}

if (! function_exists('storage_url')) {
    function storage_url($file)
    {
        return env('STORAGE_URL').$file;
    }
}

if (! function_exists('translate')) {
    function translate($label, $fallback='')
    {
        return Translate::get($label, $fallback);
    }
}


if (! function_exists('get_sql')) {
    function get_sql($model)
    {
        $replace = function ($sql, $bindings) {
            $needle = '?';

            foreach ($bindings as $replace) {
                $pos = strpos($sql, $needle);
                if ($pos !== false) {
                    if (gettype($replace) === "string") {
                        $replace = ' "'.addslashes($replace).'" ';
                    }
                    $sql = substr_replace($sql, $replace, $pos, strlen($needle));
                }
            }
            return $sql;
        };

        $sql = $replace($model->toSql(), $model->getBindings());

        return $sql;
    }
}
