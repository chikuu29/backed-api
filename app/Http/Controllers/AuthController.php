<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Queue;
use Exception;

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
        $application_url = $requestedData['application_url'];
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
                $expiration = Carbon::now()->addHours(1)->timestamp;
                $key = env('JWT_SECRET');  // Secret key from .env or configuration
                $payload = [
                    'email' => $userdata->auth_email,
                    'name' => $userdata->auth_name,
                    'profile_id' => $userdata->auth_ID,
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



                DB::table('password_resets')
                    ->where('email', $userdata->auth_email)
                    ->update(['isTokenActive' => false]);

                // Now, insert the new data into the password_resets table
                // $token = str_random(100); // Generate your token here
                DB::table('password_resets')->insert([
                    'email' => $userdata->auth_email,
                    'profile_id' => $userdata->auth_ID,
                    'token' => $jwtToken,
                    'created_at' => Carbon::now(),
                    'isTokenActive' => true // Set it to true for the new record
                ]);
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
                "error" => $e,
                "message" => "Unauthorized Access!",
            ], 401);
        }
    }



    public function validate_token(Request $request)
    {

        $authorizationHeader = $request->header('Authorization');
        // echo $authorizationHeader;
        if ($authorizationHeader) {
            // list($bearer, $token) = explode(' ', $authHeader, 2);
            // echo $token;
            try {
                $token = $request->bearerToken(); // Get the JWT from the Authorization header
                if (!$token) {
                    return response()->json(['error' => 'Token not provided'], 401);
                }
                // Now, you have the token in the $token variable.
                // You can use this token for authentication or other purposes.
                $key = env('JWT_SECRET');

                $decoded = JWT::decode($token, new Key($key, 'HS256'));
                // $decoded = (array) $decoded;
                // print_r($decoded);
                // return $next($request);
                // return response()->json($decoded);
                // Process the decoded payload
                // ...
                $expirationTime = $decoded->exp;
                // Get the current Unix timestamp
                $currentTime = time();
                // Check if the token has expired
                if ($currentTime > $expirationTime) {
                    // echo "Token has expired.";
                    // throw new \Firebase\JWT\ExpiredException('The JWT token has expired.');
                    return response()->json(['error' => 'The JWT token has expired.'], 401);
                } else {
                    $password_resets = DB::table('password_resets')
                        ->where('profile_id',  $decoded->profile_id)
                        ->where('email',  $decoded->email)
                        ->where('token', $token)
                        ->where('isTokenActive', true)->first();
                    if ($password_resets) {
                        return  response()->json(['success' => true, 'message' => "Token Validated Successfully!", "encryptedAccessData" =>  cryptoJsAesEncrypt($decoded)], 200);
                    } else {
                        return response()->json(['error' => 'Unauthorized access'], 401);
                    }
                }
            } catch (\Firebase\JWT\ExpiredException $e) {
                // Handle expired tokens
                return response()->json(['error' => 'Token has expired'], 401);
            } catch (\Firebase\JWT\SignatureInvalidException $e) {
                // Handle invalid signatures
                return response()->json(['error' => 'Invalid token signature'], 401);
            } catch (\Exception $e) {
                // Handle other JWT decoding/validation errors
                return response()->json(['error' => 'Invalid token', 'msg' => $e], 401);
            }
        } else {
            return response()->json(['error' => 'Token not provided'], 401);
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



    public function reset_password(Request $request)
    {
        // $authorizationHeader = $request->header('Authorization');
        // echo $authorizationHeader;
        // if ($authorizationHeader) {
        //     // list($bearer, $token) = explode(' ', $authHeader, 2);
        //     // echo $token;
        //     try {
        // $token = $request->bearerToken(); // Get the JWT from the Authorization header
        // if (!$token) {
        //     return response()->json(['error' => 'Token not provided'], 401);
        // }
        // Now, you have the token in the $token variable.
        // You can use this token for authentication or other purposes.
        // $key = env('JWT_SECRET');
        // $decoded = JWT::decode($token, new Key($key, 'HS256'));
        // $decoded = (array) $decoded;
        // print_r($decoded);
        // return $next($request);
        // return response()->json($decoded);
        // Process the decoded payload
        // ...
        // $expirationTime = $decoded->exp;
        // // Get the current Unix timestamp
        // $currentTime = time();
        // // Check if the token has expired
        // if ($currentTime > $expirationTime) {
        //     // echo "Token has expired.";
        //     // throw new \Firebase\JWT\ExpiredException('The JWT token has expired.');
        //     return response()->json(['error' => 'The JWT token has expired.'], 401);
        // } else {

        // Log::info($request->attributes->all());



        try {
            // Retrieve the decoded token from the request attributes
            $decodedToken = $request->attributes->get('decoded_token');
            // print_r($decodedToken);
            // Fetch the user from the database using profile ID or email

            $token = $request->bearerToken(); // Get the JWT from the Authorization header
            $password_resets = DB::table('password_resets')
                ->where('profile_id',  $decodedToken->profile_id)
                ->where('email',  $decodedToken->email)
                ->where('token', $token)
                ->where('isTokenActive', true)->first();
            // ->update(['auth_password' => $token]); // Assuming $password contains the new password
            if ($password_resets) {
                $authData = DB::table('auth_user')
                    ->where('auth_ID', $decodedToken->profile_id)
                    ->orWhere('auth_email', $decodedToken->email)
                    ->first(); // Assuming you expect only one user, use 'first()' instead of 'get()'

                if ($authData) {
                    // Update the password with the new MD5 hashed password
                    print_r($authData);
                    if ($request->input('password') == $request->input('confrim_password')) {


                        $password = md5($request->input('password'));
                        DB::table('auth_user')
                            ->where('auth_ID', $authData->profile_id)
                            ->where('auth_email', $authData->email)
                            ->update(['auth_password' => $password]);


                        // DB::table('password_resets')
                        //     ->where('email', $decodedToken->email)
                        //     ->update(['isTokenActive' => false]);

                        return response()->json(['success' => true, 'message' => 'Password updated successfully']);
                    } else {
                        return response()->json(['success' => false, 'message' => 'Enter Password Mismatch'], 404);
                    }
                } else {
                    return response()->json(['success' => false, 'message' => 'User not found'], 404);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Unautorize Access'], 404);
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

        return  response()->json(['success' => true, 'message' => "Token Validated Successfully!", "encryptedAccessData" =>  $decodedToken], 200);
        // }
        // } catch (\Firebase\JWT\ExpiredException $e) {
        //     // Handle expired tokens
        //     return response()->json(['error' => 'Token has expired'], 401);
        // } catch (\Firebase\JWT\SignatureInvalidException $e) {
        //     // Handle invalid signatures
        //     return response()->json(['error' => 'Invalid token signature'], 401);
        // } catch (\Exception $e) {
        //     // Handle other JWT decoding/validation errors
        //     return response()->json(['error' => 'Invalid token', 'msg' => $e], 401);
        // }
        // } else {
        //     return response()->json(['error' => 'Token not provided'], 401);
        // }
    }
}



function cryptoJsAesDecrypt($jsonString)
{
    $passphrase = '1E99412323A4ED2WAYWALASECRET_KEY';
    $jsondata = json_decode($jsonString, true);
    try {
        $salt = hex2bin($jsondata["s"]);
        $iv  = hex2bin($jsondata["iv"]);
    } catch (Exception $e) {
        return null;
    }
    $ct = base64_decode($jsondata["ct"]);
    $concatedPassphrase = $passphrase . $salt;
    $md5 = array();
    $md5[0] = md5($concatedPassphrase, true);
    $result = $md5[0];
    for ($i = 1; $i < 3; $i++) {
        $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
        $result .= $md5[$i];
    }
    $key = substr($result, 0, 32);
    $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    return json_decode($data, true);
}
/**
 * Encrypt value to a cryptojs compatiable json encoding string
 *
 * @param mixed $passphrase
 * @param mixed $value
 * @return string
 */
function cryptoJsAesEncrypt($value)
{
    $passphrase = '1E99412323A4ED2WAYWALASECRET_KEY';
    $salt = openssl_random_pseudo_bytes(8);
    $salted = '';
    $dx = '';
    while (strlen($salted) < 48) {
        $dx = md5($dx . $passphrase . $salt, true);
        $salted .= $dx;
    }
    $key = substr($salted, 0, 32);
    $iv  = substr($salted, 32, 16);
    $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
    $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
    return json_encode($data);
}
