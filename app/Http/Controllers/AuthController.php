<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AuthController extends Controller
{
    public function adminLogin(Request $request)
    {
        // Validate user credentials (e.g., username and password)
        // ...
        $requestedData = $request->all();
        // $data = json_decode(file_get_contents("php://input"));

        try {
            $user = $requestedData['userId'];
            $password = $requestedData['password'];
            $logindata = DB::table('admin')->where('UserId', $user)->get();
            if (count($logindata) > 0) {
                if ($password == $logindata[0]->Password) {
                    // Define the expiration time for the token (e.g., 1 hour from now)
                    // $expiration = Carbon::now()->addHours(24)->timestamp;
                    $expiration = Carbon::now()->addHours(12)->timestamp;
                    // If credentials are valid, generate JWT
                    $key = env('JWT_SECRET');  // Secret key from .env or configuration
                    $payload = [
                        'user_id' => $user,
                        'password' => $password,
                        'role' => "admin",
                        'exp' => $expiration,
                        'loginFrom' => "ADMIN"
                    ];
                    $algorithm = 'HS256';
                    $jwt = JWT::encode($payload, $key, $algorithm);
                    // return response()->json(['token' => $jwt]);
                    $user_arr = array(
                        "status" => true,
                        "success" => true,
                        "id" => $logindata[0]->UserId,
                        "name" => $logindata[0]->name,
                        "exp" => $expiration,
                        "token" => $jwt,
                        "message" => "Login Successfully !",
                    );
                } else {
                    $user_arr = array(
                        "status" => false,
                        "success" => false,
                        "id" => '',
                        "name" => '',
                        "message" => "Password not match !",
                    );
                }
            } else {

                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "id" => '',
                    "name" => '',
                    "message" => "User Id not match !",
                );
            }
        } catch (\Exception $e) {

            return response()->json(['error' => 'Token not provided'], 401);
        }

        return json_encode($user_arr);
    }

    public function userLogin(Request $request)
    {
        $requestedData = $request->all();

        $user = $requestedData['userID'];
        $password = $requestedData['password'];
        $loginDateTime = $requestedData['login_date_time'];
        //dd($user);
        if ($user == '' || $user == null || $password == '' || $password == null) {

            return response()->json(array(
                "status" => false,
                "success" => false,
                "id" => '',
                "name" => '',
                "message" => "Please Enter Your Credentials",
            ), 401);
        }

        try {

            $logindata = DB::table('auth_user')->orwhere('auth_ID', $user)->orWhere('auth_email', $user)->orWhere('auth_phone_no', $user)->get();
            if (count($logindata) > 0) {
                if (md5($password) == $logindata[0]->auth_password) {
                    DB::table("user_info")->where([
                        ['user_id', $logindata[0]->auth_ID],
                        ['user_email', $user]
                    ])->update(
                        [
                            'online_status' => 1
                        ]
                    );

                    // DB::table("login_activity")->insert(
                    //     [
                    //         'current_login_states' => 1,
                    //         'login_date_time' => $loginDateTime,
                    //         'user_id' => $logindata[0]->auth_ID
                    //     ]
                    // );
                    $expiration = Carbon::now()->addHours(12)->timestamp;
                    // If credentials are valid, generate JWT
                    $key = env('JWT_SECRET');  // Secret key from .env or configuration
                    $payload = [
                        'user_id' => $user,
                        'password' => $password,
                        'role' => "user",
                        'exp' => $expiration,
                        'loginFrom' => "FontendApplication"
                    ];
                    $algorithm = 'HS256';
                    $jwt = JWT::encode($payload, $key, $algorithm);
                    // return response()->json(['token' => $jwt]);
                    $user_arr = array(
                        "profile_id" => $logindata[0]->auth_ID,
                        "profile_name" => $logindata[0]->auth_name,
                        "profile_email" => $logindata[0]->auth_email,
                        "profile_phone" => $logindata[0]->auth_phone_no,
                        "status" => true,
                        "success" => true,
                        "id" => $logindata[0]->auth_ID,
                        "name" => $logindata[0]->auth_name,
                        "exp" => $expiration,
                        "token" => $jwt,
                        "message" => "Login Successfully !",
                    );
                    return response()->json($user_arr);
                } else {

                    return response()->json(array(
                        "status" => false,
                        "success" => false,
                        "message" => "Password not match !",
                    ));
                }
            } else {
                return response()->json(array(
                    "status" => false,
                    "success" => false,
                    "id" => '',
                    "name" => '',
                    "message" => "User Id not match !",
                ));
            }
        } catch (\Exception $e) {

            return response()->json(array(
                "status" => false,
                "success" => false,
                "id" => '',
                "name" => '',
                "message" => "Unauthorize Access!",
            ), 401);
        }

        // return array("id" => base64_encode(json_encode($user_arr)));
    }

    public function decrypt_openssl($payload)
    {
        $raw = base64_decode($payload);
        $iv_size = openssl_cipher_iv_length('AES-128-CBC');
        $iv = substr($raw, 0, $iv_size);
        $data = substr($raw, $iv_size);
        $key = '1E99412323A4ED2WAYWALASECRET_KEY';
        return openssl_decrypt($data, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    }
}
