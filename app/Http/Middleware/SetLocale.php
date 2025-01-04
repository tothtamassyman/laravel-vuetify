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
        $locale = $request->cookie('locale', LanguageHelper::getBrowserLocale());

        app()->setLocale($locale);

        return $next($request);
    }
}
