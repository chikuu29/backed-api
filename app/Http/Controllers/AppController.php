<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;


class AppController extends Controller
{
    public function settings(Request $request)
    {

        try {
            //code...

            $executeQuery = [
                "logoList" => "SELECT * FROM logo_table WHERE status=1",
                "socialMediaList" => "SELECT * FROM social_media_links",
                "bannerImageList" => "SELECT * FROM banner_image WHERE status=1",
                "homePageContent" => "SELECT * FROM homepage_content",
                "homePageMainImage" => "SELECT * FROM homepage_icon"
            ];

            foreach ($executeQuery as $key => $query) {
                $fetchData = DB::select($query);

                if (count($fetchData) > 0) {
                    if ($key == 'bannerImageList') {
                        $executeQuery[$key] = $fetchData;
                    } else {
                        $executeQuery[$key] = $fetchData[0];
                    }
                } else {
                    $executeQuery[$key] = json_encode([]);
                }
            }
            return response()->json($executeQuery);
        } catch (Exception $e) {
            //throw $th;
            return response()->json(['error' => $e], 401);
        }

        // $dataoftable = DB::select();

    }
    public function getAllCount(Request $request)
    {
        $input = $request->all();
        $userid = isset($input['user_id']) ? $input['user_id'] : null;

        if ($userid == null) {
            $user_arr = [
                "status" => false,
                "success" => false,
                "msg" => 'id Not Given'
            ];
        } else {
            try {

                $iviewedcontact = DB::table('user_activities_for_contact_details')
                    ->where('profile_view_by_profile_id', $userid)
                    ->where('viewed_contact', 1)
                    ->count();
                $iviewedcontact = ($iviewedcontact < 10) ? "0$iviewedcontact" : $iviewedcontact;

                $ViewedMyContact = DB::table('user_activities_for_contact_details')
                    ->where('viewed_profile_id', $userid)
                    ->where('viewed_contact', 1)
                    ->count();
                $ViewedMyContact = ($ViewedMyContact < 10) ? "0$ViewedMyContact" : $ViewedMyContact;

                $iviewedprofile = DB::table('user_activities_for_view_profile')
                    ->where('profile_view_by_profile_id', $userid)
                    ->count();
                $iviewedprofile = ($iviewedprofile < 10) ? "0$iviewedprofile" : $iviewedprofile;

                $viewedmyprofile = DB::table('user_activities_for_view_profile')
                    ->where('viewed_profile_id', $userid)
                    ->count();
                $viewedmyprofile = ($viewedmyprofile < 10) ? "0$viewedmyprofile" : $viewedmyprofile;

                $Shortlistedprofile = DB::table('user_activities_for_shortlisted_profile')
                    ->where('shortlist_user_id', $userid)
                    ->count();
                $Shortlistedprofile = ($Shortlistedprofile < 10) ? "0$Shortlistedprofile" : $Shortlistedprofile;

                $BlockedProfile = DB::table('user_activities')
                    ->where('user_id', $userid)
                    ->first(['user_block_list']);
                $blcount = count(explode(',', $BlockedProfile->user_block_list));
                $BlockedProfiledata =  $blcount < 10 ? "0$blcount" : $blcount;
                $LikeProfile = DB::table('user_like')
                    ->where('liked_by_profile_id', $userid)
                    ->count();
                $LikeProfile = ($LikeProfile < 10) ? "0$LikeProfile" : $LikeProfile;

                $user_arr = [
                    "status" => true,
                    "success" => true,
                    "iviewedcontact" => $iviewedcontact,
                    "ViewedMyContact" => $ViewedMyContact,
                    "iviewedprofile" => $iviewedprofile,
                    "viewedmyprofile" => $viewedmyprofile,
                    "Shortlistedprofile" => $Shortlistedprofile,
                    "BlockedProfile" => $BlockedProfiledata,
                    "LikeProfile" => $LikeProfile,
                ];
            } catch (\Exception $e) {
                $user_arr = [
                    "status" => false,
                    "success" => false,
                    "msg" => $e
                ];
            }
        }
        return json_encode($user_arr);
    }
    public function feedback(Request $request)
    {

        // Retrieve all input data from the request and sanitize it
        $sanitizedInput = array_map('htmlspecialchars', $request->all());
        //return $sanitizedInput;
        // Insert the sanitized data into the 'enquir_feedback' table
        $insert =  DB::table('enquir_feedback')->insert([
            'Name' => $sanitizedInput['Name'],
            'Email' => $sanitizedInput['Email'],
            'Contac_No' => $sanitizedInput['Contac_No'],
            'Subject' => $sanitizedInput['Subject'],
            'Feedback' => $sanitizedInput['Feedback']
        ]);
        if ($insert) {
            $user_arr = [
                "status" => true,
                "success" => true,
                "msg" => 'data inseted'
            ];
        } else {
            $user_arr = [
                "status" => false,
                "success" => false,
                "msg" => 'error'
            ];
        }
        return json_encode($user_arr);
    }
    public function dataBaseBackup()
    {
        // Database connection parameters
        $host = env('DB_HOST');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $database = env('DB_DATABASE');

        // Backup file path
        $backupFile = 'C:/backup.sql'; // Specify the full path to your desired location on the C drive

        // Construct the mysqldump command
        $command = "mysqldump --host=$host --user=$username --password=$password $database > $backupFile";

        // Execute the command
        exec($command, $output, $returnCode);

        // Check if the backup was successful
        if ($returnCode === 0) {
            $response = [
                'success' => true,
                'message' => 'Backup successful.',
                'file_path' => $backupFile
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Backup failed.'
            ];
        }

        // Return JSON response
       // header('Content-Type: application/json');
        echo json_encode($response);
    }
}
