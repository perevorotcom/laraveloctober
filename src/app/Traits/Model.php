<?php

namespace Traits;

use Illuminate\Support\Str;
use Perevorotcom\Laraveloctober\Models\SystemFile;

trait Model
{
    public function __call($mutator, $attributes)
    {
        if ($this->isAttachmentMutator($mutator)) {
            return $this->{Str::endsWith($mutator, 's') ? 'hasMany' : 'hasOne'}('Perevorotcom\Laraveloctober\Models\SystemFile', 'attachment_id')
                    ->where('is_public', 1)
                    ->where('attachment_type', $this->getBackendModel($mutator))
                    ->where('field', $mutator);
        }

        return parent::__call($mutator, $attributes);
    }

    public function __get($mutator)
    {
        if ($this->isAttachmentMutator($mutator) && !array_key_exists($mutator, $this->relations)) {
            return SystemFile::where('field', $mutator)->where('attachment_id', $this->id)->where('attachment_type', $this->getBackendModel($mutator))->where('is_public', 1)->orderBy('sort_order', 'ASC')->{Str::endsWith($mutator, 's') ? 'get' : 'first'}();
        }

        if ($this->isRicheditorMutator($mutator)) {
            $this->attributes[$mutator] = str_replace('img src="/storage/app', 'img src="'.env('STORAGE_URL'), $this->attributes[$mutator]);
        }

        return parent::__get($mutator);
    }

    public function isAttachmentMutator($mutator)
    {
        return !empty($this->attachments) && in_array($mutator, $this->attachments) && !empty($this->getBackendModel($mutator));
    }

    public function isRicheditorMutator($mutator)
    {
        return !empty($this->richeditors) && in_array($mutator, $this->richeditors);
    }

    private function getBackendModel($mutator)
    {
        if (!empty($this->backendModel)) {
            return $this->backendModel;
        }

        return false;
    }
}
