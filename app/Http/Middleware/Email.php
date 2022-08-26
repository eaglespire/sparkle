<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;

use Auth;
class Email
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::guard('user')->user()->email_verify == 1)
        {
            return $next($request);
        }else{
            return redirect()->route('user.verify-email');
        }

    }
}
