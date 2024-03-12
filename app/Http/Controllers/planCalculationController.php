<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class planCalculationController extends Controller
{
    public function callCalculation(Request $res)
    {
        $data = $res->all();
        $userid = isset($data['userid']) ? $data['userid'] : '';
        $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $userid)->first();
        if ($edited_plan_details->contact_view > 0) {
            $substractiondata = $edited_plan_details->contact_view - 1;
            //dd($substractiondata);
            $data = DB::table('edited_plan_details')->where('User_id', $userid)->update([
                "contact_view" => $substractiondata
            ]);
            if ($data) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => "Done"
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "Not Done"
                );
            }
        } else {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Used"
            );
        }
        return json_encode($user_arr);
    }
    public function sendMessageCalculation(Request $res)
    {
        $data = $res->all();
        $userid = isset($data['userid']) ? $data['userid'] : '';
        $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $userid)->first();
        if ($edited_plan_details->sendmessage > 0) {
            $substractiondata = $edited_plan_details->sendmessage - 1;
            //dd($substractiondata);
            $data = DB::table('edited_plan_details')->where('User_id', $userid)->update([
                "sendmessage" => $substractiondata
            ]);
            if ($data) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => "Done"
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "Not Done"
                );
            }
        } else {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Used"
            );
        }
        return json_encode($user_arr);
    }
    public function horscopeCalculation(Request $res)
    {
        $data = $res->all();
        $userid = isset($data['userid']) ? $data['userid'] : '';
        $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $userid)->first();
        if ($edited_plan_details->horscope > 0) {
            $substractiondata = $edited_plan_details->horscope - 1;
            //dd($substractiondata);
            $data = DB::table('edited_plan_details')->where('User_id', $userid)->update([
                "horscope" => $substractiondata
            ]);
            if ($data) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => "Done"
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "Not Done"
                );
            }
        } else {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Used"
            );
        }
        return json_encode($user_arr);
    }
    public function contactViewOtherCalculation(Request $res)
    {
        $data = $res->all();
        $userid = isset($data['userid']) ? $data['userid'] : '';
        $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $userid)->first();
        //dd($edited_plan_details->contact_view_other > 0);
        if ($edited_plan_details->contact_view_other > 0) {
            $substractiondata = $edited_plan_details->contact_view_other - 1;
            //dd($substractiondata);
            $data = DB::table('edited_plan_details')->where('User_id', $userid)->update([
                "contact_view_other" => $substractiondata
            ]);
            if ($data) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => "Done"
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "Not Done"
                );
            }
        } else {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Used Contact Admin"
            );
        }
        return json_encode($user_arr);
    }
    public function contactViewCalculation(Request $res)
    {
        $data = $res->all();
        $userid = isset($data['userid']) ? $data['userid'] : '';
        if ($userid == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Contact Admin"
            );
        } else {
            $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $userid)->first();
            //dd($edited_plan_details->contact_view_other > 0);
            if ($edited_plan_details->contact_view > 0) {
                $substractiondata = $edited_plan_details->contact_view - 1;
                //dd($substractiondata);
                $data = DB::table('edited_plan_details')->where('User_id', $userid)->update([
                    "contact_view" => $substractiondata
                ]);
                if ($data) {
                    $user_arr = array(
                        "status" => true,
                        "success" => true,
                        "message" => "Done"
                    );
                } else {
                    $user_arr = array(
                        "status" => false,
                        "success" => false,
                        "message" => "Not Done"
                    );
                }
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "Used Contact Admin"
                );
            }
        }
        return json_encode($user_arr);
    }
    public function profileView(Request $res)
    {

        $data = $res->all();
        $loginuserid = isset($data['loginuserid']) ? $data['loginuserid'] : '';
        $viewuserid = isset($data['viewuserid']) ? $data['viewuserid'] : '';
        $viwe = DB::table('user_activities_for_view_profile')->where('profile_view_by_profile_id', $loginuserid)->where('viewed_profile_id', $viewuserid)->exists();
        if (!$viwe) {
            $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $loginuserid)->first();
            if ($edited_plan_details->profile_viwe > 0) {
                $substractiondata = $edited_plan_details->profile_viwe - 1;
                //dd($substractiondata);
                $data = DB::table('edited_plan_details')->where('User_id', $loginuserid)->update([
                    "profile_viwe" => $substractiondata
                ]);
                if ($data) {
                    $user_arr = array(
                        "status" => true,
                        "success" => true,
                        "message" => "Done"
                    );
                } else {
                    $user_arr = array(
                        "status" => false,
                        "success" => false,
                        "message" => "Not Done"
                    );
                }
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "Used"
                );
            }
        } else {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Used"
            );
        }
        // $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $loginuserid)->first();
        // //dd($edited_plan_details->contact_view_other > 0);
        // if ($edited_plan_details->contact_view_other > 0) {
        //     $substractiondata = $edited_plan_details->contact_view_other - 1;
        //     //dd($substractiondata);
        //     $data = DB::table('edited_plan_details')->where('User_id', $loginuserid)->update([
        //         "contact_view_other" => $substractiondata
        //     ]);
        //     if ($data) {
        //         $user_arr = array(
        //             "status" => true,
        //             "success" => true,
        //             "message" => "Done"
        //         );
        //     } else {
        //         $user_arr = array(
        //             "status" => false,
        //             "success" => false,
        //             "message" => "Not Done"
        //         );
        //     }
        // } else {
        //     $user_arr = array(
        //         "status" => false,
        //         "success" => false,
        //         "message" => "Used"
        //     );
        // }
        return json_encode($user_arr);
    }
}
