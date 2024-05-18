<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\sentevent;
use Illuminate\Support\Facades\Queue;

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
            DB::beginTransaction();
            $data = DB::table('edited_plan_details')->where('User_id', $userid)->update([
                "horscope" => $substractiondata
            ]);
            if ($data) {
                DB::commit();
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => "Done"
                );
            } else {
                DB::rollBack();
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "Not Done database issue"
                );
            }
        } else {
            // DB::rollBack();
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Plan End"
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
    public function sendExpireMesagewhenpakageexpire(Request $res)
    {
        $data = $res->all();
        if ($data['match'] == 'cm') {
            $plans = DB::table('user_plan_deatils')->where('active_status', 1)->get();
            $filepath = 'https://choicemarriage.com/';
            foreach ($plans as $plan) {
                $expiryDate = strtotime($plan->plan_ending_date);
                $currentDate = time();
                if ($currentDate > $expiryDate) {
                    $logo =  DB::table('logo_table')->where('status', 1)->first('image');
                    $socialmedialinks = DB::table('social_media_links')->first();
                    $userEmail = $plan->user_email;
                    $fadata['mailid'] = $plan->user_email;
                    $emailData = [
                        'view' => 'mail.expired', // The view for the email content
                        'data' => [
                            'imageurl' => $filepath . 'storage/logo_image/' . $logo->image,
                            'fb' => $socialmedialinks->facebook_link,
                            'in' => $socialmedialinks->insta_id,
                            'x' => $socialmedialinks->twitter_link,
                            'yt' => $socialmedialinks->youtub_link,
                            'ld' => $socialmedialinks->linkedin_link,
                            'foter' => $filepath . 'storage/bg.jpg',
                            'baner' => $filepath . 'storage/mimg.jpg',
                            'membershipimg' => $filepath . 'storage/mdata.jpg'
                        ],
                        'subject' => 'Membership Expired',
                        'from' => 'info@choicemarriage.com', // Sender email address
                        'from_name' => 'choicemarriage', // Sender name
                        'to' => $plan->user_email, // Recipient email address

                    ];

                    //dd($emailData);

                    Queue::push(new sentevent($emailData), '', 'emails');
                }
            }
        }
    }
}
