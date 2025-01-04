<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

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
}
