<?php

namespace App\Http\Middleware;

use Closure;

class ValidateRequest
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
        if (isset($request->movie_id) && !is_int($request->movie_id)) {
            return redirect('')->with('error','movie id var is not valid');
        }

        if (isset($request->depth) && !is_int($request->depth)) {
            return redirect('/')->with('error','depth var is not valid');
        }

        if (isset($request->genre_id) && !is_int($request->genre_id)) {
            return redirect('/')->with('error','depth var is not valid');
        }

        return $next($request);
    }
}
