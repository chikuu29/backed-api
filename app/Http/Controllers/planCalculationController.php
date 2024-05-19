<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\sentevent;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Mail;

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
    public function byCastmatchesforindivisual($id) //done
    {
        // $data = json_decode(file_get_contents("php://input"));

        $user_id = $id;
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {

            try {
                $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
                $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
                // dd($user_partnerpreference);


                // dd($user_partnerpreference->user_employed_In);
                $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
                $gender = $user[0]->user_gender == "Male" ? "Female" : "Male";
                //dd($gender);
                $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
                // dd(count($user_activities));


                $fatchdata = DB::table('user_info')
                    ->select('*', 'user_info.Id AS usedid')

                    ->leftJoin('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')

                    ->leftJoin('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')

                    ->leftJoin('user_family', 'user_info.user_id', '=', 'user_family.user_ID')

                    ->leftJoin('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')

                    //->leftJoin('user_about', 'user_info.user_id', '=', 'user_about.user_ID')

                    //->leftJoin('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')

                    ->leftJoin('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')

                    ->leftJoin('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')

                    ->leftJoin('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id');

                if ($user_activities != '' && count($user_activities) > 0 && $user_activities != null) {
                    $user_block_list = $user_activities[0]->user_block_list;
                    $elements = explode(',', $user_block_list);
                    //dd($elements);
                    $fatchdata = $fatchdata->whereNotIn('user_info.user_id', $elements);
                }

                // Validate each field in a similar manner
                if ($user_partnerpreference->user_marital_status != '' && count($user_partnerpreference->user_marital_status) > 0 && $user_partnerpreference->user_marital_status != null) {
                    $fatchdata = $fatchdata->whereIn('user_info.user_marital_status', $user_partnerpreference->user_marital_status);
                }

                if ($user_partnerpreference->user_mother_toungh != '' && count($user_partnerpreference->user_mother_toungh) > 0 && $user_partnerpreference->user_mother_toungh != null) {
                    $fatchdata = $fatchdata->whereIn('user_info.user_mother_toungh', $user_partnerpreference->user_mother_toungh);
                    $user_mother_toungh = implode(',', $user_partnerpreference->user_mother_toungh);
                } else {
                    $user_mother_toungh = '""';
                }

                if ($user_partnerpreference->user_religion != '' && count($user_partnerpreference->user_religion) > 0 && $user_partnerpreference->user_religion != null) {
                    $fatchdata = $fatchdata->whereIn('user_religion.user_religion', $user_partnerpreference->user_religion);
                } else {
                    $user_religion = '""';
                }

                if ($user_partnerpreference->user_employed_In != '' && count($user_partnerpreference->user_employed_In) > 0 && $user_partnerpreference->user_employed_In != null) {
                    $fatchdata = $fatchdata->whereIn('user_education_occupations.user_employed_In', $user_partnerpreference->user_employed_In);
                } else {
                    $user_employed_In = '""';
                }

                if ($user_partnerpreference->user_occupation != '' && count($user_partnerpreference->user_occupation) > 0 && $user_partnerpreference->user_occupation != null) {
                    $fatchdata = $fatchdata->whereIn('user_education_occupations.user_occupation', $user_partnerpreference->user_occupation);
                    $user_occupation = implode(',', $user_partnerpreference->user_occupation);
                } else {
                    $user_occupation = '""';
                }



                if ($user_partnerpreference->user_country != '' && count($user_partnerpreference->user_country) > 0 && $user_partnerpreference->user_country != null) {
                    $fatchdata = $fatchdata->whereIn('user_locations.user_country', $user_partnerpreference->user_country);
                    $user_country = implode(',', $user_partnerpreference->user_country);
                } else {
                    $user_country = '""';
                }

                if ($user_partnerpreference->user_city != '' && count($user_partnerpreference->user_city) > 0 && $user_partnerpreference->user_city != null) {
                    $fatchdata = $fatchdata->whereIn('user_locations.user_city', $user_partnerpreference->user_city);
                    $user_city = implode(',', $user_partnerpreference->user_city);
                } else {
                    $user_city = '""';
                }

                if ($user_partnerpreference->user_state != '' && count($user_partnerpreference->user_state) > 0 && $user_partnerpreference->user_state != null) {
                    $fatchdata = $fatchdata->whereIn('user_locations.user_state', $user_partnerpreference->user_state);
                    $user_state = implode(',', $user_partnerpreference->user_state);
                } else {
                    $user_state = '""';
                }

                if ($user_partnerpreference->user_zodiacs != '' && count($user_partnerpreference->user_zodiacs) > 0 && $user_partnerpreference->user_zodiacs != null) {
                    $fatchdata = $fatchdata->whereIn('user_horoscope.user_zodiacs', $user_partnerpreference->user_zodiacs);
                    $zodiacs = implode(',', $user_partnerpreference->user_zodiacs);
                } else {
                    $zodiacs = '""';
                }

                if ($user_partnerpreference->user_nakshatra != '' && count($user_partnerpreference->user_nakshatra) > 0 && $user_partnerpreference->user_nakshatra != null) {
                    $fatchdata = $fatchdata->whereIn('user_horoscope.user_nakhyatra', $user_partnerpreference->user_nakshatra);
                    $nakshatra = implode(',', $user_partnerpreference->user_nakshatra);
                } else {
                    $nakshatra = '""';
                }

                if ($user_partnerpreference->user_gotra != '' && count($user_partnerpreference->user_gotra) > 0 && $user_partnerpreference->user_gotra != null) {
                    $fatchdata = $fatchdata->whereIn('user_horoscope.user_gotra', $user_partnerpreference->user_gotra);
                    $gotra = implode(',', $user_partnerpreference->user_gotra);
                } else {
                    $gotra = '""';
                }




                //dd(property_exists($user_partnerpreference, 'user_complextion'));
                if ($user_partnerpreference->user_complextion != '' && count($user_partnerpreference->user_complextion) > 0 && $user_partnerpreference->user_complextion != null) {
                    $fatchdata = $fatchdata->whereIn('user_physical_details.user_complextion', $user_partnerpreference->user_complextion);
                    //$gotra = implode(',', $user_partnerpreference->user_gotra);
                } else {
                    $gotra = '""';
                }

                if ($user_partnerpreference->user_body_type != '' && count($user_partnerpreference->user_body_type) > 0 && $user_partnerpreference->user_body_type != null) {
                    $fatchdata = $fatchdata->whereIn('user_physical_details.user_body_type', $user_partnerpreference->user_body_type);
                    //$gotra = implode(',', $user_partnerpreference->user_body_type);
                } else {
                    $gotra = '""';
                }

                if ($user_partnerpreference->user_highest_education != '' && count($user_partnerpreference->user_highest_education) > 0 && $user_partnerpreference->user_highest_education != null) {
                    $fatchdata = $fatchdata->whereIn('user_education_occupations.user_highest_education', $user_partnerpreference->user_highest_education);
                    //$gotra = implode(',', $user_partnerpreference->user_body_type);
                } else {
                    $gotra = '""';
                }
                if ($user_partnerpreference->user_deg != '' && count($user_partnerpreference->user_deg) > 0 && $user_partnerpreference->user_deg != null) {
                    $fatchdata = $fatchdata->whereIn('user_education_occupations.user_deg', $user_partnerpreference->user_deg);
                    //$gotra = implode(',', $user_partnerpreference->user_body_type);
                } else {
                    $gotra = '""';
                }


                if ($user_partnerpreference->user_family_value != '' && count($user_partnerpreference->user_family_value) > 0 && $user_partnerpreference->user_family_value != null) {
                    $fatchdata = $fatchdata->whereIn('user_family.user_family_value', $user_partnerpreference->user_family_value);
                    //$gotra = implode(',', $user_partnerpreference->user_body_type);
                } else {
                    $gotra = '""';
                }

                if ($user_partnerpreference->user_family_status != '' && count($user_partnerpreference->user_family_status) > 0 && $user_partnerpreference->user_family_status != null) {
                    $fatchdata = $fatchdata->whereIn('user_family.user_family_status', $user_partnerpreference->user_family_status);
                    //$gotra = implode(',', $user_partnerpreference->user_body_type);
                } else {
                    $gotra = '""';
                }
                if ($user_partnerpreference->user_mangalik != '' && $user_partnerpreference->user_mangalik != null) {
                    $fatchdata = $fatchdata->where('user_horoscope.user_mangalik', $user_partnerpreference->user_mangalik);
                    //$gotra = implode(',', $user_partnerpreference->user_body_type);
                } else {
                    $gotra = '""';
                }
                if ($user_partnerpreference->user_cast != '' && count($user_partnerpreference->user_cast) > 0 && $user_partnerpreference->user_cast != null) {
                    $fatchdata = $fatchdata->whereIn('user_religion.user_caste', $user_partnerpreference->user_cast);
                    $user_cast = implode(',', $user_partnerpreference->user_cast);
                } else {
                    $user_cast = '""';
                }
                $user_min_height = $user_partnerpreference->user_min_height;
                $user_max_height = $user_partnerpreference->user_max_height;
                if ($user_min_height  != '' && $user_max_height != '') {
                    $fatchdata = $fatchdata->whereBetween('user_physical_details.user_height', [$user_min_height, $user_max_height]);
                }
                $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
                $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
                if ($user_max_anual_income  != '' && $user_min_anual_income != '') {
                    $fatchdata = $fatchdata->whereBetween('user_education_occupations.user_anual_income', [$user_min_anual_income, $user_max_anual_income]);
                }
                $to_age = $user_partnerpreference->to_user_age;
                $from_age =  $user_partnerpreference->from_user_age;
                if ($to_age != '' && $from_age != '') {
                    $fatchdata = $fatchdata->whereBetween('user_info.user_age', [$from_age, $to_age]);
                }















                $fatchdata = $fatchdata
                    ->where('user_info.user_gender', $gender)
                    ->where('user_info.user_id', '!=', $user_id)
                    ->where('user_info.user_status', 'Approved')
                    ->where('user_info.deleted', 1)
                    ->where('user_info.status', 1)
                    ->get();
                //  dd($fatchdata);

                $shuffledData = $fatchdata->shuffle();
                //shuffle($shuffledData);

                if (count($shuffledData) > 0) {
                    $user_arr = array(
                        "status" => true,
                        "success" => true,
                        "data" => $shuffledData,
                        "message" => count($shuffledData) . ' records Match'
                    );
                } else {
                    $user_arr = array(
                        "status" => false,
                        "success" => false,
                        "data" => [],
                        "message" => "No Match Found",
                    );
                }
            } catch (Exception $e) {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "data" => [],
                    "message" => "No Match Found",
                );
            }
        }
        return json_encode($user_arr);
    }
    public function SendMatchaes(Request $res)
    {
        $input = $res->all();
        if ($input['match'] == 'mc') {
            $randomUsers = DB::table('user_info')
                ->join('user_partnerpreference', 'user_info.user_id', '=', 'user_partnerpreference.user_ID')
                ->inRandomOrder()
                ->take(4)
                ->select('user_info.user_id', 'user_info.user_email', 'user_info.user_fname')
                ->get();
            foreach ($randomUsers as $user) {
                $getmatch =  $this->byCastmatchesforindivisual($user->user_id);
                $arrayData = json_decode($getmatch, true);
                $myArray = [];
                if ($arrayData['status']) {
                    foreach ($arrayData['data'] as $id) {
                        $elements = $id['usedid'];
                        array_push($myArray, $elements);
                    };
                }
                if (count($myArray) > 0) {
                    $quotedElements = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $myArray);
                    $ids = implode(",", $quotedElements);

                    $alluserusers = DB::select("select * from user_info as a left join user_education_occupations as b on a.user_id = b.user_ID left join                                user_locations as c on a.user_id = c.user_ID
                where a.Id  IN ($ids)");
                    // dd($alluserusers);
                    $filePath = 'https://choicemarriage.com/';
                    //$alluserusers = DB::table('user_info')->whereIn('Id',$ids)->get();
                    $socialmedialinks = DB::table('social_media_links')->first();
                    //$senddata = DB::table('user_info')->where('user_id', $sendid)->get();
                    $icoin = DB::table('logo_table')->where('status', 1)->get();
                    // return $icoin;
                    $fadata['fb'] = $socialmedialinks->facebook_link;
                    $fadata['in'] = $socialmedialinks->insta_id;
                    $fadata['x'] = $socialmedialinks->twitter_link;
                    $fadata['yt'] = $socialmedialinks->youtub_link;
                    $fadata['ld'] = $socialmedialinks->linkedin_link;
                    $fadata['name'] = $user->user_fname;
                    $fadata['user_email'] = $user->user_email;
                    $fadata['Alluser'] =  $alluserusers;
                    $fadata['Subject'] =  'Find Your Matches';
                    $fadata['imageurl'] = $filePath . 'storage/logo_image/' . $icoin[0]->image;
                    $fadata['logo'] = $icoin[0]->image;
                    $fadata['date'] = date("d M Y");
                    $fadata['baner'] = $filePath . 'storage/newm.jpg';
                    $fadata['foter'] = $filePath . 'storage/bg.jpg';
                    try {
                        $maildata =   Mail::send('mail.sendmatchs', $fadata, function ($message) use ($fadata) {
                            $message->from('info@choicemarriage.com', 'choicemarriage');
                            $message->to($fadata['user_email'], $fadata['name'])->subject($fadata['Subject']);
                        });
                    } catch (Exception $e) {
                        return $e;
                    }
                }
            }
        }
    }
}
