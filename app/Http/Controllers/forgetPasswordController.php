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
    public function passwordresetbyadmin(Request $res){
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
            $data = DB::table('auth_user')->where('auth_ID',$user_id)->update([
                'auth_password'=> $passs,
                'password_created_by_admin'=>$text_pass
            ]);
            if($data){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                );
            }
        }
        return json_encode($user_arr);
    }
}