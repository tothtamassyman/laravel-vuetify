<?php

namespace App\Http\Middleware;

use App\Services\LanguageHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SetLocale
 * @package App\Http\Middleware
 */
class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
public function handle(Request $request, Closure $next)
{
    $availableLocales = config('availableLocales', []);
    $currentLocale = app()->getLocale();
    $cookieLocale = $request->cookie('locale');

    $locale = $cookieLocale && array_key_exists($cookieLocale, $availableLocales)
        ? $cookieLocale
        : LanguageHelper::getBrowserLocale();

    if ($locale !== $currentLocale) {
        app()->setLocale($locale);
    }

    $response = $next($request);

    if ($cookieLocale !== $locale) {
//        $response->withCookie(cookie('locale', $locale, 525600, null, null, true, true, false, 'none'));
        $response->withCookie(cookie('locale', $locale, 525600));
    }

    return $response;
}
}
