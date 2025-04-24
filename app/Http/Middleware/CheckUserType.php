<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$types
     * @return mixed
     */
    public function handle($request, Closure $next, ...$types)
    {
        if (!Auth::check()) {
            return redirect()->route('login.show')->with('mensagem', 'Você precisa estar logado.');
        }

        $user = Auth::user();

        if (!in_array($user->tipo, $types)) {
            abort(403, 'Acesso não autorizado.');
        }

        return $next($request);
    }
}
