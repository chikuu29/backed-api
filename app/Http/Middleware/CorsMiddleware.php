<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        // // Add CORS headers
        // $response->headers->set('Access-Control-Allow-Origin', '*');
        // $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        // $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        
        // return $response;
        $allowedOrigins = [
            'http://localhost:4200',
            'https://admin.choicemarriage.com'
        ];
    
        $origin = $request->header('Origin');
        if (in_array($origin, $allowedOrigins)) {
            $headers['Access-Control-Allow-Origin'] = $origin;
        }else{
             return $response;
            
        }
            // var_dumb($request);
            $headers = [
                'Access-Control-Allow-Origin'      => $origin,
              'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE',
              'Access-Control-Allow-Credentials' => 'true',
              'Access-Control-Max-Age'           => '86400',
              'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With'
    
          ];

      

       if ($request->isMethod('OPTIONS'))
       {
           return response()->json('{"method":"OPTIONS"}', 200, $headers);
       }

       $response = $next($request);
       foreach($headers as $key => $value)
       {
           $response->header($key, $value);
       }

       return $response;
    }
}
