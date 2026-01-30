<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThrottleLogin
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->throttleKey($request);

        // 5 attempts in 1 minute
        if ($this->limiter->tooManyAttempts($key, 5)) {
            $seconds = $this->limiter->availableIn($key);

            return redirect()->back()->withErrors([
                'username' => "Çox sayda uğursuz cəhd. Zəhmət olmasa {$seconds} saniyə sonra yenidən cəhd edin."
            ])->withInput($request->only('username'));
        }

        $this->limiter->hit($key, 60);

        return $next($request);
    }

    /**
     * Get the throttle key for the given request.
     */
    protected function throttleKey(Request $request): string
    {
        return strtolower($request->input('username', '')) . '|' . $request->ip();
    }
}
