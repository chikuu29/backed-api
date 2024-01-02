<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\MyMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class mailcontroller extends Controller
{
    public function sendEmail()
    {
		
         $fadata['name'] = 'gyana';

       Mail::send('name',$fadata,function($message) use ($fadata) {
        $message->from('info@choicemarriage.com','choicemarriage');
        $message->to('cchiku1999@gmail.com','gyana')->subject($fadata['name']);
       });

			
        return response()->json(['message' => 'Email sent successfully']);
      
    }
	public function sendData(Request $res)
    {
		$input =  $data = json_decode(file_get_contents("php://input"), true);
		
		//return $input['ids'];
		$ids = (($input['ids'] == '') ? [] : $input['ids'] );
		$sendid = (($input['sendid'] == null) ? '' : $input['sendid'] );
		//return $sendid;
		if (count($input['ids']) > 0) {
                $elements = $input['ids'];
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $ids = implode(",", $quotedElements);
            } else {
                $ids = '""';
            }
		
	     $alluserusers = DB::select("select * from user_info as a left join user_education_occupations as b on a.user_id = b.user_ID left join                                user_locations as c on a.user_id = c.user_ID 
                                where a.Id  IN ($ids)");
		
		//$alluserusers = DB::table('user_info')->whereIn('Id',$ids)->get();
		 $senddata = DB::table('user_info')->where('user_id',$sendid)->get();
		 $icoin = DB::table('logo_table')->where('status',1)->get();
		// return $icoin;
         $fadata['name'] = $senddata[0]->user_fname;
         $fadata['user_email'] = $senddata[0]->user_email;
		 $fadata['Alluser'] =  $alluserusers;
		 $fadata['Subject'] =  'Find Your Matches';
		 $fadata['imageurl'] = 'https://admin.choicemarriage.com/api/storage/';
		 $fadata['logo'] = $icoin[0]->image;
		 //return $fadata['logo'];

       $maildata =   Mail::send('mail.sendmatchs',$fadata,function($message) use ($fadata) {
        $message->from('info@choicemarriage.com','choicemarriage');
        $message->to($fadata['user_email'],$fadata['name'])->subject($fadata['Subject']);
       });
		
		//return $maildata;

			
        return response()->json(['message' => 'Email sent successfully' , 'code' => 200]);
      
    }
}
