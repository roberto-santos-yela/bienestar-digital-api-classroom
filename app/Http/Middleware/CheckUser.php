<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Token;
use App\User;

class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $request_token = $request->header('Authorization');
        $token = new Token();
        $decoded_token = $token->decode($request_token);
        $user = User::where('email', '=', $decoded_token->email)->first();  
        $request->request->add(['user' => $user]);

        if($user != null)
        {
            return $next($request);

        }

        ///FALTA ERROR DE TOKEN/// 

    }
}
