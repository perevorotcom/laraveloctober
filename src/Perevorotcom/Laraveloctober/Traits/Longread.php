<?php

namespace Perevorotcom\Laraveloctober\Traits;

use Illuminate\Support\Str;
use Localization;
use Perevorotcom\Laraveloctober\Models\SystemFile;

trait Longread
{
    public function longreadValue($mutator, $value, $isArray = false)
    {
        $blocks = $this->getLongreadValue($mutator);

        if (!empty($blocks)) {
            $this->longreadProccessFiles($blocks);

            $html = [];

            foreach ($blocks as $key => $block) {
                $parsed = $this->processBlockClass($block, $key, sizeof($blocks));

                if (!empty($parsed)) {
                    $parsed = str_replace('img src="/storage/app', 'img src="'.env('STORAGE_URL'), $parsed);

                    $html[] = $parsed;
                }
            }

            return $isArray ? $html : implode('', $html);
        }

        return $value;
    }

    private function processBlockClass($block, $key, $total)
    {
        $namespace Perevorotcom\Laraveloctober\= '\App\Longread\\'.ucfirst(Str::camel($block->alias));

        if (!class_exists($namespace)) {
            return [];
        }

        $block = new $namespace($block);

        $block->first = ($key == 0);
        $block->last = ($key + 1 == $total);

        $block->parse();

        return $block->get();
    }

    public function isLongreadMutator($mutator)
    {
        return !empty($this->longread) && in_array($mutator, $this->longread);
    }

    private function longreadProccessFiles(&$blocks)
    {
        $ids = [];
        $fields = [];

        foreach ($blocks as $block) {
            if (!empty($block->files)) {
                foreach ($block->files as $field => $file) {
                    array_push($fields, $file);
                }
            }
        }

        $files = SystemFile::where('attachment_type', $this->backendModel)->where('attachment_id', $this->id)->whereIn('field', $fields)->orderBy('sort_order', 'ASC')->get();

        if ($files) {
            foreach ($blocks as $k => $block) {
                if (!empty($block->files)) {
                    foreach ($block->files as $field => $file) {
                        $function = (Str::endsWith($field, 's') ? 'filter' : 'first');

                        $block->value->{$field} = $files->$function(function ($systemFile) use ($file) {
                            return $systemFile->field == $file;
                        });
                    }

                    $blocks[$k] = $block;
                }
            }
        }
    }

    private function getLongreadValue($mutator)
    {
        $value = !empty($this->attributes[$mutator.'_'.Localization::getCurrentLocale()]) ? json_decode($this->attributes[$mutator.'_'.Localization::getCurrentLocale()]) : '';

        $value = empty($value) && !empty($this->attributes[$mutator]) ? json_decode($this->attributes[$mutator]) : $value;

        return $value;
    }
}
