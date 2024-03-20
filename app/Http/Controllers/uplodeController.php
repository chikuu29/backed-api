<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class uplodeController extends Controller
{


    public function idProofUplode(Request $res)
    {
        $input = $res->all();

        $date = $input['data'];
        $id = $input['user_Id'];
        $image = explode(';base64,', $input['data']);
        $image_base64 = base64_decode($image[1]);
        $extention = explode('/', $image[0]);
        $root = env('FILE_UPLOAD_PATH');
        $uniqid = uniqid();
        $storageFile = $root . '/' . 'idproof/' . $id . $uniqid . '.' . $extention[1];
       //dd($storageFile);
        if (file_put_contents($storageFile, $image_base64)) {
            $useidupload = DB::table('use_id_upload')->where('user_ID', $id)->first();
            if ($useidupload) {
                unlink($root . '/' . 'idproof/' . $useidupload->ID_image);
                DB::table('use_id_upload')->where('user_ID', $id)->update([
                    "ID_image" => $id . $uniqid . '.' . $extention[1],
                ]);
                return response()->json([
                    "success" => true,
                    "message" => "File Uploaded Successfully",
                ]);
            } else {
                DB::table('use_id_upload')->insert([
                    "user_ID" => $id,
                    "ID_image" => $id . $uniqid . '.' . $extention[1],
                ]);
                return response()->json([
                    "success" => true,
                    "message" => "File Uploaded Successfully",
                ]);
            }
        }else{
            return response()->json(['success' => false, 'message' => 'File could not be saved.'], 500);
        }

    }
    public function horoscopeUplode(Request $res)
    {
        $input = $res->all();

        $date = $input['data'];
        $id = $input['user_Id'];
        $image = explode(';base64,', $input['data']);
        $image_base64 = base64_decode($image[1]);
        $extention = explode('/', $image[0]);
        $root = env('FILE_UPLOAD_PATH');
        $uniqid = uniqid();
        $storageFile = $root . '/' . 'horoscope/' . $id . $uniqid . '.' . $extention[1];
       //dd($storageFile);
        if (file_put_contents($storageFile, $image_base64)) {
            $useidupload = DB::table('use_horoscope_upload')->where('user_ID', $id)->first();
            if ($useidupload) {
                unlink(storage_path() . '/' . 'horoscope/' . $useidupload->ID_image);
                DB::table('use_horoscope_upload')->where('user_ID', $id)->update([
                    "ID_image" => $id . $uniqid . '.' . $extention[1],
                ]);
                return response()->json([
                    "success" => true,
                    "message" => "File Uploaded Successfully",
                ]);
            } else {
                DB::table('use_horoscope_upload')->insert([
                    "user_ID" => $id,
                    "ID_image" => $id . $uniqid . '.' . $extention[1],
                ]);
                return response()->json([
                    "success" => true,
                    "message" => "File Uploaded Successfully",
                ]);
            }
        }else{
            return response()->json(['success' => false, 'message' => 'File could not be saved.'], 500);
        }

    }
    public function paymentSlipUplode(Request $res)
    {
        $input = $res->all();

        $date = $input['data'];
        $id = $input['user_Id'];
        $image = explode(';base64,', $input['data']);
        $image_base64 = base64_decode($image[1]);
        $extention = explode('/', $image[0]);
        $root = env('FILE_UPLOAD_PATH');
        $uniqid = uniqid();
        $storageFile = $root . '/' . 'paymentslip/' . $id . $uniqid . '.' . $extention[1];
       //dd($storageFile);
        if (file_put_contents($storageFile, $image_base64)) {
            $useidupload = DB::table('use_payment_slip_upload')->where('user_ID', $id)->first();
            if ($useidupload) {
                unlink(storage_path() . '/' . 'paymentslip/' . $useidupload->ID_image);
                DB::table('use_payment_slip_upload')->where('user_ID', $id)->update([
                    "ID_image" => $id . $uniqid . '.' . $extention[1],
                ]);
                return response()->json([
                    "success" => true,
                    "message" => "File Uploaded Successfully",
                ]);
            } else {
                DB::table('use_horoscope_upload')->insert([
                    "user_ID" => $id,
                    "ID_image" => $id . $uniqid . '.' . $extention[1],
                ]);
                return response()->json([
                    "success" => true,
                    "message" => "File Uploaded Successfully",
                ]);
            }
        }else{
            return response()->json(['success' => false, 'message' => 'File could not be saved.'], 500);
        }

    }
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
                'upi' => $upi
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
