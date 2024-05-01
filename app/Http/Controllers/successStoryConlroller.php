<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class successStoryConlroller extends Controller
{
    public function successStory(Request $res)
    {
        $data = json_decode(file_get_contents("php://input"));
        //dd($data);
        // return $data;
        $how_we_met = isset($data->how_we_met) ? $data->how_we_met : '';
        $id = isset($data->id) ? $data->id : '';
        $life_after_marriage = isset($data->life_after_marriage) ? $data->life_after_marriage : '';
        $login_name = isset($data->login_name) ? $data->login_name : '';
        $marriage_date = isset($data->marriage_date) ? $data->marriage_date : '';
        $our_wedding_day = isset($data->our_edding_day) ? $data->our_edding_day : '';
        $partner_name = isset($data->partner_name) ? $data->partner_name : '';
        $ring_exchange_date = isset($data->ring_exchange_date) ? $data->ring_exchange_date : '';
        $the_proposal = isset($data->the_proposal) ? $data->the_proposal : '';
        $wedding_photo = isset($data->wedding_photo) ? $data->wedding_photo : '';
        $login_id = isset($data->login_id) ? $data->login_id : '';
        $root = env('FILE_UPLOAD_PATH');
        if ($id == '') {
            $image = explode(';base64,',  $wedding_photo);
            $image_base64 = base64_decode($image[1]);
            $extention = explode('/', $image[0]);
            $path = $root . '/successstory/';
            $uniqid = uniqid();
            $file = $path . $uniqid . '.' . $extention[1];
            if (file_put_contents($file, $image_base64)) {
                $insert = DB::table('success_story_by_user')->insert([
                    'life_after_marriage' => $life_after_marriage,
                    'login_name' => $login_name,
                    'marriage_date' => $marriage_date,
                    'our_wedding_day' => $our_wedding_day,
                    'partner_name' => $partner_name,
                    'ring_exchange_date' => $ring_exchange_date,
                    'the_proposal' => $the_proposal,
                    'how_we_met' => $how_we_met,
                    'wedding_photo' => $uniqid . '.' . $extention[1],
                    'login_id' => $login_id
                ]);
                if ($insert) {
                    $user_arr = array(
                        "success" => true,
                        "message" => "Data inserted successfully",
                    );
                } else {
                    $user_arr = array(
                        "success" => false,
                        "message" => "Data not inserted successfully",
                    );
                }
            }
        } elseif ($id > 0) {
            if (strpos($wedding_photo, '.') !== false) {
                $insert = DB::table('success_story_by_user')->where('id', $id)->update([
                    'life_after_marriage' => $life_after_marriage,
                    'login_name' => $login_name,
                    'marriage_date' => $marriage_date,
                    'our_wedding_day' => $our_wedding_day,
                    'partner_name' => $partner_name,
                    'ring_exchange_date' => $ring_exchange_date,
                    'the_proposal' => $the_proposal,
                    'how_we_met' => $how_we_met,
                    'status' => 1
                ]);
                if ($insert) {
                    $user_arr = array(
                        "success" => true,
                        "message" => "Data Updated successfully",
                    );
                } else {
                    $user_arr = array(
                        "success" => false,
                        "message" => "Data not inserted successfully",
                    );
                }
            } else {
                $image = explode(';base64,',  $wedding_photo);
                $image_base64 = base64_decode($image[1]);
                $extention = explode('/', $image[0]);
                $path = $root . '/successstory/';
                $uniqid = uniqid();
                $file = $path . $uniqid . '.' . $extention[1];
                if (file_put_contents($file, $image_base64)) {
                    $insert = DB::table('success_story_by_user')->where('id', $id)->update([
                        'life_after_marriage' => $life_after_marriage,
                        'login_name' => $login_name,
                        'marriage_date' => $marriage_date,
                        'our_wedding_day' => $our_wedding_day,
                        'partner_name' => $partner_name,
                        'ring_exchange_date' => $ring_exchange_date,
                        'the_proposal' => $the_proposal,
                        'how_we_met' => $how_we_met,
                        'wedding_photo' => $uniqid . '.' . $extention[1],
                        'status' => 1
                    ]);
                    if ($insert) {
                        $user_arr = array(
                            "success" => true,
                            "message" => "Data inserted successfully",
                        );
                    } else {
                        $user_arr = array(
                            "success" => false,
                            "message" => "Data not inserted successfully",
                        );
                    }
                }
            }
        } else {
            if (strpos($wedding_photo, '.') !== false) {
                $insert = DB::table('success_story_by_user')->insert([
                    'life_after_marriage' => $life_after_marriage,
                    'login_name' => $login_name,
                    'marriage_date' => $marriage_date,
                    'our_wedding_day' => $our_wedding_day,
                    'partner_name' => $partner_name,
                    'ring_exchange_date' => $ring_exchange_date,
                    'the_proposal' => $the_proposal,
                    'how_we_met' => $how_we_met,
                ]);
                if ($insert) {
                    $user_arr = array(
                        "success" => true,
                        "message" => "Data inserted successfully",
                    );
                } else {
                    $user_arr = array(
                        "success" => false,
                        "message" => "Data not inserted successfully",
                    );
                }
            } else {
                $image = explode(';base64,',  $wedding_photo);
                $image_base64 = base64_decode($image[1]);
                $extention = explode('/', $image[0]);
                $path = $root . '/successstory/';
                $uniqid = uniqid();
                $file = $path . $uniqid . '.' . $extention[1];
                if (file_put_contents($file, $image_base64)) {
                    $insert = DB::table('success_story_by_user')->insert([
                        'life_after_marriage' => $life_after_marriage,
                        'login_name' => $login_name,
                        'marriage_date' => $marriage_date,
                        'our_wedding_day' => $our_wedding_day,
                        'partner_name' => $partner_name,
                        'ring_exchange_date' => $ring_exchange_date,
                        'the_proposal' => $the_proposal,
                        'how_we_met' => $how_we_met,
                        'wedding_photo' => $uniqid . '.' . $extention[1]
                    ]);
                    if ($insert) {
                        $user_arr = array(
                            "success" => true,
                            "message" => "Data inserted successfully",
                        );
                    } else {
                        $user_arr = array(
                            "success" => false,
                            "message" => "Data not inserted successfully",
                        );
                    }
                }
            }
        }
        return json_encode($user_arr);
    }
}
