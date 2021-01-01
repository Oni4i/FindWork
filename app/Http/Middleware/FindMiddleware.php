<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Validator;

class FindMiddleware
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
        $validator = Validator::make($request->all(),
        [
            'query' => 'required|string',
            'options' => 'array|nullable',
            'options.sites' => 'array|nullable',
            'options.sites.*' => 'string|nullable',
            'options.countries' => 'array|nullable',
            'options.countries.*' => 'string|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors());
        }
        return $next($request);
    }
}
