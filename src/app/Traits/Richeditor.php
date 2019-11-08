<?php

namespace Perevorotcom\LaravelOctober\Traits;

trait Richeditor
{
    public function __call($mutator, $attributes)
    {
        if($this->isRicheditorMutator($mutator)) {
            return $mutator;
        }

        return parent::__call($mutator, $attributes);
    }

    public function __get($mutator)
    {
        if($this->isRicheditorMutator($mutator)) {
            return $mutator;//$this->{$mutator};
        }

        return parent::__get($mutator);
    }

    public function isRicheditorMutator($mutator)
    {
        dd($this->richeditors);
        return !empty($this->richeditors) && in_array($mutator, $this->richeditors);
    }
}
