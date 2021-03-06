<?php

namespace Perevorotcom\Laraveloctober\Traits;

use DB;
use Illuminate\Support\Arr;
use Localization;
use Perevorotcom\Laraveloctober\Scopes\TranslatableScope;

trait Translatable
{
    public $translationIndexesTable = 'rainlab_translate_indexes';
    public $translationAttributesTable = 'rainlab_translate_attributes';
    public $translatableData = [];
    public $ignoreTranslated = false;

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new TranslatableScope());
    }

    public function isTranslatableMutator($mutator)
    {
        return !empty($this->translatable) && in_array($mutator, $this->getTranslatableColumns());
    }

    public function getPrimatyTranslatableMutators()
    {
        if (!empty($this->translatable)) {
            $primaryColumns = Arr::where($this->translatable, function ($column) {
                return !empty($column['primary']);
            });

            if (!empty($primaryColumns)) {
                return Arr::pluck($primaryColumns, 0);
            }
        }

        return [];
    }

    public function getFallbackTranslatableMutators()
    {
        if (!empty($this->translatable)) {
            $fallbackColumns = Arr::where($this->translatable, function ($column) {
                return !empty($column['fallback']);
            });

            if (!empty($fallbackColumns)) {
                return Arr::pluck($fallbackColumns, 0);
            }
        }

        return [];
    }

    public function getTranslatableColumns()
    {
        $array = [];

        foreach ($this->translatable as $column) {
            array_push($array, (is_array($column) ? $column[0] : $column));
        }

        return $array;
    }

    public function translatedValue($mutator, $value)
    {
        $locale = ($this->forcedLocale ? $this->forcedLocale : Localization::getCurrentLocale());

        if (Localization::getDefaultLocale() != $locale) {
            if (!array_key_exists($locale, $this->translatableData)) {
                if (empty($this->backendModel)) {
                    abort(500, 'Для `'.get_class($this).'` не указана переменная backendModel');
                }

                $translation = DB::table($this->translationAttributesTable)->where('model_type', $this->backendModel)->where('model_id', $this->id)->where('locale', $locale)->first();

                if ($translation) {
                    $this->translatableData[$locale] = json_decode($translation->attribute_data);
                } else {
                    $this->translatableData[$locale] = false;
                }

                $this->forcedLocale = null;
            }

            if (!empty($this->richeditors) && in_array($mutator, $this->richeditors)) {
                $this->translatableData[$locale]->{$mutator} = str_replace('img src="/storage/app', 'img src="'.config('laraveloctober.storageUrl'), $this->translatableData[$locale]->{$mutator});
            }

            $return = !empty($this->translatableData[$locale]->{$mutator}) ? $this->translatableData[$locale]->{$mutator} : '';

            if (empty($return) && !empty($this->getFallbackTranslatableMutators())) {
                $return = !empty($this->attributes[$mutator]) ? $this->attributes[$mutator] : '';
            }

            return $return;
        }

        $this->forcedLocale = null;

        return $value;
    }

    public function scopeIgnoreTranslated($query)
    {
        $this->ignoreTranslated = true;

        return $query;
    }

    private $forcedLocale;

    public function locale($locale)
    {
        $this->forcedLocale = $locale;

        return $this;
    }
}
