<?php

namespace Ryan\LineKit\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LineRequestMiddleware
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
        // 讀取組織資訊
        $organization = \App\Models\Organization::with('line_channel:id,organization_id')->where('id', $request->input('organization.id'))->firstOrFail();
        if ($organization->line_channel) {
            $request->merge(['line_channel' => $organization->line_channel->toArray()]);
            return $next($request);
        }
        return response()->json(['message' => 'LINE 官方帳號無效'], 400);
    }
}
