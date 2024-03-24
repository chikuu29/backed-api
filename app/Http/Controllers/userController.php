<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Queue;

use DateTime;


class userController extends Controller
{
    public function addUserDataFirstApi(Request $res)
    {
        $data = json_decode(file_get_contents("php://input"));

        $profiletype = $data->profiletype ?? 'myself';
        $email = $data->email ?? '';
        $phone = $data->phone ?? '';
        $password = isset($data->password) ? md5($data->password) : '';
        $gender = $data->gender ?? '';
        $url = $data->url ?? '';
        $userreligion = $data->user_religion ?? '';
        $usercaste = $data->user_caste ?? '';
        $usersubcaste = $data->user_subcaste ?? '';
        $fname = $data->fname ?? '';
        $lname = $data->lname ?? '';
        $dob = $data->dob ?? '';
        $usermothertoungh = $data->user_mother_toungh ?? '';
        $usermaritalstatus = $data->user_marital_status ?? '';

        $iddata = DB::table('prefix_id')->get('prefix_id_name');
        $id = $iddata[0]->prefix_id_name;
        $userId = $id . chr(64 + rand(0, 26)) . rand(0, 9) . chr(64 + rand(0, 26)) . rand(0, 9) . chr(64 + rand(0, 26)) . rand(1000, 9999);

        // Create DateTime objects for the date of birth and current date
        $dateOfBirth = new DateTime($dob);
        $currentDate = new DateTime();
        // Calculate the difference in years between the two dates
        $age = $currentDate->diff($dateOfBirth)->y;
        if (empty($profiletype) || empty($email) || empty($phone) || empty($password) || empty($gender)) {
            $user_arr = [
                "status" => false,
                "success" => false,
                "message" => "Please Fill All Data",
            ];
        } else {
            $getAuthUserCount = DB::table('auth_user')
                ->where('auth_email', $email)
                ->orWhere('auth_phone_no', $phone)
                ->count();

            if ($getAuthUserCount == 0) {
                try {
                    DB::beginTransaction();
                    DB::transaction(function () use ($userId, $age, $profiletype, $gender, $email, $fname, $lname, $dob, $password, $phone, $url, $usermothertoungh, $usermaritalstatus, $userreligion, $usercaste, $usersubcaste) {
                        DB::table('auth_user')->insert([
                            'auth_ID' => $userId,
                            'auth_email' => $email,
                            'auth_password' => $password,
                            'auth_phone_no' => $phone,
                            'auth_name' => $fname . " " . $lname,
                        ]);

                        DB::table('user_info')->insert([
                            'user_id' => $userId,
                            'user_profileType' => $profiletype,
                            'user_phone_no' => $phone,
                            'user_gender' => $gender,
                            'user_email' => $email,
                            'user_fname' => $fname,
                            'user_lname' => $lname,
                            'user_dob' => $dob,
                            'status' => 1,
                            'deleted' => 1,
                            'user_mother_toungh' => $usermothertoungh,
                            'user_marital_status' => $usermaritalstatus,
                            'user_age' => $age,
                            'user_full_name' => $fname . ' ' . $lname
                        ]);

                        DB::table('user_religion')->insert([
                            'user_ID' => $userId,
                            'user_religion' => $userreligion,
                            'user_caste' => $usercaste,
                            'user_subcaste' => $usersubcaste,
                            'completed' => 1
                        ]);
                    });




                    $emailData = [
                        'view' => 'mail.registration', // The view for the email content
                        'data' => [
                            'user_email' => $email,
                            'name' => $fname,
                            'url' => $url,
                            'profile_id' => $userId
                        ],
                        'subject' => 'Registration Successful',
                        'from' => 'info@choicemarriage.com', // Sender email address
                        'from_name' => 'choicemarriage', // Sender name
                        'to' => $email, // Recipient email address
                        'to_name' => $fname, // Recipient name
                    ];


                    Queue::push(new SendEmailJob($emailData), '', 'emails');


                    $user_arr = [
                        "status" => true,
                        "success" => true,
                        "profileID" => $userId,
                        "message" => "Congratulations! Your Registration Done",
                    ];
                    DB::commit();
                } catch (\Exception $e) {
                    // Handle the exception
                    DB::rollback();
                    $user_arr = [
                        "status" => false,
                        "success" => false,
                        "message" => "An error occurred during registration: " . $e->getMessage(),
                    ];
                }
            } else {
                $user_arr = [
                    "status" => false,
                    "success" => false,
                    "message" => "Email or Phone number already exists!",
                ];
            }
        }

        return json_encode($user_arr);
    }
    // {


    //     $data = json_decode(file_get_contents("php://input"));
    //    // return $data;

    //     $profiletype = !isset($data->profiletype) ? 'myself' : $data->profiletype;

    //     $email = !isset($data->email) ? '' : $data->email;
    //     $phone = !isset($data->phone) ? '' : $data->phone;
    //     $password = !isset($data->password) ? '' : md5($data->password);
    //     $gender = !isset($data->gender) ? '' : $data->gender;
    //     $url = !isset($data->url) ? '' : $data->url;

    //     $userreligion=!isset($data->user_religion) ? '' : $data->user_religion;
    //     $usercaste=!isset($data->user_caste) ? '' : $data->user_caste;
    //     $usersubcaste=!isset($data->user_subcaste) ? '' : $data->user_subcaste;


    //     $fname = !isset($data->fname) ? '' : $data->fname; // :'';
    //     $lname = !isset($data->lname) ? '' : $data->lname; // :'';
    //     $dob = !isset($data->dob) ? '' : $data->dob;
    //     $usermothertoungh = !isset($data->user_mother_toungh) ? '' : $data->user_mother_toungh;
    //     $usermaritalstatus =!isset($data->user_marital_status) ? '' : $data->user_marital_status;
    //     $iddata = DB::table('prefix_id')->get('prefix_id_name');
    //     $id = $iddata[0]->prefix_id_name;
    //     $userId = $id . chr(64 + rand(0, 26)) . rand(0, 9) . chr(64 + rand(0, 26)) . rand(0, 9) . chr(64 + rand(0, 26)) . rand(1000, 9999);
    //     if (empty($profiletype) || empty($email) || empty($phone) || empty($password) || empty($gender)) {
    //         $user_arr = array(
    //             "status" => false,
    //             "success" => false,
    //             "message" => "Please Fill All Data",
    //         );
    //     }
    //     $getAuthUserCount = DB::table('auth_user')
    //         ->where('auth_email', $email)
    //         ->orWhere('auth_phone_no', $phone)
    //         ->count();
    //     if ($getAuthUserCount == 0) {

    //         try {
    //             DB::transaction(function () use ($userId, $profiletype, $gender, $email, $fname, $lname, $dob, $password, $phone, $url,$usermothertoungh,$usermaritalstatus,$userreligion,$usercaste,$usersubcaste) {
    //                 DB::table('auth_user')->insert([
    //                     'auth_ID' => $userId,
    //                     'auth_email' => $email,
    //                     'auth_password' => $password,
    //                     'auth_phone_no' => $phone,
    //                     'auth_name' => $fname . " " . $lname,
    //                 ]);

    //                 DB::table('user_info')->insert([
    //                     'user_id' => $userId,
    //                     'user_profileType' => $profiletype,
    //                     'user_gender' => $gender,
    //                     'user_email' => $email,
    //                     'user_fname' => $fname,
    //                     'user_lname' => $lname,
    //                     'user_dob' => $dob,
    //                     'status' => 1,
    //                     'deleted' => 1,
    //                     'user_mother_toungh'=> $usermothertoungh,
    //                     '$user_marital_status' => $usermaritalstatus
    //                 ]);
    //                 DB::table('user_religion')->insert([
    //                     'user_ID' => $userId,
    //                     'user_religion'=> $userreligion,
    //                     'user_caste' => $usercaste,
    //                     'user_subcaste' => $usersubcaste,
    //                 ]);
    //             });
    //             try {
    //                 $fadata['user_email'] = $email;
    //                 $fadata['name'] = $fname;
    //                 $fadata['url'] = $url;
    //                 $fadata['Subject'] = 'Registration Successfull';
    //                 Mail::send('mail.registation_alert', $fadata, function ($message) use ($fadata) {
    //                     $message->from('info@choicemarriage.com', 'choicemarriage');
    //                     $message->to($fadata['user_email'], $fadata['name'])->subject($fadata['Subject']);
    //                 });
    //             } catch (Exception $e) {
    //                 DB::rollBack();
    //             }

    //             $user_arr = [
    //                 "status" => true,
    //                 "success" => true,
    //                 "profileID" => $userId,
    //                 "message" => "Congratulations! Your Registration Done",
    //             ];
    //             DB::commit();
    //         } catch (\Exception $e) {
    //             // Handle the exception
    //             DB::rollback();
    //             $user_arr = [
    //                 "status" => false,
    //                 "success" => false,
    //                 "message" => "An error occurred during registration: " . $e->getMessage(),
    //             ];
    //         }
    //     } else {
    //         $user_arr = array(
    //             "status" => false,
    //             "success" => false,
    //             "message" => "Enter Email and Phone no already exist!",
    //         );
    //     }
    //     return json_encode($user_arr);
    // }

    public function addUserDataSecondApi(Request $res)
    {

        $data = json_decode(file_get_contents("php://input"));

        $fname = !isset($data->fname) ? '' : $data->fname; // :'';
        $lname = !isset($data->lname) ? '' : $data->lname; // :'';
        $dob = !isset($data->dob) ? '' : $data->dob;
        $profileID = !isset($data->profileID) ? '' : $data->profileID;

        if (empty($fname) || empty($lname) || empty($dob)) {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please Fill All Details",
            );
        } else {
        }
        $user = DB::table('user_info')->where('user_id', $profileID)
            ->update([
                'user_fname' => $fname,
                'user_lname' => $lname,
                'user_dob' => $dob,
            ]);
        $authuser = DB::table('auth_user')->where('auth_ID', $profileID)
            ->update([
                'auth_name' => $fname . " " . $lname
            ]);

        if ($user > 0 && $authuser > 0) {
            $user_arr = array(
                "status" => true,
                "success" => true,
                "message" => "Congratulation! your account setup done",
            );
        } else {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Data not Inserted Successfully !",
            );
        }
        return json_encode($user_arr);
    }

    public function fatchAllaDataByUserId(Request $res)
    {
        $data = $res->all();
        $userid = isset($data['userid']) ? $data['userid'] : '';

        try {
            $user_info = DB::table('user_info')->where('user_id', $userid)->first();
            $auth_user = DB::table('auth_user')->where('auth_ID', $userid)->first(['auth_phone_no']);
            $user_info->user_phone_no = $auth_user->auth_phone_no;
            $user_education_occupations = DB::table('user_education_occupations')->where('user_ID', $userid)->first();
            $user_religion = DB::table('user_religion')->where('user_ID', $userid)->first();
            $user_about = DB::table('user_about')->where('user_ID', $userid)->first();
            $user_diet_hobbies = DB::table('user_diet_hobbies')->where('user_ID', $userid)->first();
            $user_family = DB::table('user_family')->where('user_ID', $userid)->first();
            $user_locations = DB::table('user_locations')->where('user_ID', $userid)->first();
            $user_physical_details = DB::table('user_physical_details')->where('user_ID', $userid)->first();
            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $userid)->first();

            $user_profile_images = DB::table('user_profile_images')->where('user_ID', $userid)->get();
            $user_plan_deatils = DB::table('user_plan_deatils')->where('user_id', $userid)->get();
            $user_horoscope_deatils = DB::table('user_horoscope')->where('user_id', $userid)->first();
            //dd( @$user_profile_images[0]);
            if (@$user_info->user_has_complete_profile == 1 && @$user_education_occupations->completed == 1 && @$user_religion->completed == 1 && @$user_about->completed == 1 && @$user_diet_hobbies->completed == 1 && @$user_family->completed == 1 && @$user_locations->completed == 1 && @$user_physical_details->completed == 1 && @$user_profile_images[0]->completed == 1 && @$user_horoscope_deatils->completed == 1 && !(empty($user_partnerpreference))) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "user_info" => $user_info != null ? $user_info : [],
                    "user_education_occupations" => $user_education_occupations != null ? $user_education_occupations : [],
                    "user_religion" => $user_religion != null ? $user_religion : (object) [],
                    "user_about" => $user_about != null ? $user_about : [],
                    "user_diet_hobbies" => $user_diet_hobbies != null ? $user_diet_hobbies : [],
                    "user_family" => $user_family != null ? $user_family : [],
                    "user_locations" => $user_locations != null ? $user_locations : [],
                    "user_physical_details" => $user_physical_details != null ? $user_physical_details : [],
                    "user_profile_images" => $user_profile_images != null ? $user_profile_images : (object) [],
                    "user_partnerpreference" => $user_partnerpreference != null ? $user_partnerpreference : [],
                    "user_profile_status" => "Completed",
                    "user_plan_deatils" => $user_plan_deatils != null ? $user_plan_deatils : [],
                    "user_horoscope_deatils" => $user_horoscope_deatils != null ? $user_horoscope_deatils : (object) []
                );
            } else {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "user_info" => $user_info != null ? $user_info : [],
                    "user_education_occupations" => $user_education_occupations != null ? $user_education_occupations : [],
                    "user_religion" => $user_religion != null ? $user_religion : (object) [],
                    "user_about" => $user_about != null ? $user_about : [],
                    "user_diet_hobbies" => $user_diet_hobbies != null ? $user_diet_hobbies : [],
                    "user_family" => $user_family != null ? $user_family : [],
                    "user_locations" => $user_locations != null ? $user_locations : [],
                    "user_physical_details" => $user_physical_details != null ? $user_physical_details : [],
                    "user_partnerpreference" => $user_partnerpreference != null ? $user_partnerpreference : [],
                    "user_profile_images" => $user_profile_images != null ? $user_profile_images : [],
                    "user_profile_status" => "Not Completed",
                    "user_plan_deatils" => $user_plan_deatils != null ? $user_plan_deatils : [],
                    "user_horoscope_deatils" => $user_horoscope_deatils != null ? $user_horoscope_deatils : (object) []
                );
            }
        } catch (Exception $e) {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Error" . $e
            );
        }

        return json_encode($user_arr);
    }

    public function uploadImage(Request $request)
    {
        $alldata = $request->all();
        $targetDir = env('FILE_UPLOAD_PATH');

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        if ($request->hasFile('file')) {

            $maxFileSize = 5 * 1024 * 1024; // 5 MB in bytes
            $uploadedFileSize = $request->file('file')->getSize();

            if ($uploadedFileSize > $maxFileSize) {
                return response()->json([
                    "success" => false,
                    "message" => "File size exceeds the maximum allowed limit of 5 MB"
                ]);
            }
            $profile_id = $alldata['q'];

            $temp = $_FILES['file']['tmp_name'];

            $fileReadyToMove = md5(uniqid($profile_id . '_' . time(), true)) . '.' . $_FILES['file']['name'];

            $generatedFileName = str_replace($_FILES['file']['name'], 'jpg', $fileReadyToMove);
            move_uploaded_file($temp, $targetDir . '/' . $generatedFileName);


            $profile_image_table = DB::table('user_profile_images')->insert([
                'completed' => 1,
                'user_ID' => $profile_id,
                'user_feature_images' => $generatedFileName,
                'user_profile_images' => $generatedFileName
            ]);

            if ($profile_image_table) {
                return response()->json([
                    "success" => true,
                    "message" => "File Uploaded Successfully",
                    "data" => [
                        'completed' => 1,
                        'user_ID' => $profile_id,
                        'user_feature_images' => $generatedFileName,
                        'user_profile_images' => $generatedFileName
                    ]
                ]);
            } else {
                return response()->json([
                    "success" => false,
                    "message" => "Unable to Store Data"
                ]);
            }
        } else {
            return response()->json([
                "success" => false,
                "message" => "No file uploaded"
            ]);
        }
    }

    public function userActivation(Request $res)
    {
        $data = $res->all();
        $id = isset($data['id']) ? $data['id'] : '';

        $plan = DB::table('membership_plan')->where('membership_plan_default', 1)->get();

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
        if ($edited_plan_details && $plan_details) {

            $userdeatils = DB::table('user_info')->where('user_id', $id)->get();
            $user_plan_deatils = DB::table('user_plan_deatils')->where('user_id', $id)->exists();


            if ($user_plan_deatils) {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => 'Defult Plan Added',
                );
            } else {
                $Date = date('Y-m-d h:i:s');
                $valid = date('Y-m-d h:i:s', strtotime($Date . ' +' . $plan[0]->membership_plan_validity_date . 'days'));
                $insertdata = DB::table('user_info')->where('user_id', $id)->update([
                    'user_membership_plan_type' => $plan[0]->membership_plan_type,
                    'user_ready_for_active_account' => 0,
                    'user_membership_plan_active' => 1,
                    'user_status' => 'Approved'
                ]);
                $insertdatain_user_plan_deatils = DB::table('user_plan_deatils')->insert([
                    'user_id' => $id,
                    'user_email' => $userdeatils[0]->user_email,
                    'user_plan_type' => $plan[0]->membership_plan_type,
                    'user_plan_id' => $plan[0]->membership_plan_id,
                    'plan_ending_date' => $valid
                ]);

                if ($insertdata > 0 && $insertdatain_user_plan_deatils > 0) {
                    $user_arr = array(
                        "status" => true,
                        "success" => true,
                        "message" => 'Update Successfully! ',
                    );
                } else {
                    $user_arr = array(
                        "status" => false,
                        "success" => false,
                        "message" => 'Not Update Successfully! ',
                    );
                }
            }
        } else {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => 'Not Update Successfully! ',
            );
        }
        return json_encode($user_arr);
    }
    public function profileValidation(Request $res)
    {
        $data = $res->all();

        $id = isset($data['id']) ? $data['id'] : '';
        $profile_image_table = DB::table('user_profile_images')->where('user_ID', $id)->get();

        $user_info = DB::table('user_info')->where('user_id', $id)->update([
            'user_profile_image' => $profile_image_table[0]->user_profile_images
        ]);
        if ($user_info > 0) {
            $user_arr = array(
                "success" => true,
                "message" => "Approvaled",
            );
        } else {
            $user_arr = array(
                "success" => false,
                "message" => "Not Approvaled",
            );
        }
        return json_encode($user_arr);
    }
    public function getAllDataById(Request $res)
    {
        $data = $res->all();
        $id = isset($data['id']) ? $data['id'] : '';

        $alldata = DB::select("SELECT * FROM user_info
        LEFT JOIN user_religion ON user_info.user_id = user_religion.user_ID
        LEFT JOIN user_locations ON user_info.user_id = user_locations.user_ID
        LEFT JOIN user_family ON user_info.user_id = user_family.user_ID
        LEFT JOIN user_physical_details ON user_info.user_id = user_physical_details.user_ID
        LEFT JOIN user_about ON user_info.user_id = user_about.user_ID
        LEFT JOIN user_diet_hobbies ON user_info.user_id = user_diet_hobbies.user_ID
        LEFT JOIN user_education_occupations ON user_info.user_id = user_education_occupations.user_ID
        LEFT JOIN auth_user ON user_info.user_id = auth_user.auth_ID
        LEFT JOIN user_horoscope ON  user_info.user_id = user_horoscope.user_id
        WHERE user_info.user_id = '$id'");
        if (count($alldata) > 0) {
            $user_arr = array(
                "status" => true,
                "success" => true,
                "data" => $alldata,
                "message" => count($alldata) . ' records Match'
            );
        } else {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "No Match Found",
            );
        }
        return json_encode($user_arr);
    }
}
