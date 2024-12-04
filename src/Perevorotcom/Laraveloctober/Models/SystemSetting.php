<?php

namespace Perevorotcom\Laraveloctober\Models;

use Cache;
use Localization;

class SystemSetting extends \LaraveloctoberModel
{
    use \TranslatableTrait;
    use \AttachmentsTrait;

    protected $table = 'system_settings';

    protected $withoutTranslations = true;

    public $instance;
    public $backendModel;
    public $translatable;
    public $attachments;

    public function __construct($attributes = [])
    {
        $this->setAttributes($attributes);

        parent::__construct();
    }

    public function parse($attributes)
    {
        $this->setAttributes($attributes);

        $data = json_decode($this->value);

        foreach ($data as $key => $value) {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    public function parseTranslated($attributes)
    {
        if ($attributes && !empty($attributes->attribute_data)) {
            $attributes = json_decode($attributes->attribute_data);
            $this->setAttributes($attributes);

            $data = json_decode($this->value);

            foreach ($data as $key => $value) {
                $this->attributes[$key] = $value;
            }
        }

        return $this;
    }

    private function setAttributes($attributes)
    {
        foreach ($attributes as $method => $value) {
            $this->{$method} = $value;
        }
    }

    public function scopeInstance()
    {
        return Cache::rememberForever('settings_' . $this->instance, function () {
            $attributes = [
                'backendModel' => $this->backendModel,
                'translatable' => $this->translatable,
                'attachments' => $this->attachments,
            ];

            $setting = new SystemSetting($attributes);

            $instance = $setting->where('item', $this->instance)->first();
            $translated = Localization::getDefaultLocale() !== Localization::getCurrentLocale();

            if ($translated) {
                $attributes = SystemTranslationAttribute::select('attribute_data')->where('model_type', $this->backendModel)->where('locale', Localization::getCurrentLocale())->first();
            }

            return $instance ? $instance->{$translated ? 'parseTranslated' : 'parse'}($attributes) : [];
        });
    }
}
