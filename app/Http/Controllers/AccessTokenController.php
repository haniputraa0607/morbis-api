<?php

namespace App\Http\Controllers;


use App\Traits\ApiResponse;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Laravel\Passport\Http\Controllers\AccessTokenController as PassportAccessTokenController;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Response as Psr7Response;
use Illuminate\Support\Facades\Auth;
use Modules\User\Entities\User;
use Illuminate\Http\Request;
use App\Http\Models\OauthClient;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Hash;
use Modules\EmployeeSchedule\Entities\EmployeeScheduleDate;
use Modules\Cashier\Entities\EmployeeAttendance;
use Modules\Outlet\Entities\OutletDevice;

class AccessTokenController extends PassportAccessTokenController
{
    use ApiResponse;

/**
     * Authorize a client to access the user's account.
     *
     * @param  ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */


    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
    }

    public function login(Request $request): JsonResponse
    {

        $post = $request->all();

        $user = ModelsUser::where('email', $post['email'])->first();
        if (!$user) {
            return $this->error('Admin tidak ditemukan');
        }
        if(!Hash::check($post['password'], $user['password'])){
            return $this->error('Password salah');
        }
        Auth::loginUsingId($user['id']);
        if($user['type'] == 'admin'){
            $token = auth()->user()->createToken('AdminToken', ['admin'])->accessToken;
        }else{
            return $this->error('Bukan Admin');
        }
        $data = ['access_token' => $token, 'token_type' => 'Bearer', 'admin' => $user];
        return $this->ok("success login", $data);

    }

}
