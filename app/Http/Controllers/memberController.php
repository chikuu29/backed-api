<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
       // dd($input);
        date_default_timezone_set('Asia/Kolkata');
        $curenttim = date("Y-m-d h:i:s");
        if ($input == null) {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Where is your data!",
            );
        } else {
            $id = $input['id'] == null ? '' : $input['id'];
            $curentplan = $input['curentplan'] == null ? '' : $input['curentplan'];
            $palnType = $input['palnType'] == null ? '' : $input['palnType'];
            $paymentType = $input['paymentType'] == null ? '' : $input['paymentType'];
            $planId = $input['planId'] == null ? '' : $input['planId'];
            $membership_plan_table = DB::table('membership_plan')->where('membership_plan_default', 1)->first();
            $userdeatils = DB::table('user_info')->where('user_id', $id)->get();
            $ispresentplan = DB::table('plan_details')->where('User_id', $id)->exists();
           
            if ($membership_plan_table->membership_plan_type == $curentplan) {
                //if user is free user then direct all active plan updated in edited_plan_details
                $activecurentplan = DB::table('user_plan_deatils')->where('user_id', $id)->update([
                    'active_status' => 0
                ]);
                if ($activecurentplan) {
                    $plan = DB::table('membership_plan')->where('membership_plan_id', $planId)->get();
                    $ispresent = DB::table('edited_plan_details')->where('User_id', $id)->exists();
                    if ($ispresentplan) {
                        $plan_details = DB::table('plan_details')->where('User_id', $id)->update([
                            'photoviwe' => $plan[0]->membership_plan_no_of_photo,
                            'sendmessage' => $plan[0]->membership_plan_of_send_message,
                            'horscope' => $plan[0]->membership_plan_no_of_horscope,
                            'contact_view' => $plan[0]->membership_plan_no_of_contact,
                            'contact_view_other' => $plan[0]->membership_plan_show_contact_number_other,
                            'chating' => $plan[0]->membership_plan_chating,
                            'profile_viwe' => $plan[0]->membership_plan_visibility
                        ]);
                    } else {
                        $plan_details = DB::table('plan_details')->insert([
                            'user_id' => $id,
                            'photoviwe' => $plan[0]->membership_plan_no_of_photo,
                            'sendmessage' => $plan[0]->membership_plan_of_send_message,
                            'horscope' => $plan[0]->membership_plan_no_of_horscope,
                            'contact_view' => $plan[0]->membership_plan_no_of_contact,
                            'contact_view_other' => $plan[0]->membership_plan_show_contact_number_other,
                            'chating' => $plan[0]->membership_plan_chating,
                            'profile_viwe' => $plan[0]->membership_plan_visibility
                        ]);
                    }
                    if ($ispresent) {
                        $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $id)->update([
                            'photoviwe' => $plan[0]->membership_plan_no_of_photo,
                            'sendmessage' => $plan[0]->membership_plan_of_send_message,
                            'horscope' => $plan[0]->membership_plan_no_of_horscope,
                            'contact_view' => $plan[0]->membership_plan_no_of_contact,
                            'contact_view_other' => $plan[0]->membership_plan_show_contact_number_other,
                            'chating' => $plan[0]->membership_plan_chating,
                            'profile_viwe' => $plan[0]->membership_plan_visibility
                        ]);
                    } else {
                        $edited_plan_details = DB::table('edited_plan_details')->insert([
                            'user_id' => $id,
                            'photoviwe' => $plan[0]->membership_plan_no_of_photo,
                            'sendmessage' => $plan[0]->membership_plan_of_send_message,
                            'horscope' => $plan[0]->membership_plan_no_of_horscope,
                            'contact_view' => $plan[0]->membership_plan_no_of_contact,
                            'contact_view_other' => $plan[0]->membership_plan_show_contact_number_other,
                            'chating' => $plan[0]->membership_plan_chating,
                            'profile_viwe' => $plan[0]->membership_plan_visibility
                        ]);
                    }

                    $Date = date('Y-m-d h:i:s');
                    $valid = date('Y-m-d h:i:s', strtotime($Date . ' +' . $plan[0]->membership_plan_validity_date . 'days'));
                    $insertdata = DB::table('user_info')->where('user_id', $id)->update([
                        'user_membership_plan_type' => $plan[0]->membership_plan_type,
                    ]);
                    $insertdatain_user_plan_deatils = DB::table('user_plan_deatils')->insert([
                        'user_id' => $id,
                        'user_email' => $userdeatils[0]->user_email,
                        'user_plan_type' => $plan[0]->membership_plan_type,
                        'user_plan_id' => $plan[0]->membership_plan_id,
                        'plan_ending_date' => $valid,
                        'planactiveted_mode' => $paymentType
                    ]);

                    if ($edited_plan_details && $insertdatain_user_plan_deatils && $ispresentplan) {

                        $user_arr = array(
                            "status" => true,
                            "success" => true,
                            "message" => "Update Successfully!",
                        );
                    } else {
                        $user_arr = array(
                            "status" => false,
                            "success" => false,
                            "message" => "Not Update Successfully!",
                        );
                    }
                }
            } else {
                
                $membership_plan_table = DB::table('membership_plan')->where('membership_plan_id', $planId)->where('membership_plan_default', 0)->get();
                $avtiveplan = DB::table('user_plan_deatils')->where('user_id', $id)->where('active_status', 1)->first();
                $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $id)->get();

                $plan_end_date = $avtiveplan->plan_ending_date;

                $curentdateinstring = strtotime($curenttim);
                $expirydate = strtotime($plan_end_date);
                $plan_expire_in_days = $expirydate - $curentdateinstring;
                // return round($plan_expire_in_days / 86400);
                if (round($plan_expire_in_days / 86400) < 0) {
                    //dd('1');
                    $plan = DB::table('membership_plan')->where('membership_plan_id', $planId)->get();
                    $ispresent = DB::table('edited_plan_details')->where('User_id', $id)->exists();
                    $ispresentplan = DB::table('plan_details')->where('User_id', $id)->exists();
                    $activecurentplan = DB::table('user_plan_deatils')->where('user_id', $id)->update([
                        'active_status' => 0
                    ]);
                    if ($ispresentplan) {
                        $plan_details = DB::table('plan_details')->where('User_id', $id)->update([
                            'photoviwe' => $plan[0]->membership_plan_no_of_photo,
                            'sendmessage' => $plan[0]->membership_plan_of_send_message,
                            'horscope' => $plan[0]->membership_plan_no_of_horscope,
                            'contact_view' => $plan[0]->membership_plan_no_of_contact,
                            'contact_view_other' => $plan[0]->membership_plan_show_contact_number_other,
                            'chating' => $plan[0]->membership_plan_chating,
                            'profile_viwe' => $plan[0]->membership_plan_visibility
                        ]);
                    } else {
                        $plan_details = DB::table('plan_details')->insert([
                            'user_id' => $id,
                            'photoviwe' => $plan[0]->membership_plan_no_of_photo,
                            'sendmessage' => $plan[0]->membership_plan_of_send_message,
                            'horscope' => $plan[0]->membership_plan_no_of_horscope,
                            'contact_view' => $plan[0]->membership_plan_no_of_contact,
                            'contact_view_other' => $plan[0]->membership_plan_show_contact_number_other,
                            'chating' => $plan[0]->membership_plan_chating,
                            'profile_viwe' => $plan[0]->membership_plan_visibility
                        ]);
                    }
                    if ($ispresent) {
                        $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $id)->update([
                            'photoviwe' => $plan[0]->membership_plan_no_of_photo,
                            'sendmessage' => $plan[0]->membership_plan_of_send_message,
                            'horscope' => $plan[0]->membership_plan_no_of_horscope,
                            'contact_view' => $plan[0]->membership_plan_no_of_contact,
                            'contact_view_other' => $plan[0]->membership_plan_show_contact_number_other,
                            'chating' => $plan[0]->membership_plan_chating,
                            'profile_viwe' => $plan[0]->membership_plan_visibility
                        ]);
                    } else {
                        $edited_plan_details = DB::table('edited_plan_details')->insert([
                            'user_id' => $id,
                            'photoviwe' => $plan[0]->membership_plan_no_of_photo,
                            'sendmessage' => $plan[0]->membership_plan_of_send_message,
                            'horscope' => $plan[0]->membership_plan_no_of_horscope,
                            'contact_view' => $plan[0]->membership_plan_no_of_contact,
                            'contact_view_other' => $plan[0]->membership_plan_show_contact_number_other,
                            'chating' => $plan[0]->membership_plan_chating,
                            'profile_viwe' => $plan[0]->membership_plan_visibility
                        ]);
                    }

                    $Date = date('Y-m-d h:i:s');
                    $valid = date('Y-m-d h:i:s', strtotime($Date . ' +' . $plan[0]->membership_plan_validity_date . 'days'));
                    $insertdata = DB::table('user_info')->where('user_id', $id)->update([
                        'user_membership_plan_type' => $plan[0]->membership_plan_type,
                    ]);
                    $insertdatain_user_plan_deatils = DB::table('user_plan_deatils')->insert([
                        'user_id' => $id,
                        'user_email' => $userdeatils[0]->user_email,
                        'user_plan_type' => $plan[0]->membership_plan_type,
                        'user_plan_id' => $plan[0]->membership_plan_id,
                        'plan_ending_date' => $valid,
                        'planactiveted_mode' => $paymentType
                    ]);


                    if ($edited_plan_details && $insertdatain_user_plan_deatils && $ispresentplan) {

                        $user_arr = array(
                            "status" => true,
                            "success" => true,
                            "message" => "Update Successfully!",
                        );
                    } else {
                        $user_arr = array(
                            "status" => false,
                            "success" => false,
                            "message" => "Not Update Successfully!",
                        );
                    }

                } else {
                   // return 'e';
                    $plan = DB::table('membership_plan')->where('membership_plan_id', $planId)->get();
                    $ispresent = DB::table('edited_plan_details')->where('User_id', $id)->exists();
                    $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $id)->get();
                    
                    //return  $edited_plan_details;
                    $ispresentplan = DB::table('plan_details')->where('User_id', $id)->exists();

                    $final_photoviwe = $edited_plan_details[0]->photoviwe + $plan[0]->membership_plan_no_of_photo;
                    $final_sendmessage = $edited_plan_details[0]->sendmessage + $plan[0]->membership_plan_of_send_message;
                    $final_horscope = $edited_plan_details[0]->horscope + $plan[0]->membership_plan_no_of_horscope;
                    $final_contact_view = $edited_plan_details[0]->contact_view + $plan[0]->membership_plan_no_of_contact;
                    $final_contact_view_other = $edited_plan_details[0]->contact_view_other + $plan[0]->membership_plan_show_contact_number_other;
                    $final_chating = $edited_plan_details[0]->chating + $plan[0]->membership_plan_chating;
                    $finalprofile_viwe = $edited_plan_details[0]->profile_viwe + $plan[0]->membership_plan_visibility;
                    $plan_end_date = $avtiveplan->plan_ending_date;
                    if ($ispresentplan) {
                        $edited_plan_details = DB::table('plan_details')->where('User_id', $id)->update([
                            'photoviwe' => $final_photoviwe,
                            'sendmessage' => $final_sendmessage,
                            'horscope' => $final_horscope,
                            'contact_view' => $final_contact_view,
                            'contact_view_other' => $final_contact_view_other,
                            'chating' => $final_chating,
                            'profile_viwe' => $finalprofile_viwe
                        ]);
                    } else {
                        $edited_plan_details = DB::table('plan_details')->insert([
                            'user_id' => $id,
                            'photoviwe' => $final_photoviwe,
                            'sendmessage' => $final_sendmessage,
                            'horscope' => $final_horscope,
                            'contact_view' => $final_contact_view,
                            'contact_view_other' => $final_contact_view_other,
                            'chating' => $final_chating,
                            'profile_viwe' => $finalprofile_viwe
                        ]);
                    }
                    if ($ispresent) {
                        $edited_plan_details = DB::table('edited_plan_details')->where('User_id', $id)->update([
                            'photoviwe' => $final_photoviwe,
                            'sendmessage' => $final_sendmessage,
                            'horscope' => $final_horscope,
                            'contact_view' => $final_contact_view,
                            'contact_view_other' => $final_contact_view_other,
                            'chating' => $final_chating,
                            'profile_viwe' => $finalprofile_viwe
                        ]);
                    } else {
                        $edited_plan_details = DB::table('edited_plan_details')->insert([
                            'user_id' => $id,
                            'photoviwe' => $final_photoviwe,
                            'sendmessage' => $final_sendmessage,
                            'horscope' => $final_horscope,
                            'contact_view' => $final_contact_view,
                            'contact_view_other' => $final_contact_view_other,
                            'chating' => $final_chating,
                            'profile_viwe' => $finalprofile_viwe
                        ]);
                    }
                    $Date = date('Y-m-d h:i:s');
                    $valid = date('Y-m-d h:i:s', strtotime($Date . ' +' . $plan[0]->membership_plan_validity_date . 'days'));
                    $avtiveplan = DB::table('user_plan_deatils')->where('user_id', $id)->where('active_status', 1)->first();

                    $plan_end_date = $avtiveplan->plan_ending_date;
                   
                    $curentdateinstring = strtotime($curenttim);
                    $expirydate = strtotime($plan_end_date);
                    $plan_expire_in_days = $expirydate - $curentdateinstring;
                     $restday= round($plan_expire_in_days / 86400);
                    $finalexfitydate = date('Y-m-d h:i:s', strtotime($valid . ' +' . $restday . 'days'));
                    $activecurentplan = DB::table('user_plan_deatils')->where('user_id', $id)->update([
                        'active_status' => 0
                    ]);
                    $insertdata = DB::table('user_info')->where('user_id', $id)->update([
                        'user_membership_plan_type' => $plan[0]->membership_plan_type,
                    ]);
                    $insertdatain_user_plan_deatils = DB::table('user_plan_deatils')->insert([
                        'user_id' => $id,
                        'user_email' => $userdeatils[0]->user_email,
                        'user_plan_type' => $plan[0]->membership_plan_type,
                        'user_plan_id' => $plan[0]->membership_plan_id,
                        'plan_ending_date' => $finalexfitydate,
                        'planactiveted_mode' => $paymentType
                    ]);
                    if ($edited_plan_details && $insertdatain_user_plan_deatils && $ispresentplan) {

                        $user_arr = array(
                            "status" => true,
                            "success" => true,
                            "message" => "Update Successfully!",
                        );
                    } else {
                        $user_arr = array(
                            "status" => false,
                            "success" => false,
                            "message" => "Not Update Successfully!",
                        );
                    }

                }

            }
        }
        return json_encode($user_arr);
    }

}

