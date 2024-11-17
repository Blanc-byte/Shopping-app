<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsRider
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user() && auth()->user()->role == 'rider') {
            return $next($request);
        }
        return redirect('/home')->with('error', 'You have no user access.');
    }
}
?>