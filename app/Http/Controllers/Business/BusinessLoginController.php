<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

class BusinessLoginController extends Controller
{
    /**
     * 登录
     * @param Request $loginRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $loginRequest)
    {
        try {
            $credentials = self::credentials($loginRequest);
            if (!$token = auth('business')->attempt($credentials)) {
                return json_fail(100, '账号或者用户名错误!', null);
            }
            return self::respondWithToken($token, '登陆成功!');
        } catch (\Exception $e) {
            echo $e->getMessage();
            return json_fail(500, '登陆失败!', null, 500);
        }
    }

    /**
     * 注销登录
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            auth()->logout();
        } catch (\Exception $e) {

        }
        return auth()->check() ?
            json_fail('注销登陆失败!',null, 100 ) :
            json_success('注销登陆成功!',null,  200);
    }



    protected function credentials($request)
    {
        return ['Business_number' => $request['Business_number'], 'Business_password' => $request['Business_password']];
    }

    protected function respondWithToken($token, $msg)
    {
        return json_success( $msg, array(
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('business')->factory()->getTTL() * 60
        ),200);
    }

    /**
     * 注册
     * @param Request $registeredRequest
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function registered(Request $registeredRequest)
    {
        return Business::createUser(self::userHandle($registeredRequest)) ?
            json_success('注册成功!',null,200  ) :
            json_success('注册失败!',null,100  ) ;

    }
    protected function userHandle($request)
    {
        $registeredInfo = $request->except('password_confirmation');
        $registeredInfo['Business_Password'] = bcrypt($registeredInfo['Business_Password']);
        $registeredInfo['Business_number'] = $registeredInfo['Business_number'];
        return $registeredInfo;
    }
}
