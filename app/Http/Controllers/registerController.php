<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


class registerController extends Controller
{
    function deleteRequest(Request $res)
    {
        $input = $res->all();
        $id = $input['id'] == '' || $input['id'] == null  ? '' : $input['id'];
        if ($id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Id missing"
            );
        } else {
            // Start the database transaction
            DB::beginTransaction();

            try {
                $userinfo = DB::table('user_info')->where('user_id', $id)->get(['user_whatsapp_no', 'user_full_name', 'user_email', 'user_phone_no']);

                foreach ($userinfo as $user) {
                    $newUser = [
                        'user_name' => $user->user_full_name,
                        'user_mailid' => $user->user_email,
                        'user_phone_number' => $user->user_phone_no,
                        'user_whatsapp_number' => $user->user_whatsapp_no,
                        'states' => 1
                    ];

                    // Update user_delete_request table based on $id
                    DB::table('user_delete_request')->where('user_id', $id)->update($newUser);
                    $tables = [
                        'user_info',
                        'auth_user',
                        'user_delete_request',
                        'user_diet_hobbies',
                        'user_education_occupations',
                        'user_family',
                        'user_horoscope',
                        'user_like',
                        'user_locations',
                        'user_partnerpreference',
                        'user_physical_details',
                        'user_plan_details',
                        'user_profile_images',
                        'user_religion'
                    ];
                    foreach ($tables as $table) {

                        if (Schema::hasColumn($table, 'user_id')) {

                            DB::table($table)->where('user_id', $id)->delete();
                        } elseif (Schema::hasColumn($table, 'auth_ID')) {

                            DB::table($table)->where('auth_ID', $id)->delete();
                        } elseif (Schema::hasColumn($table, 'user_ID')) {

                            DB::table($table)->where('user_ID', $id)->delete();
                        }

                    }
                    $user_arr = array(
                        "status" => true,
                        "success" => true,
                        "message" => "Deleted"
                    );
                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "DB Problem"
                );
            }
        }
        return json_encode($user_arr);
    }
}
