<?php

namespace Ryan\LineKit\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SettingLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ('GET' != $request->method()) {
            $data['method'] = $request->method();
            $data['request_url'] = $request->path();
            $data['request_ip'] = $request->ip();
            $data['request_user'] = $request->user() ? $request->user()->id : 0;
            $data['description'] = $request->toArray();
            \App\Models\SettingLog::create($data);
        }
        return $next($request);
    }
}
