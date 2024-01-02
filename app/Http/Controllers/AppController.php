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
                "homePageContent"=>"SELECT * FROM homepage_content",
                "homePageMainImage"=> "SELECT * FROM homepage_icon"
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
}
