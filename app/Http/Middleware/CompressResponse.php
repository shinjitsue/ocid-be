<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CompressResponse
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if ($request->header('Accept-Encoding') && 
            strpos($request->header('Accept-Encoding'), 'gzip') !== false) {
            
            $content = $response->getContent();
            if (strlen($content) > 1024) { // Only compress if > 1KB
                $compressed = gzencode($content, 6);
                $response->setContent($compressed);
                $response->headers->set('Content-Encoding', 'gzip');
                $response->headers->set('Content-Length', strlen($compressed));
            }
        }
        
        return $response;
    }
}