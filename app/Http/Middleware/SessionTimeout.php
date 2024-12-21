<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{

    protected $timeout = 600; // 10 minutes
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = session('lastActivityTime');
            if ($lastActivity && (time() - $lastActivity) > $this->timeout) {
                Auth::logout();
                return redirect()->route('login')->withErrors(['email' => 'Your session has expired due to inactivity.']);
            }
            session(['lastActivityTime' => time()]);
        }
        \Log::info('SessionTimeout middleware hit'); // Debugging line
        return $next($request);
    }

    
}
