<?php

namespace Ryan\LineKit\Http\Middleware;

use App\Services\Plugins\AES256EncryptService;
use App\Services\v2\OrganizationService;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class OrganizationMiddleware
{
    protected $organizationService;

    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $headers = \Illuminate\Support\Str::of($request->header('token', null))->explode(',');

        foreach ($headers as $key => $value) {
            // 解密 token
            $plainText = AES256EncryptService::decrypt($value);
            $plainText_decode = json_decode($plainText, true);
            if ($plainText_decode) {
                $organization = $this->organizationService->findByValidOrganizationID($plainText_decode['id']);
                if ($organization) {
                    $organization->load('created_by_user');
                    $user = $organization->created_by_user;
                    if ($user && Carbon::now()->gte($user->expire_at)) {
                        return response()->json(['message' => '您的方案已過期'], 499);
                    }
                    $request->merge(['organization' => json_decode($plainText, true)]);
                    return $next($request);
                }
            }
        }
        return response()->json(['message' => '請先選擇有效的組織'], 400);
    }
}
