<?php

namespace Wmzs\WebWeChat\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class LoginMiddleware
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
		
//		$uuid = $request->session()->get('uuid', null);
//		if ( 
//		
//			$request->path() == 'create/web/wechat/image/login' ||
//			$request->path() == 'api/service/web/wechat/check/login' ||
//			$request->path() == 'api/service/web/wechat/get/xml/init/data' ||
//		    $request->path() == 'create/web/wechat/image/login/result'
//		
//		) {
//			return $next($request);
//		}
//		
//		if ( $uuid == null ) {
//			return redirect('/create/web/wechat/image/login');
//		}
		
		
        return $next($request);
    }
}
