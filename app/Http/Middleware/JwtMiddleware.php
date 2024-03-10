<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use JWTAuth;
use Exception;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {

            if($e instanceof TokenInvalidException){
                return response()->json(['status'=> 'invalid token'],401);
            }
            if($e instanceof TokenExpiredException){
                return response()->json(['status'=> 'expired token'],401);
            }
            return response()->json(['status'=> 'token not found'],401);
        }
        return $next($request);
    }
}
