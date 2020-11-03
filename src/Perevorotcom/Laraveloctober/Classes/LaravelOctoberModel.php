<?php

namespace Perevorotcom\Laraveloctober\Classes;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Str;

class LaravelOctoberModel extends EloquentModel
{
    public function hasGetMutator($mutator)
    {
        if (method_exists($this, 'isTranslatableMutator') && $this->isTranslatableMutator($mutator)) {
            return true;
        }

        if (method_exists($this, 'isLongreadMutator') && $this->isLongreadMutator($mutator)) {
            return true;
        }

        if (method_exists($this, 'isLongreadMutator') && Str::endsWith($mutator, 'Array') && $this->isLongreadMutator(substr($mutator, 0, -5))) {
            return true;
        }

        return method_exists($this, 'get'.Str::studly($mutator).'Attribute');
    }

    public function mutateAttribute($mutator, $value)
    {
        if (method_exists($this, 'isTranslatableMutator') && $this->isTranslatableMutator($mutator)) {
            return $this->translatedValue($mutator, $value);
        }

        if (method_exists($this, 'isLongreadMutator') && $this->isLongreadMutator($mutator)) {
            return $this->longreadValue($mutator, $value);
        }

        if (method_exists($this, 'isLongreadMutator') && Str::endsWith($mutator, 'Array') && $this->isLongreadMutator(substr($mutator, 0, -5))) {
            return $this->longreadValue(substr($mutator, 0, -5), $value, true);
        }

        return $this->{'get'.Str::studly($mutator).'Attribute'}($value);
    }
}
