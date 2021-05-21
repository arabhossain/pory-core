<?php


namespace Pory\Core\ServiceProviders;
use Closure;


class MiddlewareServices
{
    public function handle( $request, Closure $next ) {

        return $next($request);
    }
}
