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
            try{

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
            
            $BlockedProfile = 5; // No need to change this as it's not a count.
            
            $LikeProfile = DB::table('user_like')
                ->where('liked_by_profile_id', $userid)
                ->count();
            $LikeProfile = ($LikeProfile < 10) ? "0$LikeProfile" : $LikeProfile;
            
            $user_arr = [
                "status" => true,
                "success" => true,
                "iviewedcontact" => $iviewedcontact,
                "ViewedMyContact"=> $ViewedMyContact,
                "iviewedprofile" => $iviewedprofile,
                "viewedmyprofile" => $viewedmyprofile,
                "Shortlistedprofile" => $Shortlistedprofile,
                "BlockedProfile" => $BlockedProfile,
                "LikeProfile" => $LikeProfile,
            ];
            
            }catch(\Exception $e){
                $user_arr = [
                    "status" => false,
                    "success" => false,
                    "msg" => $e
                ];
            }

        }
        return json_encode($user_arr);

    }
}
