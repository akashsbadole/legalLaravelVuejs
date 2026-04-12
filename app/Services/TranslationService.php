<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class TranslationService
{
    private string $locale;
    private array $translations = [];

    public function __construct(string $locale = 'en')
    {
        $this->locale = $locale;
        $this->loadTranslations();
    }

    public static function setLocale(string $locale): self
    {
        return new self($locale);
    }

    public static function getAvailableLocales(): array
    {
        return [
            'en' => 'English',
            'hi' => 'Hindi',
            'ta' => 'Tamil',
            'te' => 'Telugu',
            'kn' => 'Kannada',
            'ml' => 'Malayalam',
            'mr' => 'Marathi',
            'gu' => 'Gujarati',
            'bn' => 'Bengali',
        ];
    }

    private function loadTranslations(): void
    {
        $cacheKey = "translations_{$this->locale}";
        
        $this->translations = Cache::rememberForever($cacheKey, function () {
            $langPath = resource_path("lang/{$this->locale}");
            
            if (!File::exists($langPath)) {
                return [];
            }

            $translations = [];
            foreach (File::files($langPath) as $file) {
                $translations[pathinfo($file->getFilename(), PATHINFO_FILENAME)] 
                    = require $file->getPathname();
            }
            
            return $translations;
        });
    }

    public function get(string $key, array $replace = [], string $locale = null): string
    {
        $locale = $locale ?? $this->locale;
        $keys = explode('.', $key);
        $value = $this->translations;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $key;
            }
            $value = $value[$k];
        }

        if (is_array($value)) {
            return $key;
        }

        foreach ($replace as $placeholder => $replacement) {
            $value = str_replace(':' . $placeholder, $replacement, $value);
        }

        return $value;
    }

    public function has(string $key): bool
    {
        $keys = explode('.', $key);
        $value = $this->translations;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return false;
            }
            $value = $value[$k];
        }

        return true;
    }

    public function all(): array
    {
        return $this->translations;
    }

    public function getDirection(): string
    {
        $rtlLocales = ['hi', 'ta', 'te', 'kn', 'ml', 'mr', 'gu', 'bn', 'ar', 'ur'];
        return in_array($this->locale, $rtlLocales) ? 'rtl' : 'ltr';
    }
}
