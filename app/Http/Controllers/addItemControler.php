<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Exception;

class addItemControler extends Controller
{
    public function socialMediaLink()
    {
        $data = json_decode(file_get_contents("php://input"));

        $facebook_link = $data->fb == '' ? '' : $data->fb;
        $whatsapp_no = !isset($data->wh) ? '' : $data->wh;
        $twitter_link = !isset($data->tw) ? '' : $data->tw;
        $youtub_link = !isset($data->yo) ? '' : $data->yo;
        $linkedin_link = !isset($data->li) ? '' : $data->li;
        $application_link = !isset($data->ai) ? '' : $data->ai;
        // dd($facebook_link);

        try {

            $social_media_links = DB::table('social_media_links')->where('id', 1)->update([
                'facebook_link' => $facebook_link,
                'whatsapp_no' => $whatsapp_no,
                'twitter_link' => $twitter_link,
                'linkedin_link' => $linkedin_link,
                'youtub_link' => $youtub_link,
                'application_link' => $application_link
                //'updatedon' => date();

            ]);
            if ($social_media_links > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => "Updated Successfully !",
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "Data NOT Inserted !",
                );
            }

        } catch (Exception $e) {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Exception Error",
            );
        }
        return json_encode($user_arr);
    }

    public function getsocialMediaLink()
    {

        try {

            $data = DB::table('social_media_links')->get();

            if (count($data) == 1) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => "data  found!",
                    "result" => $data
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "data not found!",
                    "result" => []
                );
            }



        } catch (\Exception $e) {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Exception",
                "result" => []
            );
        }

        return json_encode($user_arr);
    }

    public function country()
    {
        $data = json_decode(file_get_contents("php://input"));
        $status = !(isset($data->status)) ? '' : $data->status;

        if ($status == 21) {
            $country = !(isset($data->country)) ? '' : $data->country;
            $countryinsert = DB::table('countries')->insert([
                'country_name' => $country,
            ]);
            if ($countryinsert > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => "Data Inserted Successfully !",
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "Data not Inserted !",
                );
            }
        }
        if ($status == 211) {
            $countryalldata = DB::table('countries')->orderBy('name')->get();
            if (count($countryalldata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $countryalldata,
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
        }
        if ($status == 22) {
            $country = !(isset($data->country)) ? '' : $data->country;
            $countryalldata = DB::table('countries')->where('name', 'like', '%' . $country . '%')->get();
            if (count($countryalldata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $countryalldata,
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
    public function state()
    {
        $data = json_decode(file_get_contents("php://input"));
        $status = !(isset($data->status)) ? '' : $data->status;

        if ($status == 21) {
            $countryid = !(isset($data->countryid)) ? '' : $data->countryid;
            $state = !(isset($data->state)) ? '' : $data->state;
            $stateinsert = DB::table('states')->insert([
                'country_id' => $countryid,
                'state_name' => $state
            ]);
            if ($stateinsert > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => "Data Inserted Successfully !",
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "Data not Inserted !",
                );
            }
        }
        if ($status == 211) {
            $stateinsert = DB::table('states')->orderBy('country_id')->get();
            if (count($stateinsert) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $stateinsert,
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
        }
        if ($status == 22) {
            $country = !(isset($data->country)) ? '' : $data->country;
            $stateinsert = DB::table('states')->where('country_id', $country)->get();
            if (count($stateinsert) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $stateinsert,
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
        }
        if ($status == 23) {
            // dd('88');
            $country = !(isset($data->country)) ? '' : $data->country;
            $state = !(isset($data->state)) ? '' : $data->state;
            if ($country == '') {
                $stateinsert = DB::table('states')->where('name', 'like', '%' . $state . '%')->get();
                if (count($stateinsert) > 0) {
                    $user_arr = array(
                        "status" => true,
                        "success" => true,
                        "message" => $stateinsert,
                    );
                } else {
                    $user_arr = array(
                        "status" => false,
                        "success" => false,
                        "message" => [],
                    );
                }
            } else {
                $stateinsert = DB::table('states')->where('country_id', $country)->where('name', 'like', '%' . $state . '%')->get();
                if (count($stateinsert) > 0) {
                    $user_arr = array(
                        "status" => true,
                        "success" => true,
                        "message" => $stateinsert,
                    );
                } else {
                    $user_arr = array(
                        "status" => false,
                        "success" => false,
                        "message" => [],
                    );
                }
            }
        }

        return json_encode($user_arr);

    }
    public function zodiacs(){
        $data = json_decode(file_get_contents("php://input"));
        $status = !(isset($data->status)) ? '' : $data->status;

        if($status == 23){
            $allzodiacsdata = DB::table('zodiacs')->get();
            if(count($allzodiacsdata) > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $allzodiacsdata,
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
        }
        if($status == 24){
            $rasi = !(isset($data->rasi)) ? '' : $data->rasi;
            $allzodiacsdata = DB::table('zodiacs')->where('name','like','%'.$rasi.'%')->get();
            if(count($allzodiacsdata) > 0 ){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $allzodiacsdata,
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
        }
        return json_encode($user_arr);
    }
    public function nakshatra(){
        $data = json_decode(file_get_contents("php://input"));
        $status = !(isset($data->status)) ? '' : $data->status;
        if($status == 23){
            $allzodiacsdata = DB::table('nakshatra')->get();
            if(count($allzodiacsdata) > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $allzodiacsdata,
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
        }
        if($status == 24){
            $nakshatraname = !(isset($data->nakhetra)) ? '' : $data->nakhetra;
            $allzodiacsdata = DB::table('nakshatra')->where('nakshatra_name','like','%'.$nakshatraname.'%')->get();
            if(count($allzodiacsdata) > 0 ){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $allzodiacsdata,
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
        }
        return json_encode($user_arr);
 
    }
    public function annual_income(){
        $data = json_decode(file_get_contents("php://input"));
        $status = !(isset($data->status)) ? '' : $data->status;
        if($status == 23){
            $annualincomedata = DB::table('annual_income')->get();
            if(count($annualincomedata) > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $annualincomedata,
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
        }
        if($status == 24){
            $annualincome = !(isset($data->annualincome)) ? '' : $data->annualincome;
            $annualincomedata = DB::table('annual_income')->where('annualincome','like','%'.$annualincome.'%')->get();
            if(count($annualincomedata) > 0 ){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $annualincomedata,
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
        }
        if($status == 25){
              $annualincome = !(isset($data->annualincome)) ? '' : $data->annualincome;
              $annualincomeinput = DB::table('annual_income')->insert([
                'annualincome'=> $annualincome,
              ]);
              if($annualincomeinput > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => 'Data Inserted Successfully!',
                );
              }else{
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => 'Data Not Inserted!',
                );
              }

        }
        if($status == 26){
            $annualincome = !(isset($data->annualincome)) ? '' : $data->annualincome;
            $id = !(isset($data->id)) ? '' : $data->id;
            $annualincomeinput = DB::table('annual_income')->where('id',$id)->update([
              'annualincome'=> $annualincome,
            ]);
            if($annualincomeinput > 0){
              $user_arr = array(
                  "status" => true,
                  "success" => true,
                  "message" => 'Data Updated Successfully!',
              );
            }else{
              $user_arr = array(
                  "status" => true,
                  "success" => true,
                  "message" => 'Data Not Updated!',
              );
            }

        }
        if($status == 27){
            $id = !(isset($data->id)) ? '' : $data->id;
            $annualincomedata = DB::table('annual_income')->where('id',$id)->get();
            if(count($annualincomedata) > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $annualincomedata,
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
            
        }  
        
        return json_encode($user_arr);
  
    }
    public function city(){
        $data = json_decode(file_get_contents("php://input"));
        $status = !(isset($data->status)) ? '' : $data->status;
        if($status == 25){
            $citydata = DB::table('cities')->get();
             if(count($citydata) > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $citydata,
                );
             }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
             }
        }



        return json_encode($user_arr);
    }

    public function privacypolicy(){
        $data = json_decode(file_get_contents("php://input"));
        $status = !(isset($data->status)) ? '' : $data->status;
        if($status == 25){
            $alldata = DB::table('privacy_policy')->get();
            if(count($alldata) > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $alldata,
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
        }else if($status == 26){
            $privacypolicy = !(isset($data->privacypolicy)) ? '' : $data->privacypolicy;
            $datainserted = DB::table('privacy_policy')->insert([
                 'privacy_policy_content' =>  $privacypolicy
            ]);
            if($datainserted > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => 'Data Insert Successfully!',
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => 'Data Not Insert',
                );
            }
        }else if($status == 27){
            $privacypolicy = !(isset($data->privacypolicy)) ? '' : $data->privacypolicy;
            $id = !(isset($data->id)) ? '' : $data->id;
            $datainserted = DB::table('privacy_policy')->where('id',$id)->update([
                 'privacy_policy_content' =>  $privacypolicy
            ]);
            if($datainserted > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => 'Data Updated Successfully!',
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => 'Data Not Updated !',
                );
            }
        }
        return json_encode($user_arr);
    }
    public function contactus(){
        $data = json_decode(file_get_contents("php://input"));
        $status = !(isset($data->status)) ? '' : $data->status;
        if($status == 25){
            $alldata = DB::table('contactus')->get();
            if(count($alldata) > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $alldata,
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
        }else if($status == 26){
            $contactus = !(isset($data->contactus)) ? '' : $data->contactus;
            $datainserted = DB::table('contactus')->insert([
                 'contact_us_content' =>  $contactus
            ]);
            if($datainserted > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => 'Data Insert Successfully!',
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => 'Data Not Insert',
                );
            }
        }else if($status == 27){
            $contactus = !(isset($data->contactus)) ? '' : $data->contactus;
            $id = !(isset($data->id)) ? '' : $data->id;
            $datainserted = DB::table('contactus')->where('id',$id)->update([
                 'contact_us_content' =>  $contactus
            ]);
            if($datainserted > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => 'Data Updated Successfully!',
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => 'Data Not Updated !',
                );
            }
        }
        return json_encode($user_arr);
    }

    public function termandcondition(){
        $data = json_decode(file_get_contents("php://input"));
        $status = !(isset($data->status)) ? '' : $data->status;
        if($status == 25){
            $alldata = DB::table('termand_condition')->get();
            if(count($alldata) > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $alldata,
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
        }else if($status == 26){
            $termandcondition = !(isset($data->termandcondition)) ? '' : $data->termandcondition;
            $datainserted = DB::table('termand_condition')->insert([
                 'termand_condition_content' =>  $termandcondition
            ]);
            if($datainserted > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => 'Data Insert Successfully!',
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => 'Data Not Insert',
                );
            }
        }else if($status == 27){
            $termandcondition = !(isset($data->termandcondition)) ? '' : $data->termandcondition;
            $id = !(isset($data->id)) ? '' : $data->id;
            $datainserted = DB::table('termand_condition')->where('id',$id)->update([
                 'termand_condition_content' =>  $termandcondition
            ]);
            if($datainserted > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => 'Data Updated Successfully!',
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => 'Data Not Updated !',
                );
            }
        }
        return json_encode($user_arr);
    }

    public function aboutus(){
        $data = json_decode(file_get_contents("php://input"));
        $status = !(isset($data->status)) ? '' : $data->status;
        if($status == 25){
            $alldata = DB::table('about_us')->get();
            if(count($alldata) > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => $alldata,
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => [],
                );
            }
        }else if($status == 26){
            $aboutus = !(isset($data->aboutus)) ? '' : $data->aboutus;
            $datainserted = DB::table('about_us')->insert([
                 'about_us_content' =>  $aboutus
            ]);
            if($datainserted > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => 'Data Insert Successfully!',
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => 'Data Not Insert',
                );
            }
        }else if($status == 27){
            $aboutus = !(isset($data->aboutus)) ? '' : $data->aboutus;
            $id = !(isset($data->id)) ? '' : $data->id;
            $datainserted = DB::table('about_us')->where('id',$id)->update([
                 'about_us_content' =>  $aboutus
            ]);
            if($datainserted > 0){
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => 'Data Updated Successfully!',
                );
            }else{
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => 'Data Not Updated !',
                );
            }
        }
        return json_encode($user_arr);
    }
 
    
}