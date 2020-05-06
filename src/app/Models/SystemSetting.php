<?php

namespace Perevorotcom\Laraveloctober\Models;

class SystemSetting extends \LaraveloctoberModel
{
    use \TranslatableTrait;
    use \AttachmentsTrait;

    protected $table = 'system_settings';

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

    private function setAttributes($attributes)
    {
        foreach ($attributes as $method => $value) {
            $this->{$method} = $value;
        }
    }

    public function scopeInstance()
    {
        $attributes = [
            'backendModel' => $this->backendModel,
            'translatable' => $this->translatable,
            'attachments' => $this->attachments,
        ];

        $setting = new SystemSetting($attributes);

        $instance = $setting->where('item', $this->instance)->first();

        return $instance ? $instance->parse($attributes) : [];
    }
}
