<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemFile extends Model
{
    protected $table = 'system_files';

    public function getPathAttribute()
    {
        return $this->getStorageDirectory('uploads/public').'/'.$this->disk_name;
    }

    public function getMediaPathAttribute()
    {
        return $this->getStorageDirectory('media').'/'.$this->disk_name;
    }

    public function thumbPath($width, $height, $mode = 'auto')
    {
        return $this->getStorageDirectory('uploads/public').'/thumb_'.$this->id.'_'.$width.'_'.$height.'_0_0_'.$mode.'.'.pathinfo($this->disk_name, PATHINFO_EXTENSION);
    }

    public function srcSetPath($srcSet, $mode = 'auto')
    {
        $set = [];

        foreach ($srcSet as $dimensions) {
            $size = explode('x', $dimensions);

            array_push($set, $this->thumbPath($size[0], $size[1], $mode).' '.$size[0].'w');
        }

        return implode(', ', $set);
    }

    protected function getStorageDirectory($type)
    {
        return env('STORAGE_URL').'/'.$type.'/'.implode('/', array_slice(str_split($this->disk_name, 3), 0, 3));
    }

    public function getDescriptionAttribute()
    {
        return $this->parseJsonDescription('description');
    }

    public function getUrlAttribute()
    {
        return $this->parseJsonDescription('url');
    }

    public function getIsTargetBlankAttribute()
    {
        return $this->parseJsonDescription('is_target_blank');
    }

    public function parseJsonDescription($field)
    {
        $json = json_decode($this->attributes['description']);

        if (is_object($json)) {
            return $json->{$field};
        }

        if (!empty($this->attributes[$field])) {
            return $this->attributes[$field];
        }

        return '';
    }
}
