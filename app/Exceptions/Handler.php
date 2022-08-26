<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Auth; 
use App\Models\Settings;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $set=Settings::find(1);
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        } 
        if ($request->is($set->admin_url) || $request->is($set->admin_url.'/*')) {
            return redirect()->guest('/'.$set->admin_url);
        }
        if ($request->is('user') || $request->is('user/*')) {
            return redirect()->guest('/login');
        }
        return redirect()->guest(route('login'));
    }
}
