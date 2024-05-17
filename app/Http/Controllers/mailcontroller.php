<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\MyMail;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

use App\Jobs\sentevent;
use Illuminate\Support\Facades\Queue;


require_once __DIR__ . '../../../../config/constant.php';

class mailcontroller extends Controller
{
    public function sendEmail()
    {

        $fadata['name'] = 'gyana';

        Mail::send('name', $fadata, function ($message) use ($fadata) {
            $message->from('info@choicemarriage.com', 'choicemarriage');
            $message->to('cchiku1999@gmail.com', 'gyana')->subject($fadata['name']);
        });


        return response()->json(['message' => 'Email sent successfully']);
    }
    public function sendData(Request $res)
    {
        $input =  json_decode(file_get_contents("php://input"), true);

        //return $input['ids'];
        $ids = (($input['ids'] == '') ? [] : $input['ids']);
        $sendid = (($input['sendid'] == null) ? '' : $input['sendid']);
        $filePath = (($input['filePath'] == null) ? '' : $input['filePath']);
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
        $socialmedialinks = DB::table('social_media_links')->first();
        $senddata = DB::table('user_info')->where('user_id', $sendid)->get();
        $icoin = DB::table('logo_table')->where('status', 1)->get();
        // return $icoin;
        $fadata['fb'] = $socialmedialinks->facebook_link;
        $fadata['in'] = $socialmedialinks->insta_id;
        $fadata['x'] = $socialmedialinks->twitter_link;
        $fadata['yt'] = $socialmedialinks->youtub_link;
        $fadata['ld'] = $socialmedialinks->linkedin_link;
        $fadata['name'] = $senddata[0]->user_fname;
        $fadata['user_email'] = $senddata[0]->user_email;
        $fadata['Alluser'] =  $alluserusers;
        $fadata['Subject'] =  'Find Your Matches';
        $fadata['imageurl'] = $filePath . 'storage/logo_image/' . $icoin[0]->image;
        $fadata['logo'] = $icoin[0]->image;
        $fadata['date'] = date("d M Y");
        $fadata['baner'] = $filePath . 'storage/newm.jpg';
        $fadata['foter'] = $filePath . 'storage/bg.jpg';

        //return $fadata['logo'];
        try {
            $maildata =   Mail::send('mail.sendmatchs', $fadata, function ($message) use ($fadata) {
                $message->from('info@choicemarriage.com', 'choicemarriage');
                $message->to($fadata['user_email'], $fadata['name'])->subject($fadata['Subject']);
            });
        } catch (Exception $e) {
            return $e;
        }

        //return $maildata;


        return response()->json(['message' => 'Email sent successfully', 'code' => 200]);
    }
    public function sendMailForChange()
    {
        $input =  json_decode(file_get_contents("php://input"), true);
        $userid = ((empty($input['user_id'])) ? '' : $input['user_id']);
        $type = ((empty($input['type'])) ? '' : $input['type']);
        $olddata = ((empty($input['olddata'])) ? '' : $input['olddata']);
        $newdata = ((empty($input['newdata'])) ? '' : $input['newdata']);
        $filepath = ((empty($input['filepath'])) ? '' : $input['filepath']);

        $mailid = DB::table('user_info')->where('user_id', $userid)->first('user_email');
        $logo =  DB::table('logo_table')->where('status', 1)->first('image');
        $socialmedialinks = DB::table('social_media_links')->first();
        $fadata['fb'] = $socialmedialinks->facebook_link;
        $fadata['in'] = $socialmedialinks->insta_id;
        $fadata['x'] = $socialmedialinks->twitter_link;
        $fadata['yt'] = $socialmedialinks->youtub_link;
        $fadata['ld'] = $socialmedialinks->linkedin_link;
        $fadata['image'] = $logo->image;
        $fadata['userid'] = $userid;
        $fadata['type'] = $type;
        $fadata['olddata'] = $olddata;
        $fadata['newdata'] = $newdata;
        $fadata['mailid'] = $mailid->user_email;
        $fadata['date'] = date("d M Y");
        $fadata['Subject'] = $type . ' ' . 'Change Request';
        $fadata['imageurl'] = $filepath . 'storage/logo_image/' . $logo->image;
        $fadata['baner'] = $filepath . 'storage/update.jpg';
        $fadata['foter'] = $filepath . 'storage/bg.jpg';
        //dd($fadata);
        $maildata =   Mail::send('mail.updaterequest', $fadata, function ($message) use ($fadata) {
            $message->from('info@choicemarriage.com', 'choicemarriage');
            $message->to($fadata['mailid'])->subject($fadata['Subject']);
        });
    }
    public function sendCustmMail()
    {
        $input =  json_decode(file_get_contents("php://input"), true);
        $Subject = ((empty($input['Subject'])) ? '' : $input['Subject']);
        $mailIds = ((empty($input['mailIds'])) ? [] : $input['mailIds']);
        $messagedata = ((empty($input['message'])) ? '' : $input['message']);
        $filepath = ((empty($input['filepath'])) ? '' : $input['filepath']);
        $logo =  DB::table('logo_table')->where('status', 1)->first('image');
        $socialmedialinks = DB::table('social_media_links')->first();
        if (count($mailIds) > 0) {
            for ($i = 0; $i < count($mailIds); $i++) {
                $fadata['mailid'] = $mailIds[$i];
                $emailData = [
                    'view' => 'mail.custmail', // The view for the email content
                    'data' => [
                        'messagedata' => $messagedata,
                        'imageurl' => $filepath . 'storage/logo_image/' . $logo->image,
                        'Subject' => $Subject,
                        'fb' => $socialmedialinks->facebook_link,
                        'in' => $socialmedialinks->insta_id,
                        'x' => $socialmedialinks->twitter_link,
                        'yt' => $socialmedialinks->youtub_link,
                        'ld' => $socialmedialinks->linkedin_link
                    ],
                    'subject' => 'Registration Successful',
                    'from' => 'info@choicemarriage.com', // Sender email address
                    'from_name' => 'choicemarriage', // Sender name
                    'to' => $mailIds[$i], // Recipient email address
                ];


                Queue::push(new sentevent($emailData), '', 'emails');
            }
        }
    }
}
