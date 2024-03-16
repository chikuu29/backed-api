<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class forgetPasswordController extends Controller
{
    public function firstPass(Request $res)
    {
        $input = $res->all();
        if ($input == null) {
            $user_arr = array(
                "status" => false,
                "success" => false,
            );
        } else {
            if ($input['first'] == FIRSTPASS) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                );
            }
        }
        return json_encode($user_arr);
    }
    public function secondPass(Request $res)
    {
        $input = $res->all();
        if ($input == null) {
            $user_arr = array(
                "status" => false,
                "success" => false,
            );
        } else {
            if ($input['second'] == SENDPASS) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                );
            }
        }
        return json_encode($user_arr);
    }
    public function passwordresetbyadmin(Request $res)
    {
        $input = $res->all();
        if ($input == null) {
            $user_arr = array(
                "status" => false,
                "success" => false,
            );
        } else {
            $user_id = $input['user_id'];
            $passs = md5($input['pass']);
            $text_pass = $input['pass'];
            $data = DB::table('auth_user')->where('auth_ID', $user_id)->update([
                'auth_password' => $passs,
                'password_created_by_admin' => $text_pass,
                'change_pass_count' => 0
            ]);
            if ($data) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                );
            }
        }
        return json_encode($user_arr);
    }

    // public function checkpass(Request $res)
    // {
    //     $input = $res->all();
    //     if ($input == null) {
    //         $user_arr = array(
    //             "status" => false,
    //             "success" => false,
    //             'msg' => 'Where is you are data'
    //         );
    //     } else {
    //         $sanitizedInput = array_map('htmlspecialchars', $input);
    //         $mdconvertyold = md5($sanitizedInput['oldPassword']);
    //         $mdconvertynew = md5($sanitizedInput['newPassword']);
    //         try {
    //             $userinfomation = DB::table('auth_user')->where('auth_ID', $sanitizedInput['auth_ID'])->first();
    //             if ($userinfomation->auth_password == $mdconvertyold) {
    //                 DB::table('auth_user')->where('auth_ID', $sanitizedInput['auth_ID'])->update([
    //                     'auth_password' => $mdconvertynew,
    //                     'change_pass_count' => 0
    //                 ]);
    //                 $user_arr = array(
    //                     "status" => false,
    //                     "success" => false,
    //                     'msg' => 'password change sussfull'
    //                     //$e
    //                 );
    //             } else {
    //                 DB::table('auth_user')->where('auth_ID', $sanitizedInput['auth_ID'])->update([
    //                     'change_pass_count' => $userinfomation->change_pass_count + 1
    //                 ]);
    //                 $user_arr = array(
    //                     "status" => false,
    //                     "success" => false,
    //                     'msg' => 'Worng password'
    //                     //$e
    //                 );
    //             }
    //         } catch (Exception $e) {
    //             $user_arr = array(
    //                 "status" => false,
    //                 "success" => false,
    //                 'msg' => 'Contact to Admin'
    //                 //$e
    //             );
    //         }
    //     }
    //     return json_encode($user_arr);
    // }

    public function checkpass(Request $request)
    {
        // Retrieve input data from the request
        $input = $request->all();

        // Check if input data is empty
        if (empty($input)) {
            return response()->json([
                "status" => false,
                "success" => false,
                'msg' => 'No data provided'
            ]);
        }

        // Sanitize input data
        $sanitizedInput = array_map('htmlspecialchars', $input);

        // Retrieve user information from the database
        $userInformation = DB::table('auth_user')->where('auth_ID', $sanitizedInput['auth_ID'])->first();

        // Check if user information exists
        if (!$userInformation) {
            return response()->json([
                "status" => false,
                "success" => false,
                'msg' => 'User not found'
            ]);
        }


        // Verify old password
        $md5OldPassword = md5($sanitizedInput['oldPassword']);
        if ($userInformation->auth_password != $md5OldPassword) {
            // Increment password change count
            DB::table('auth_user')->where('auth_ID', $sanitizedInput['auth_ID'])->increment('change_pass_count');

            return response()->json([
                "status" => false,
                "success" => false,
                'msg' => 'Wrong password'
            ]);
        }
         // Update password in the database
         $md5NewPassword = md5($sanitizedInput['newPassword']);
        if($md5NewPassword ==  $userInformation->auth_password){
            return response()->json([
                "status" => false,
                "success" => false,
                'msg' => 'Old password and New password Same'
            ]);
        }


        DB::table('auth_user')->where('auth_ID', $sanitizedInput['auth_ID'])->update([
            'auth_password' => $md5NewPassword,
            'change_pass_count' => 0 // Reset change password count
        ]);

        return response()->json([
            "status" => true,
            "success" => true,
            'msg' => 'Password changed successfully'
        ]);
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
}
