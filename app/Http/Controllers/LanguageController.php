<?php

namespace App\Http\Controllers;

use App\Services\LanguageHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class LanguageController
 *
 * @package App\Http\Controllers
 */
class LanguageController extends Controller
{
    /**
     * Get the available locales.
     *
     * @return JsonResponse
     */
    public function getLocales(): JsonResponse
    {
        return response()->json(config('availableLocales'));
    }

    public function getCurrentLocale(Request $request): JsonResponse
    {
        $locale = $request->cookie('locale', LanguageHelper::getBrowserLocale());
        return response()->json(['locale' => $locale]);
    }

    public function setLocale(Request $request, $locale): JsonResponse
    {
        $availableLocales = config('availableLocales', []);
        if (array_key_exists($locale, $availableLocales)) {
            app()->setLocale($locale);

            return response()->json(['locale' => $locale])
                ->withCookie(cookie('locale', $locale, 525600));
                //->withCookie(cookie('locale', $locale, 525600, null, null, true, true, false, 'none'));
        }

        return response()->json(['error' => 'Invalid locale'], 400);
    }
}
