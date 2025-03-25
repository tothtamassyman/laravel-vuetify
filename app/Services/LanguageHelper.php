<?php

namespace App\Services;

/**
 * Class LanguageHelper
 * @package App\Services
 */
class LanguageHelper
{
    /**
     * Get the browser's default locale.
     *
     * @return string
     */
    public static function getBrowserLocale(): string
    {
        $availableLocales = config('availableLocales', []);
        $defaultLocale = config('app.locale', 'en');
        $acceptLanguage = request()->header('Accept-Language', $defaultLocale);

        foreach (explode(',', $acceptLanguage) as $lang) {
            $locale = substr($lang, 0, 2);
            if (array_key_exists($locale, $availableLocales)) {
                return $locale;
            }
        }

        return $defaultLocale;
    }
}
