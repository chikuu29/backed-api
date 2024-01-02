<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // $authHeader = $request->header('Authorization');
        $authorizationHeader = $request->header('Authorization');
        if ($authorizationHeader) {
            // list($bearer, $token) = explode(' ', $authHeader, 2);
            // echo $token;
            try {
                $token = $request->bearerToken(); // Get the JWT from the Authorization header
                if (!$token) {
                    return response()->json(['error' => 'Token not provided'], 401);
                }
                // Now, you have the token in the $token variable.
                // You can use this token for authentication or other purposes.
                $key = env('JWT_SECRET');

                $decoded = JWT::decode($token, new Key($key, 'HS256'));
                // $decoded = (array) $decoded;
                // print_r($decoded);
                // return $next($request);
                // return response()->json($decoded);
                // Process the decoded payload
                // ...

                $expirationTime = $decoded->exp;
                // Get the current Unix timestamp
                $currentTime = time();
                // Check if the token has expired
                if ($currentTime > $expirationTime) {
                    // echo "Token has expired.";
                    throw new \Firebase\JWT\ExpiredException('The JWT token has expired.');
                } else {
                    return $next($request);
                }
            } catch (\Firebase\JWT\ExpiredException $e) {
                // Handle expired tokens
                return response()->json(['error' => 'Token has expired'], 401);
            } catch (\Firebase\JWT\SignatureInvalidException $e) {
                // Handle invalid signatures
                return response()->json(['error' => 'Invalid token signature'], 401);
            } catch (\Exception $e) {
                // Handle other JWT decoding/validation errors
                return response()->json(['error' => 'Invalid token', 'msg' => $e], 401);
            }
        } else {
            return response()->json(['error' => 'Token not provided'], 401);
        }
    }
}
