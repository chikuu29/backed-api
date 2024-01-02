<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class activityController extends Controller
{
    public function getLoginCount(){
        $data = DB::table('login_activity')->select('user_id', DB::raw("count(*) as count"))->groupBy('user_id')->get();
        if(count($data) > 0){
            $user_arr = array(
                "status" => true,
                "success" => true,
                "data" => $data,
                "message" => count($data) . ' records Match'
            );
        }else{
            $user_arr = array(
                "status" => false,
                "success" => false,
                "data" => [],
                "message" => 0 . ' records Match'
            );
        }
        return json_encode($user_arr);
    }
    public function getLikeCount(){
        $data = DB::table('user_like')->select('liked_by_profile_id', DB::raw("count(*) as count"))->groupBy('liked_by_profile_id')->where('isLiked',1)->get();
        if(count($data) > 0){
            $user_arr = array(
                "status" => true,
                "success" => true,
                "data" => $data,
                "message" => count($data) . ' records Match'
            );
        }else{
            $user_arr = array(
                "status" => false,
                "success" => false,
                "data" => [],
                "message" => 0 . ' records Match'
            );
        }
        return json_encode($user_arr);
    }
    public function getUserBlockList(){
        $data = DB::table('user_activities')->get();
        dd($data);

    }
}
