<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Models\Setting;
use App\Http\Models\OauthAccessToken;
use App\Models\OauthAccessToken as ModelsOauthAccessToken;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Encoding\JoseEncoder;

class CheckScopes
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $scope = null, $scope2 = null): mixed
    {
        $mtScope = ['admin'];

        if ($request->user()) {
            $dataToken = json_decode($request->user()->token());
            $scopeUser = $dataToken->scopes[0];
        } else {
            try {
                $bearerToken = $request->bearerToken();

                $parser = new Parser(new JoseEncoder());
                $token = $parser->parse($bearerToken);
                $tokenId = $token->claims()->get('jti');

                $getOauth = ModelsOauthAccessToken::find($tokenId);
                $scopeUser = str_replace(str_split('[]""'), "", $getOauth['scopes']);
            } catch (\Exception $e) {
                return $this->unauthorized("Unauthenticated.");
            }
        }


        $arrScope = ['admin'];
        if (
            (in_array($scope, $arrScope) && $scope == $scopeUser) ||
            (in_array($scope2, $arrScope) && $scope2 == $scopeUser)
        ) {
            return $next($request);
        }

        return $this->unauthorized("Unauthenticated.");
    }
}
