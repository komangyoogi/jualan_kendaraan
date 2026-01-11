<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

use Closure;

use App\Helpers\ApiFormatter;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use TYmon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\JWTException;

class Authenticate extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        $header = $request->header('Authorization');
        if (!$header) {
            return response()->json(ApiFormatter::createJson(401, 'Authorization Token not found'), 401);
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(ApiFormatter::createJson(401, 'User not found'), 401);
            }
        } catch (TokenExpiredException $e) {
            return response()->json(ApiFormatter::createJson(401, 'Authorization Token is Expired'), 401);
        } catch (TokenInvalidException $e) {
            return response()->json(ApiFormatter::createJson(401, 'Authorization Token is Invalid'), 401);
        } catch (TokenBlacklistedException $e) {
            return response()->json(ApiFormatter::createJson(401, 'Authorization Token is Blacklisted'), 401);
        } catch (JWTException $e) {
            return response()->json(ApiFormatter::createJson(401, 'Authorization Token not found'), 401);
        }
        return $next($request);
    }
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }
}
