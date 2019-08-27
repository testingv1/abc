<?php
namespace App\Middleware;

use Closure;
use App\Libs\AccessToken;
use App\Models\User;

class Authorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->header('Authorization');
        if (!$token) {
            return response(['error' => 'Authorization failed'], 403);
        }
        try {
            $payload = AccessToken::decode($token);
            $userIdCount = User::where('id', $payload->userId)->count();
            if ($userIdCount !== 1) {
                return response(['error' => 'Authorization failed'], 403);
            }
        } catch (\Exception $e) {
            return response(['error' => 'Expired or invalid token'], 403);
        }
        return $next($request);
    }
}
