<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
      $role = Auth::user()->role;
      $cek = Auth::check();
      if(!$cek || !$role === 'admin'){
        return response()->json(['message' => 'gagal', 'user' => $cek]);
      }
      
      return $next($request);
    }
}
