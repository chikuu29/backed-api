<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class loginController extends Controller
{
    public function adminLogin()
    {

        $data = json_decode(file_get_contents("php://input"));

        $user = $data->userId;
        $password = $data->password;

        try {


            $logindata = DB::table('admin')->where('UserId', $user)->get();
            if (count($logindata) > 0) {
                if ($password == $logindata[0]->Password) {

                    $user_arr = array(
                        "status" => true,
                        "success" => true,
                        "id" => $logindata[0]->UserId,
                        "name" => $logindata[0]->name,
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
            $user_arr = array(
                "status" => false,
                "success" => false,
                "id" => '',
                "name" => '',
                "message" => "Something Wrong Happened!",
            );

        }

        return json_encode($user_arr);
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

    public function userLogin()
    {
        $data = json_decode(file_get_contents("php://input"));
        // print_r($data->id);
        $Key = '1E99412323A4ED2WAYWALASECRET_KEY';
        // $encrypted = json_decode(base64_decode($data->encrypted));
        // $encrypted = json_decode(base64_decode($data->encrypted));
        //print_r($encrypted);
        //$value = openssl_decrypt($encrypted, "AES-128-CTR", $Key);
        // $data1 = $this->decrypt_openssl($data->encrypted);
        // echo $value;
        // return;
        $user = isset($data->userID) ? $data->userID : '';
        $password = isset($data->password) ? $data->password : '';
        //dd($user);
        if ($user == '' || $user == null || $password == '' || $password == null) {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "id" => '',
                "name" => '',
                "message" => "Please Enter Your Credentials",
            );
        }

        try {

            $logindata = DB::table('auth_user')->orwhere('auth_ID', $user)->orWhere('auth_email', $user)->orWhere('auth_phone_no', $user)->get();
            if (count($logindata) > 0) {
                if (md5($password) == $logindata[0]->auth_password) {
                 DB::table("user_info")->where([
                        ['user_id', $logindata[0]->auth_ID],
                        ['user_email',$user]
                    ])->update(
                        [
                            'online_status' => 1
                        ]
                    );
                    $user_arr = array(
                        "status" => true,
                        "success" => true,
                        "profile_id" => $logindata[0]->auth_ID,
                        "profile_name" => $logindata[0]->auth_name,
                        "profile_email" => $logindata[0]->auth_email,
                        "profile_phone" => $logindata[0]->auth_phone_no
                    );
                } else {
                    $user_arr = array(
                        "status" => false,
                        "success" => false,
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
            $user_arr = array(
                "status" => false,
                "success" => false,
                "id" => '',
                "name" => '',
                "message" => "Something Wrong Happened!",
            );

        }

        return array("id" => base64_encode(json_encode($user_arr)));
    }


}