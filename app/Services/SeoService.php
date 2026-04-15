<?php

namespace App\Services;

class SeoService
{
    protected static array $seoData = [];

    public static function set(array $data): void
    {
        self::$seoData = array_merge([
            'title' => config('app.name', 'Legal CRM'),
            'description' => '',
            'keywords' => '',
            'image' => '',
            'url' => '',
            'type' => 'website',
            'twitter_card' => 'summary_large_image',
            'twitter_site' => '',
            'og_locale' => 'en_US',
        ], $data);
    }

    public static function getTitle(): string
    {
        $title = self::$seoData['title'] ?? config('app.name', 'Legal CRM');
        
        if (isset(self::$seoData['title_append']) && self::$seoData['title_append'] !== false) {
            $separator = self::$seoData['separator'] ?? ' | ';
            $title .= $separator . config('app.name', 'Legal CRM');
        }
        
        return $title;
    }

    public static function getDescription(): string
    {
        return self::$seoData['description'] ?? '';
    }

    public static function getKeywords(): string
    {
        return self::$seoData['keywords'] ?? '';
    }

    public static function getImage(): string
    {
        $image = self::$seoData['image'] ?? '';
        
        if ($image && !str_starts_with($image, 'http')) {
            return asset($image);
        }
        
        return $image;
    }

    public static function getUrl(): string
    {
        return self::$seoData['url'] ?? '';
    }

    public static function getType(): string
    {
        return self::$seoData['type'] ?? 'website';
    }

    public static function getTwitterCard(): string
    {
        return self::$seoData['twitter_card'] ?? 'summary_large_image';
    }

    public static function getTwitterSite(): string
    {
        return self::$seoData['twitter_site'] ?? '';
    }

    public static function getOgLocale(): string
    {
        return self::$seoData['og_locale'] ?? 'en_US';
    }

    public static function forget(): void
    {
        self::$seoData = [];
    }

    public static function toArray(): array
    {
        return [
            'title' => self::getTitle(),
            'description' => self::getDescription(),
            'keywords' => self::getKeywords(),
            'image' => self::getImage(),
            'url' => self::getUrl(),
            'type' => self::getType(),
            'twitter_card' => self::getTwitterCard(),
            'twitter_site' => self::getTwitterSite(),
            'og_locale' => self::getOgLocale(),
        ];
    }
}