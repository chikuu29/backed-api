<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;


class memberController extends Controller
{
    public function memberpaln(Request $res)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($data == null) {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Where is your data!",
            );
        } else {

            $addarray = array(
                'membership_plan_id' => time() . rand(100, 999)
            );

            // print_r($input);
            // return ;
            //dd( $data );
            $finalarray = array_merge($data['value'], $addarray);
            try {
                $inputdata = DB::table('membership_plan')->insert($finalarray);
                $type = DB::table('type')->where('name', $finalarray['membership_plan_type'])->update([
                    "used" => 1
                ]);
                if ($inputdata > 0) {
                    $user_arr = array(
                        "status" => true,
                        "success" => true,
                        "message" => "Data Inserted Successfully !",
                    );
                } else {
                    $user_arr = array(
                        "status" => false,
                        "success" => false,
                        "message" => "Data Not Inserted !",
                    );
                }

            } catch (Exception $e) {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "Error" . $e,
                );
            }
        }
        return json_encode($user_arr);
        // dd($finalarray);
        // dd($input['value']['type']);


    }
    public function getAllData()
    {
        $data = json_decode(file_get_contents("php://input"));
        $id = !isset($data->id) || $data->id == null ? '' : $data->id;
        if ($id == '') {
            $alldata = DB::table('membership_plan')->get();
            if (count($alldata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $alldata,
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
        } else {
            $alldata = DB::table('membership_plan')->where('Id', $id)->get();
            if (count($alldata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $alldata,
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
        }
        return json_encode($user_arr);
    }
    function getMembersheepPlan(Request $res)
    {
        // dd($res->all());
        if ($res->all() == null) {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Where is your data!",
            );
        } else {
            $input = $res->all();
            try {

                $userId = $input['userId'];
                $avtiveplan = DB::table('user_plan_deatils')->where('user_id', $userId)->where('active_status', 1)->first();
                $curentplanid = $avtiveplan->user_plan_id;
                $plan = DB::table('membership_plan')->where('membership_plan_id', $curentplanid)->first();
                $plan_start_date = $avtiveplan->plan_stating_date;
                $plan_end_date = $avtiveplan->plan_ending_date;
                $plan_type = $avtiveplan->user_plan_type;
                $expire = DB::table('user_plan_deatils')->where('user_id', $userId)->where('active_status', 0)->get();
                date_default_timezone_set('Asia/Kolkata');
                $curenttim = date("Y-m-d h:i:s");
                $curentdateinstring = strtotime($curenttim);
                $expirydate = strtotime($plan_end_date);
                $plan_expire_in_days = $expirydate - $curentdateinstring;
                // dd($plan_expire_in_days);
                //dd();
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => "Done",
                    "profile_id" => $userId,
                    "current_active_plan" => $avtiveplan,
                    "currect_active_plan_id" => $avtiveplan->user_plan_id,
                    "plan_history" => $expire,
                    "plan_start_date" => $plan_start_date,
                    "plan_end_date" => $plan_end_date,
                    "plan_type" => $plan_type,
                    "currect_active_plan_information" => $plan,
                    "plan_expire_in_days" => (round($plan_expire_in_days / 86400))
                );

            } catch (\Throwable $th) {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "Where is your data!",
                    "ERROR" => $th
                );
            }

        }

        return json_encode($user_arr);
    }
    public function updateEditedPlanDetails(Request $res)
    {
        $input = $res->all();
        //dd($input);

        if ($input == null) {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Where is your data!",
            );
        } else {
            DB::beginTransaction();

            try {
                $id = $input['id'] == null ? '' : $input['id'];
                $userinfo = DB::table('user_info')->where('user_id', $id)->first();
                if ($userinfo->user_status == 'Approved') {
                    $curentplan = $input['curentplan'] == null ? '' : $input['curentplan'];
                    $palnType = $input['palnType'] == null ? '' : $input['palnType'];
                    $paymentType = $input['paymentType'] == null ? '' : $input['paymentType'];
                    $planId = $input['planId'] == null ? '' : $input['planId'];

                    $plan = DB::table('membership_plan')->where('membership_plan_id', $planId)->first();
                    $Membershipdefaultdlan = DB::table('membership_plan')->where('membership_plan_default', 1)->first();
                    $plandetails = DB::table('plan_details')->where('user_id', $id)->first();
                    $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $id)->first();
                    $arrayFromObject = get_object_vars($plandetails);
                    $userplandeatils = DB::table('user_plan_deatils')->where('user_id', $id)->where('active_status', 1)->first();
                    if ($curentplan == 'Free') {
                        $arrayFromObject['plan_deatils'] = "Previous plan: " . $curentplan . ". Current plan: " . $palnType . ". curent plan Taken at: " . date("Y-m-d");
                        unset($arrayFromObject['id']);
                        DB::table('left_plan')->insert($arrayFromObject);
                        $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $id)->update([
                            'photoviwe' => $plan->membership_plan_no_of_photo,
                            'sendmessage' => $plan->membership_plan_of_send_message,
                            'horscope' => $plan->membership_plan_no_of_horscope,
                            'contact_view' => $plan->membership_plan_no_of_contact,
                            'contact_view_other' => $plan->membership_plan_show_contact_number_other,
                            'chating' => $plan->membership_plan_chating,
                            'profile_viwe' => $plan->membership_plan_visibility
                        ]);
                        DB::table('plan_details')->where('User_id', $id)->update([
                            'photoviwe' => $plan->membership_plan_no_of_photo,
                            'sendmessage' => $plan->membership_plan_of_send_message,
                            'horscope' => $plan->membership_plan_no_of_horscope,
                            'contact_view' => $plan->membership_plan_no_of_contact,
                            'contact_view_other' => $plan->membership_plan_show_contact_number_other,
                            'chating' => $plan->membership_plan_chating,
                            'profile_viwe' => $plan->membership_plan_visibility
                        ]);
                        DB::table('user_plan_deatils')->where('user_id', $id)->update([
                            'active_status' => 0
                        ]);
                        $Date = date('Y-m-d h:i:s');
                        $valid = date('Y-m-d h:i:s', strtotime($Date . ' +' . $plan->membership_plan_validity_date . 'days'));
                        $insertdata = DB::table('user_info')->where('user_id', $id)->update([
                            'user_membership_plan_type' => $plan->membership_plan_type,
                        ]);
                        DB::table('user_plan_deatils')->insert([
                            'user_id' => $id,
                            'user_email' => $userinfo->user_email,
                            'user_plan_type' => $plan->membership_plan_type,
                            'user_plan_id' => $plan->membership_plan_id,
                            'plan_ending_date' => $valid,
                            'planactiveted_mode' => $paymentType
                        ]);
                        $user_arr = array(
                            "status" => true,
                            "success" => true,
                            "message" => "Plan Upgraded free to",
                        );

                    } else {
                        $date = Date($userplandeatils->plan_ending_date);
                        $currentDate = Date('Y-m-d h:i:s');
                        if ($date < $currentDate) {
                            $arrayFromObject['plan_deatils'] = "Previous expired .  Previous plan: " . $curentplan . ". Current plan: " . $palnType . ". curent plan Taken at: " . date("Y-m-d");
                            unset($arrayFromObject['id']);
                            DB::table('left_plan')->insert($arrayFromObject);
                            DB::table('edited_plan_details')->where('User_id', $id)->update([
                                'photoviwe' => $plan->membership_plan_no_of_photo,
                                'sendmessage' => $plan->membership_plan_of_send_message,
                                'horscope' => $plan->membership_plan_no_of_horscope,
                                'contact_view' => $plan->membership_plan_no_of_contact,
                                'contact_view_other' => $plan->membership_plan_show_contact_number_other,
                                'chating' => $plan->membership_plan_chating,
                                'profile_viwe' => $plan->membership_plan_visibility
                            ]);
                            DB::table('plan_details')->where('User_id', $id)->update([
                                'photoviwe' => $plan->membership_plan_no_of_photo,
                                'sendmessage' => $plan->membership_plan_of_send_message,
                                'horscope' => $plan->membership_plan_no_of_horscope,
                                'contact_view' => $plan->membership_plan_no_of_contact,
                                'contact_view_other' => $plan->membership_plan_show_contact_number_other,
                                'chating' => $plan->membership_plan_chating,
                                'profile_viwe' => $plan->membership_plan_visibility
                            ]);
                            DB::table('user_plan_deatils')->where('user_id', $id)->update([
                                'active_status' => 0
                            ]);
                            $Date = date('Y-m-d h:i:s');
                            $valid = date('Y-m-d h:i:s', strtotime($Date . ' +' . $plan->membership_plan_validity_date . 'days'));
                            $insertdata = DB::table('user_info')->where('user_id', $id)->update([
                                'user_membership_plan_type' => $plan->membership_plan_type,
                            ]);
                            DB::table('user_plan_deatils')->insert([
                                'user_id' => $id,
                                'user_email' => $userinfo->user_email,
                                'user_plan_type' => $plan->membership_plan_type,
                                'user_plan_id' => $plan->membership_plan_id,
                                'plan_ending_date' => $userplandeatils->plan_ending_date,
                                'planactiveted_mode' => $paymentType
                            ]);
                            $user_arr = array(
                                "status" => true,
                                "success" => true,
                                "message" => "Plan Upgraded ",
                            );
                        } else {
                            // Convert strings to DateTime objects

                            $userPlanEndingDate = $userplandeatils->plan_ending_date;
                            $currentDateTime = Carbon::now();
                            $planEndDate = Carbon::createFromFormat('Y-m-d H:i:s', $userPlanEndingDate);
                            $daysLeft = $currentDateTime->diffInDays($planEndDate, false); // false to get absolute value
                            if ($daysLeft > $plan->membership_plan_validity_date) {
                                $arrayFromObject['plan_deatils'] = "Previous not expired. Previous validity add.   Previous plan: " . $curentplan . ". Current plan: " . $palnType . ". curent plan Taken at: " . date("Y-m-d");
                                unset($arrayFromObject['id']);
                                DB::table('left_plan')->insert($arrayFromObject);
                                $final_photoviwe = $edited_plan_details->photoviwe + $plan->membership_plan_no_of_photo;
                                $final_sendmessage = $edited_plan_details->sendmessage + $plan->membership_plan_of_send_message;
                                $final_horscope = $edited_plan_details->horscope + $plan->membership_plan_no_of_horscope;
                                $final_contact_view = $edited_plan_details->contact_view + $plan->membership_plan_no_of_contact;
                                $final_contact_view_other = $edited_plan_details->contact_view_other + $plan->membership_plan_show_contact_number_other;
                                $final_chating = $edited_plan_details->chating + $plan->membership_plan_chating;
                                $finalprofile_viwe = $edited_plan_details->profile_viwe + $plan->membership_plan_visibility;
                                $plan_end_date = $userplandeatils->plan_ending_date;


                                $edited_plan_details = DB::table('plan_details')->where('User_id', $id)->update([
                                    'photoviwe' => $final_photoviwe,
                                    'sendmessage' => $final_sendmessage,
                                    'horscope' => $final_horscope,
                                    'contact_view' => $final_contact_view,
                                    'contact_view_other' => $final_contact_view_other,
                                    'chating' => $final_chating,
                                    'profile_viwe' => $finalprofile_viwe
                                ]);

                                $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $id)->update([
                                    'photoviwe' => $final_photoviwe,
                                    'sendmessage' => $final_sendmessage,
                                    'horscope' => $final_horscope,
                                    'contact_view' => $final_contact_view,
                                    'contact_view_other' => $final_contact_view_other,
                                    'chating' => $final_chating,
                                    'profile_viwe' => $finalprofile_viwe
                                ]);

                                DB::table('user_plan_deatils')->where('user_id', $id)->update([
                                    'active_status' => 0
                                ]);

                                $insertdata = DB::table('user_info')->where('user_id', $id)->update([
                                    'user_membership_plan_type' => $plan->membership_plan_type,
                                ]);

                                DB::table('user_plan_deatils')->insert([
                                    'user_id' => $id,
                                    'user_email' => $userplandeatils->user_email,
                                    'user_plan_type' => $plan->membership_plan_type,
                                    'user_plan_id' => $plan->membership_plan_id,
                                    'plan_ending_date' => $plan_end_date,
                                    'planactiveted_mode' => $paymentType
                                ]);
                                //dd('lll');
                                $user_arr = array(
                                    "status" => true,
                                    "success" => true,
                                    "message" => "Plan Upgraded ",
                                );
                            } else {
                                $arrayFromObject['plan_deatils'] = "Previous  expired. curent validity add.   Previous plan: " . $curentplan . ". Current plan: " . $palnType . ". curent plan Taken at: " . date("Y-m-d");
                                unset($arrayFromObject['id']);
                                DB::table('left_plan')->insert($arrayFromObject);
                                $final_photoviwe = $edited_plan_details->photoviwe + $plan->membership_plan_no_of_photo;
                                $final_sendmessage = $edited_plan_details->sendmessage + $plan->membership_plan_of_send_message;
                                $final_horscope = $edited_plan_details->horscope + $plan->membership_plan_no_of_horscope;
                                $final_contact_view = $edited_plan_details->contact_view + $plan->membership_plan_no_of_contact;
                                $final_contact_view_other = $edited_plan_details->contact_view_other + $plan->membership_plan_show_contact_number_other;
                                $final_chating = $edited_plan_details->chating + $plan->membership_plan_chating;
                                $finalprofile_viwe = $edited_plan_details->profile_viwe + $plan->membership_plan_visibility;
                                $plan_end_date = $userplandeatils->plan_ending_date;


                                $edited_plan_details = DB::table('plan_details')->where('User_id', $id)->update([
                                    'photoviwe' => $final_photoviwe,
                                    'sendmessage' => $final_sendmessage,
                                    'horscope' => $final_horscope,
                                    'contact_view' => $final_contact_view,
                                    'contact_view_other' => $final_contact_view_other,
                                    'chating' => $final_chating,
                                    'profile_viwe' => $finalprofile_viwe
                                ]);

                                $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $id)->update([
                                    'photoviwe' => $final_photoviwe,
                                    'sendmessage' => $final_sendmessage,
                                    'horscope' => $final_horscope,
                                    'contact_view' => $final_contact_view,
                                    'contact_view_other' => $final_contact_view_other,
                                    'chating' => $final_chating,
                                    'profile_viwe' => $finalprofile_viwe
                                ]);

                                DB::table('user_plan_deatils')->where('user_id', $id)->update([
                                    'active_status' => 0
                                ]);

                                $insertdata = DB::table('user_info')->where('user_id', $id)->update([
                                    'user_membership_plan_type' => $plan->membership_plan_type,
                                ]);
                                $Date = date('Y-m-d h:i:s');
                                $valid = date('Y-m-d h:i:s', strtotime($Date . ' +' . $plan->membership_plan_validity_date . 'days'));
                                DB::table('user_plan_deatils')->insert([
                                    'user_id' => $id,
                                    'user_email' => $userplandeatils->user_email,
                                    'user_plan_type' => $plan->membership_plan_type,
                                    'user_plan_id' => $plan->membership_plan_id,
                                    'plan_ending_date' => $valid,
                                    'planactiveted_mode' => $paymentType
                                ]);
                                //dd('lll');
                                $user_arr = array(
                                    "status" => true,
                                    "success" => true,
                                    "message" => "Plan Upgraded ",
                                );
                            }

                        }
                    }
                    DB::commit();
                } else {
                    $user_arr = array(
                        "status" => false,
                        "success" => false,
                        "message" => "Not Approved contact admin with User id " . $id,
                    );
                }
            } catch (Exception $e) {
                DB::rollback();
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "Contact To admin!",
                    "e" => $e
                );
            }

        }

        return json_encode($user_arr);
    }

}

