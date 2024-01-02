<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class uplodeController extends Controller
{
    public function logoUplode(Request $res)
    {

        $input = $res->all();
        $date = $input['date'];
        $image = explode(';base64,', $input['image']);
        $image_base64 = base64_decode($image[1]);
        $extention = explode('/', $image[0]);
        $path = storage_path() . '/logo_image/';
        $uniqid = uniqid();
        $file = $path . $uniqid . '.' . $extention[1];
        if (file_put_contents($file, $image_base64)) {
            $data = DB::table('logo_table')->insert([
                'image' => $uniqid . '.' . $extention[1],
                'created_At' => $date
            ]);
            if ($data) {
                $user_arr = array(
                    "success" => true,
                    "message" => "File Uploaded Successfully",
                );
            }
        } else {
            $user_arr = array(
                "success" => false,
                "message" => "Error",
            );
        }
        return json_encode($user_arr);
    }

    public function bannerUplode(Request $res)
    {

        $input = $res->all();
        // return $input;
        $date = $input['date'];
        $index = $input['index'];
        $image = explode(';base64,', $input['image']);
        $image_base64 = base64_decode($image[1]);
        $extention = explode('/', $image[0]);
        $path = storage_path() . '/banner/';
        $uniqid = uniqid();
        $file = $path . $uniqid . '.' . $extention[1];


        if (file_put_contents($file, $image_base64)) {
            $data = DB::table('banner_image')->insert([
                'image' => $uniqid . '.' . $extention[1],
                'created_At' => $date,
                'index' => $index
            ]);
            if ($data) {
                $user_arr = array(
                    "success" => true,
                    "message" => "File Uploaded Successfully",
                );
            }
        } else {
            $user_arr = array(
                "success" => false,
                "message" => "Error",
            );
        }
        return json_encode($user_arr);
    }
    public function waterMark(Request $res)
    {

        $input = $res->all();
        // return $input;
        $date = $input['date'];

        $image = explode(';base64,', $input['image']);
        $image_base64 = base64_decode($image[1]);
        $extention = explode('/', $image[0]);
        $path = storage_path() . '/watermark/';
        $uniqid = uniqid();
        $file = $path . $uniqid . '.' . $extention[1];


        if (file_put_contents($file, $image_base64)) {
            $data = DB::table('watermark')->insert([
                'image' => $uniqid . '.' . $extention[1],
                'created_At' => $date,
            ]);
            if ($data) {
                $user_arr = array(
                    "success" => true,
                    "message" => "File Uploaded Successfully",
                );
            }
        } else {
            $user_arr = array(
                "success" => false,
                "message" => "Error",
            );
        }
        return json_encode($user_arr);
    }
    public function barCode(Request $res)
    {

        $input = $res->all();
        //return $input;
        $date = $input['date'];
        $name = $input['name'];
        $phoneno = $input['phoneno'];
        $upi = $input['upi'];
        $image = explode(';base64,', $input['image']);
        $image_base64 = base64_decode($image[1]);
        $extention = explode('/', $image[0]);
        $path = storage_path() . '/barcode/';
        $uniqid = uniqid();
        $file = $path . $uniqid . '.' . $extention[1];


        if (file_put_contents($file, $image_base64)) {
            $data = DB::table('barCode')->insert([
                'image' => $uniqid . '.' . $extention[1],
                'created_At' => $date,
                'name' => $name,
                'phoneno' => $phoneno,
                'upi' =>$upi
            ]);
            if ($data) {
                $user_arr = array(
                    "success" => true,
                    "message" => "File Uploaded Successfully",
                );
            }
        } else {
            $user_arr = array(
                "success" => false,
                "message" => "Error",
            );
        }
        return json_encode($user_arr);
    }
    public function homeLogoUplode(Request $res)
    {

        $input = $res->all();
        $date = $input['date'];
        $image = explode(';base64,', $input['image']);
        $image_base64 = base64_decode($image[1]);
        $extention = explode('/', $image[0]);
        $path = storage_path() . '/logo_image/';
        $uniqid = uniqid();
        $file = $path . $uniqid . '.' . $extention[1];
        if (file_put_contents($file, $image_base64)) {
            $data = DB::table('homepage_icon')->insert([
                'image' => $uniqid . '.' . $extention[1],
                'created_At' => $date
            ]);
            if ($data) {
                $user_arr = array(
                    "success" => true,
                    "message" => "File Uploaded Successfully",
                );
            }
        } else {
            $user_arr = array(
                "success" => false,
                "message" => "Error",
            );
        }
        return json_encode($user_arr);
    }
}