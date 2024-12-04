<?php

namespace Perevorotcom\Laraveloctober\Traits;

use Illuminate\Support\Str;
use Perevorotcom\Laraveloctober\Models\SystemFile;
use Localization;

trait Model
{
    public function __call($mutator, $attributes)
    {
        if ($this->isAttachmentMutator($mutator)) {
            return $this->{Str::endsWith($mutator, 's') ? 'hasMany' : 'hasOne'}('Perevorotcom\Laraveloctober\Models\SystemFile', 'attachment_id')
                ->where('is_public', 1)
                ->where('attachment_type', $this->getBackendModel())
                ->where('field', $mutator);
        }

        if ($mutator == 'translations') {
            return $this->hasMany('Perevorotcom\Laraveloctober\Models\SystemTranslation', 'model_id')
                ->where('model_type', $this->getBackendModel())
                ->where('locale', Localization::getCurrentLocale());
        }

        return parent::__call($mutator, $attributes);
    }

    public function __get($mutator)
    {
        if ($this->isAttachmentMutator($mutator) && !array_key_exists($mutator, $this->relations)) {
            return SystemFile::where('field', $mutator)->where('attachment_id', $this->id)->where('attachment_type', $this->getBackendModel())->where('is_public', 1)->orderBy('sort_order', 'ASC')->{Str::endsWith($mutator, 's') ? 'get' : 'first'}();
        }

        if ($this->isRicheditorMutator($mutator)) {
            $this->attributes[$mutator] = str_replace('img src="/storage/app', 'img src="' . config('laraveloctober.storageUrl'), $this->attributes[$mutator]);
        }

        return parent::__get($mutator);
    }

    public function isAttachmentMutator($mutator)
    {
        return !empty($this->attachments) && in_array($mutator, $this->attachments) && !empty($this->getBackendModel());
    }

    public function isRicheditorMutator($mutator)
    {
        return !empty($this->richeditors) && in_array($mutator, $this->richeditors);
    }

    private function getBackendModel()
    {
        if (!empty($this->backendModel)) {
            return $this->backendModel;
        }

        return false;
    }
}
