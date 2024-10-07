<?php

namespace App\Services;

use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateService
{
    protected $translator;

    public function __construct()
    {
        $this->translator = new GoogleTranslate();
    }

    public function translate($text, $targetLang)
    {
        return $this->translator->setTarget($targetLang)->translate($text);
    }

    public function translateArray(array $texts, $targetLang)
    {
        $translated = [];
        foreach ($texts as $key => $text) {
            if (is_array($text)) {
                $translated[$key] = $this->translateArray($text, $targetLang);
            } else {
                $translated[$key] = $this->translate($text, $targetLang);
            }
        }
        return $translated;
    }

    public function translateWithPlaceholders($text, $targetLang)
    {
        // :attribute gibi yer tutucuları bul
        preg_match_all('/:([a-zA-Z0-9_]+)/', $text, $matches);
        $placeholders = $matches[0];

        // Çeviri yap
        $translated = $this->translate($text, $targetLang);

        // Yer tutucuları geri yerleştir
        foreach ($placeholders as $placeholder) {
            $translated = str_replace($placeholder, $placeholder, $translated);
        }

        return $translated;
    }
}
