<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Queue;


class AuthController extends Controller
{
    public function adminLogin(Request $request)
    {
        // Validate user credentials (e.g., username and password)
        // ...
        // $requestedData = $request->all();
        $data = json_decode(file_get_contents("php://input"));
        //dd($data->userId);

        try {
            $user = $data->userId;
            $password = $data->password;
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

            return response()->json(['error' => 'Token not provided', 'error1' => $e], 401);
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



    public function generateResetLink(Request $request)
    {
        $requestedData = $request->all();
        $userID = $requestedData['userID'];
        $application_url=$requestedData['application_url'];
        if (empty($userID) &&  empty($application_url)) {
            return response()->json([
                "status" => false,
                "success" => false,
                "message" => "Please Enter Your Credentials",
            ], 401);
        }

        try {
            $userdata = DB::table('auth_user')->orWhere('auth_ID', $userID)->orWhere('auth_email', $userID)->first();
            if ($userdata) {
               
                // Generate a random token
                $token = Str::random(60);
                $expiration = Carbon::now()->addHours(12)->timestamp;
                $key = env('JWT_SECRET');  // Secret key from .env or configuration
                $payload = [
                    'email' => $userdata->auth_email,
                    'token' => $token,
                    'exp' => $expiration,
                ];
                $algorithm = 'HS256';
                $jwtToken = JWT::encode($payload, $key, $algorithm);
                // echo $jwtToken;
                // Return the reset link with the JWT token

                $resetLink = $application_url . 'auth/reset_link/' . $jwtToken;

                $emailData = [
                    'view' => 'mail.resetLink', // The view for the email content
                    'data' => [
                        'user_email' => $userdata->auth_email,
                        'name' => $userdata->auth_name,
                        'resetLink' => $resetLink,
                        'profile_id' => $userdata->auth_ID
                    ],
                    'subject' => 'Password Reset Link',
                    'from' => 'info@choicemarriage.com', // Sender email address
                    'from_name' => 'choicemarriage', // Sender name
                    'to' => $userdata->auth_email, // Recipient email address
                    'to_name' => $userdata->auth_name, // Recipient name
                ];


                Queue::push(new SendEmailJob($emailData), '', 'emails');
                return response()->json([
                    "status" => true,
                    "success" => true,
                    "message" => "Please Check You Mail - Reset link generated successfully",
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "success" => false,
                    "message" => "Email not found. Please enter a valid email.",
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "success" => false,
                "error"=>$e,
                "dta"=>$userdata,
                "message" => "Unauthorized Access!",
            ], 401);
        }
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
