<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class adminController extends Controller
{
    public function logouplode(Request $request){
       
    
            $alldata = $request->all();
            // print_r($alldata);
            $id = $alldata['q'];
            if ($request->hasFile('uploadfile')) {
                $file = $request->file('uploadfile');
                $imagedata = $_FILES['uploadfile']['name'];
                $temp = $_FILES['uploadfile']['tmp_name'];
                $datainarryform = array();
                for ($i = 0; $i < count($imagedata); $i++) {
                    $imgstoreindatabase[$i] = $i . time() . '.' . $_FILES['uploadfile']['name'][$i];
                    $a[$i] = str_replace($_FILES['uploadfile']['name'][$i], 'jpg', $imgstoreindatabase[$i]);
                    $c = move_uploaded_file($temp[$i], storage_path() . '/uploads/logos'.'/' . $a[$i]);
                    array_push($datainarryform, $a[$i]);
                }
                $d = implode(',', $datainarryform);
    
                // $user_info = DB::table('user_info')->where('user_id', $id)->update([
                //     'user_profile_image' => $d
                // ]);
                $profile_image_table = DB::table('user_profile_images')->insert([
                    'completed' => 1,
                    'user_ID' => $id,
                    'user_feature_images' => $d,
                    'user_profile_images' => $d
                ]);
                // $user_info > 0 &&
                if ($profile_image_table > 0) {
                    $user_arr = array(
                        "success" => true,
                        "message" => "File Uploaded Successfully",
                        "data" => $d
                    );
                } else {
                    $user_arr = array(
                        "success" => false,
                        "message" => "Unable to Store Data"
    
                    );
                }
    
                return json_encode($user_arr);
            }
        
    }
    public function bannerUplode(Request $request){
        $alldata = $request->all();
        // print_r($alldata);
        $id = $alldata['q'];
        if ($request->hasFile('uploadfile')) {
            $file = $request->file('uploadfile');
            $imagedata = $_FILES['uploadfile']['name'];
            $temp = $_FILES['uploadfile']['tmp_name'];
            $datainarryform = array();
            for ($i = 0; $i < count($imagedata); $i++) {
                $imgstoreindatabase[$i] = $i . time() . '.' . $_FILES['uploadfile']['name'][$i];
                $a[$i] = str_replace($_FILES['uploadfile']['name'][$i], 'jpg', $imgstoreindatabase[$i]);
                $c = move_uploaded_file($temp[$i], storage_path() . '/uploads/banner'.'/' . $a[$i]);
                array_push($datainarryform, $a[$i]);
            }
            $d = implode(',', $datainarryform);

            // $user_info = DB::table('user_info')->where('user_id', $id)->update([
            //     'user_profile_image' => $d
            // ]);
            $profile_image_table = DB::table('user_profile_images')->insert([
                'completed' => 1,
                'user_ID' => $id,
                'user_feature_images' => $d,
                'user_profile_images' => $d
            ]);
            // $user_info > 0 &&
            if ($profile_image_table > 0) {
                $user_arr = array(
                    "success" => true,
                    "message" => "File Uploaded Successfully",
                    "data" => $d
                );
            } else {
                $user_arr = array(
                    "success" => false,
                    "message" => "Unable to Store Data"

                );
            }

            return json_encode($user_arr);
        }
    
    }

    
}
