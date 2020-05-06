<?php

namespace Perevorotcom\Laraveloctober\Classes;

use Cache;
use DB;
use Localization;

class SystemTranslate
{
    public $table = 'rainlab_translate_messages';

    private $messages;
    private $currentLocale;

    public function __construct()
    {
        $this->currentLocale = Localization::getCurrentLocale();
        $this->messages = $this->parseMessages();
    }

    public function get($label, $fallback = '')
    {
        if (!$this->isValidLabel($label)) {
            abort(500, 'Ошибка в метке перевода: `'.$label.'`. Допустимы только [a-ZA-Z0-9.-] с максимальной длиной 255 символов');
        }

        return $this->getMessage($label, $fallback);
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function clearMessageCache()
    {
        Cache::forget($this->getCacheKey());
    }

    private function getMessage($label, $fallback)
    {
        if (!$this->isLabelExists($label)) {
            if (!DB::table($this->table)->where('code', $label)->first()) {
                DB::table($this->table)->insert([
                    'code' => $label,
                    'message_data' => $this->getBlankMessageData($label),
                ]);
            }

            $this->clearMessageCache();

            $this->messages = $this->parseMessages();
        }

        return $fallback && $this->messages[$label] == $label ? $fallback : $this->messages[$label];
    }

    private function getBlankMessageData($label)
    {
        $data = [
            'x' => $label,
        ];

        foreach (Localization::getSupportedLanguagesKeys() as $locale) {
            $data[$locale] = '';
        }

        return json_encode($data);
    }

    private function isLabelExists($label)
    {
        return !empty($this->messages[$label]);
    }

    private function isValidLabel($label)
    {
        return !preg_match('/[^A-Za-z0-9.\-$]/', $label) && mb_strlen($label) <= 255;
    }

    private function parseMessages()
    {
        return Cache::rememberForever($this->getCacheKey(), function () {
            $messages = DB::table($this->table)->get();
            $localizedMessages = [];

            foreach ($messages as $message) {
                $messageData = json_decode($message->message_data);
                $localizedMessages[$message->code] = !empty($messageData->{$this->currentLocale}) ? $messageData->{$this->currentLocale} : $message->code;
            }

            return $localizedMessages;
        });
    }

    private function getCacheKey()
    {
        return 'translate_messages_'.$this->currentLocale;
    }
}
