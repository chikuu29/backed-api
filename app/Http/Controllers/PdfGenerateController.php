<?php

namespace App\Http\Controllers;


use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use \stdClass;

class PdfGenerateController extends Controller
{
    public function generatePDF(Request $request)
    {
        $input = $request->all();
        $userid = isset($input['user_id']) ? $input['user_id'] : '';
        $filepath = isset($input['filepath']) ? $input['filepath'] : '';
        $userinfo = DB::table('user_info')->where('user_id', $userid)->first(['user_dob', 'user_phone_no', 'country_code', 'user_whatsapp_no', 'whats_app_c_code', 'user_profile_image']);
        $user_education_occupations = DB::table('user_education_occupations')->where('user_id', $userid)->first(['user_highest_education', 'user_occupation', 'user_deg', 'user_anual_income', 'user_occupation_location']);
        if ($user_education_occupations == null) {
            $user_education_occupations = new stdClass();
            $user_education_occupations->user_highest_education = '';
            $user_education_occupations->user_occupation = '';
            $user_education_occupations->user_deg = '';
            $user_education_occupations->user_anual_income = '';
            $user_education_occupations->user_occupation_locatio = '';
            $user_education_occupations->user_occupation_location = '';
        }
        $user_locations = DB::table('user_locations')->where('user_id', $userid)->first('user_city');
        if ($user_locations == null) {
            $user_locations = new stdClass();
            $user_locations->user_city = '';
        }
        $user_physical_details = DB::table('user_physical_details')->where('user_id', $userid)->first(['user_complextion', 'user_height']);
        if ($user_physical_details == null) {
            $user_physical_details = new stdClass();
            $user_physical_details->user_complextion = '';
            $user_physical_details->user_height = '';
        }
        $user_horoscope = DB::table('user_horoscope')->where('user_id', $userid)->first(['user_zodiacs']);
        if ($user_horoscope == null) {
            $user_horoscope = new stdClass();
            $user_horoscope->user_zodiacs = '';
        }
        $social_media_links = DB::table('social_media_links')->where('id', 1)->first(['phone_no']);
        if ($user_physical_details->user_height == 0 || $user_physical_details->user_height == '') {
            $fitincconverted = 'NA';
        } else {
            $inches = $user_physical_details->user_height / 2.54;
            $feet = floor($inches / 12);
            $remainingInches = $inches % 12;
            $fitincconverted = $feet . 'ft' . ' ' . $remainingInches . 'in';
        }
        //dd($userinfo->user_dob);
        if ($userinfo->user_dob == '') {
            $formattedDate = '';
        } else {
            $timestamp = strtotime($userinfo->user_dob);
            $formattedDate = date('d M Y', $timestamp);
        }
        //dd($formattedDate);
        $maskedPhoneNumber = 'XXXXXXX' . substr($userinfo->user_phone_no, -3);
        $maskedwhatsapNumber = 'XXXXXXX' . substr($userinfo->user_whatsapp_no, -3);
        if ($user_education_occupations->user_anual_income == '') {
            $finalincome = 'NA';
        } else {
            $finalincome = $user_education_occupations->user_anual_income * 1000000;
        }

        $lodganeshimage = $filepath . 'storage/images.jpg';
        $lodganesh64concert = @base64_encode(file_get_contents($lodganeshimage));

        $profilrimgpath = $filepath . 'storage/' . $userinfo->user_profile_image;
        $profilebase64EncodedImage = @base64_encode(file_get_contents($profilrimgpath));
        if ($profilebase64EncodedImage == '') {
            $profilrimgpath = $filepath . 'storage/no_profile_picture_found.jpg';
            $profilebase64EncodedImage = base64_encode(file_get_contents($profilrimgpath));
        }
        if (pathinfo($profilrimgpath, PATHINFO_EXTENSION) == 'PNG' || pathinfo($profilrimgpath, PATHINFO_EXTENSION) == 'png') {
            // $source = imagecreatefrompng($profilrimgpath);
            // $jpgImagePath = 'http://localhost/storage/pngimg/image.jpg';
            // imagejpeg($source, $jpgImagePath, 100);
            // imagedestroy($source);
            // $profilebase64EncodedImage = @base64_encode(file_get_contents($jpgImagePath));
            $profilrimgpath = $filepath . 'storage/no_profile_picture_found.jpg';
            $profilebase64EncodedImage = base64_encode(file_get_contents($profilrimgpath));
        }
        $imagePath = $filepath . 'storage/logo.jpg';
        $base64EncodedImage = @base64_encode(file_get_contents($imagePath));
        $dynamicData = [
            'name' => 'John Doe',
            'age' => 30,
            'img' => 'data:image/' . pathinfo($imagePath, PATHINFO_EXTENSION) . ';base64,' . $base64EncodedImage,
            'profile' => 'data:image/' . pathinfo($profilrimgpath, PATHINFO_EXTENSION) . ';base64,' . $profilebase64EncodedImage,
            'lodganesh' => 'data:image/' . pathinfo($profilrimgpath, PATHINFO_EXTENSION) . ';base64,' . $lodganesh64concert,
            'dob' => $formattedDate == '' ? 'NA' : $formattedDate,
            'phone' => '+' . ($userinfo->country_code == '' ? 'NA' : $userinfo->country_code) . ' ' . ($maskedPhoneNumber == 'XXXXXXX' ? 'XXXXXXXNA' : $maskedPhoneNumber),
            'whats' => '+' . ($userinfo->whats_app_c_code == '' ? 'NA' : $userinfo->whats_app_c_code) . ' ' . ($maskedwhatsapNumber == 'XXXXXXX' ? 'XXXXXXXNA' : $maskedwhatsapNumber),
            'userhighesteducation' => $user_education_occupations->user_highest_education == '' ? 'NA' : $user_education_occupations->user_highest_education,
            'occupation' =>  $user_education_occupations->user_occupation == '' ? 'NA' : $user_education_occupations->user_occupation,
            'deg' => $user_education_occupations->user_deg == '' ? 'NA' : $user_education_occupations->user_deg,
            'joblocation' => $user_education_occupations->user_occupation_location == '' ? 'NA' : $user_education_occupations->user_occupation_location,
            'income' => $finalincome == 0 ? 'NA' : $finalincome,
            'hometown' => $user_locations->user_city == '' ? 'NA' : $user_locations->user_city,
            'id' => $userid,
            'hieght' => $fitincconverted,
            'color' => $user_physical_details->user_complextion == '' ? 'NA' : $user_physical_details->user_complextion,
            'rasi' => $user_horoscope->user_zodiacs == '' ? 'NA' : $user_horoscope->user_zodiacs,
            'phoneadmin' => $social_media_links->phone_no
        ];

        // Render the Blade view to HTML with dynamic data
        $html = view('pdf.demo', $dynamicData)->render();

        // Create an instance of the Dompdf class
        $dompdf = app(Dompdf::class);

        // Load HTML content
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        $filedname = $userid . date('d-M-Y') . '.pdf';

        // Output the generated PDF (inline or attachment)
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filedname . '"');
    }
    //


    public function generateMergePDF(Request $request)
    {
        $input = $request->all();
        $userid = isset($input['user_id']) ? $input['user_id'] : '';
        $filepath = isset($input['filepath']) ? $input['filepath'] : '';
        $userinfo = DB::table('user_info')->where('user_id', $userid)->first(['user_dob', 'user_phone_no', 'country_code', 'user_whatsapp_no', 'whats_app_c_code', 'user_profile_image']);
        $user_education_occupations = DB::table('user_education_occupations')->where('user_id', $userid)->first(['user_highest_education', 'user_occupation', 'user_deg', 'user_anual_income', 'user_occupation_location']);
        if ($user_education_occupations == null) {
            $user_education_occupations = new stdClass();
            $user_education_occupations->user_highest_education = '';
            $user_education_occupations->user_occupation = '';
            $user_education_occupations->user_deg = '';
            $user_education_occupations->user_anual_income = '';
            $user_education_occupations->user_occupation_locatio = '';
            $user_education_occupations->user_occupation_location = '';
        }
        $user_locations = DB::table('user_locations')->where('user_id', $userid)->first('user_city');
        if ($user_locations == null) {
            $user_locations = new stdClass();
            $user_locations->user_city = '';
        }
        $user_physical_details = DB::table('user_physical_details')->where('user_id', $userid)->first(['user_complextion', 'user_height']);
        if ($user_physical_details == null) {
            $user_physical_details = new stdClass();
            $user_physical_details->user_complextion = '';
            $user_physical_details->user_height = '';
        }
        $user_horoscope = DB::table('user_horoscope')->where('user_id', $userid)->first(['user_zodiacs']);
        if ($user_horoscope == null) {
            $user_horoscope = new stdClass();
            $user_horoscope->user_zodiacs = '';
        }
        $social_media_links = DB::table('social_media_links')->where('id', 1)->first(['phone_no']);
        if ($user_physical_details->user_height == 0 || $user_physical_details->user_height == '') {
            $fitincconverted = 'NA';
        } else {
            $inches = $user_physical_details->user_height / 2.54;
            $feet = floor($inches / 12);
            $remainingInches = $inches % 12;
            $fitincconverted = $feet . 'ft' . ' ' . $remainingInches . 'in';
        }
        //dd($userinfo->user_dob);
        if ($userinfo->user_dob == '') {
            $formattedDate = '';
        } else {
            $timestamp = strtotime($userinfo->user_dob);
            $formattedDate = date('d M Y', $timestamp);
        }
        //dd($formattedDate);
        $maskedPhoneNumber = 'XXXXXXX' . substr($userinfo->user_phone_no, -3);
        $maskedwhatsapNumber = 'XXXXXXX' . substr($userinfo->user_whatsapp_no, -3);
        if ($user_education_occupations->user_anual_income == '') {
            $finalincome = 'NA';
        } else {
            $finalincome = $user_education_occupations->user_anual_income * 1000000;
        }

        $lodganeshimage = $filepath . 'storage/images.jpg';
        $lodganesh64concert = @base64_encode(file_get_contents($lodganeshimage));

        $profilrimgpath = $filepath . 'storage/' . $userinfo->user_profile_image;
        $profilebase64EncodedImage = @base64_encode(file_get_contents($profilrimgpath));
        if ($profilebase64EncodedImage == '') {
            $profilrimgpath = $filepath . 'storage/no_profile_picture_found.jpg';
            $profilebase64EncodedImage = base64_encode(file_get_contents($profilrimgpath));
        }
        if (pathinfo($profilrimgpath, PATHINFO_EXTENSION) == 'PNG' || pathinfo($profilrimgpath, PATHINFO_EXTENSION) == 'png') {
            // $source = imagecreatefrompng($profilrimgpath);
            // $jpgImagePath = 'http://localhost/storage/pngimg/image.jpg';
            // imagejpeg($source, $jpgImagePath, 100);
            // imagedestroy($source);
            // $profilebase64EncodedImage = @base64_encode(file_get_contents($jpgImagePath));
            $profilrimgpath = $filepath . 'storage/no_profile_picture_found.jpg';
            $profilebase64EncodedImage = base64_encode(file_get_contents($profilrimgpath));
        }
        $imagePath = $filepath . 'storage/logo.jpg';
        $base64EncodedImage = @base64_encode(file_get_contents($imagePath));
        $dynamicData = [[
            'name' => 'John Doe',
            'age' => 30,
            'img' => 'data:image/' . pathinfo($imagePath, PATHINFO_EXTENSION) . ';base64,' . $base64EncodedImage,
            'profile' => 'data:image/' . pathinfo($profilrimgpath, PATHINFO_EXTENSION) . ';base64,' . $profilebase64EncodedImage,
            'lodganesh' => 'data:image/' . pathinfo($profilrimgpath, PATHINFO_EXTENSION) . ';base64,' . $lodganesh64concert,
            'dob' => $formattedDate == '' ? 'NA' : $formattedDate,
            'phone' => '+' . ($userinfo->country_code == '' ? 'NA' : $userinfo->country_code) . ' ' . ($maskedPhoneNumber == 'XXXXXXX' ? 'XXXXXXXNA' : $maskedPhoneNumber),
            'whats' => '+' . ($userinfo->whats_app_c_code == '' ? 'NA' : $userinfo->whats_app_c_code) . ' ' . ($maskedwhatsapNumber == 'XXXXXXX' ? 'XXXXXXXNA' : $maskedwhatsapNumber),
            'userhighesteducation' => $user_education_occupations->user_highest_education == '' ? 'NA' : $user_education_occupations->user_highest_education,
            'occupation' =>  $user_education_occupations->user_occupation == '' ? 'NA' : $user_education_occupations->user_occupation,
            'deg' => $user_education_occupations->user_deg == '' ? 'NA' : $user_education_occupations->user_deg,
            'joblocation' => $user_education_occupations->user_occupation_location == '' ? 'NA' : $user_education_occupations->user_occupation_location,
            'income' => $finalincome == 0 ? 'NA' : $finalincome,
            'hometown' => $user_locations->user_city == '' ? 'NA' : $user_locations->user_city,
            'id' => $userid,
            'hieght' => $fitincconverted,
            'color' => $user_physical_details->user_complextion == '' ? 'NA' : $user_physical_details->user_complextion,
            'rasi' => $user_horoscope->user_zodiacs == '' ? 'NA' : $user_horoscope->user_zodiacs,
            'phoneadmin' => $social_media_links->phone_no
        ], [
            'name' => 'John Doe',
            'age' => 30,
            'img' => 'data:image/' . pathinfo($imagePath, PATHINFO_EXTENSION) . ';base64,' . $base64EncodedImage,
            'profile' => 'data:image/' . pathinfo($profilrimgpath, PATHINFO_EXTENSION) . ';base64,' . $profilebase64EncodedImage,
            'lodganesh' => 'data:image/' . pathinfo($profilrimgpath, PATHINFO_EXTENSION) . ';base64,' . $lodganesh64concert,
            'dob' => $formattedDate == '' ? 'NA' : $formattedDate,
            'phone' => '+' . ($userinfo->country_code == '' ? 'NA' : $userinfo->country_code) . ' ' . ($maskedPhoneNumber == 'XXXXXXX' ? 'XXXXXXXNA' : $maskedPhoneNumber),
            'whats' => '+' . ($userinfo->whats_app_c_code == '' ? 'NA' : $userinfo->whats_app_c_code) . ' ' . ($maskedwhatsapNumber == 'XXXXXXX' ? 'XXXXXXXNA' : $maskedwhatsapNumber),
            'userhighesteducation' => $user_education_occupations->user_highest_education == '' ? 'NA' : $user_education_occupations->user_highest_education,
            'occupation' =>  $user_education_occupations->user_occupation == '' ? 'NA' : $user_education_occupations->user_occupation,
            'deg' => $user_education_occupations->user_deg == '' ? 'NA' : $user_education_occupations->user_deg,
            'joblocation' => $user_education_occupations->user_occupation_location == '' ? 'NA' : $user_education_occupations->user_occupation_location,
            'income' => $finalincome == 0 ? 'NA' : $finalincome,
            'hometown' => $user_locations->user_city == '' ? 'NA' : $user_locations->user_city,
            'id' => $userid,
            'hieght' => $fitincconverted,
            'color' => $user_physical_details->user_complextion == '' ? 'NA' : $user_physical_details->user_complextion,
            'rasi' => $user_horoscope->user_zodiacs == '' ? 'NA' : $user_horoscope->user_zodiacs,
            'phoneadmin' => $social_media_links->phone_no
        ]];

        // Render the Blade view to HTML with dynamic data
        $html = view('pdf.demo', $dynamicData)->render();

        // Create an instance of the Dompdf class
        $dompdf = app(Dompdf::class);

        // Load HTML content
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        $filedname = $userid . date('d-M-Y') . '.pdf';

        // Output the generated PDF (inline or attachment)
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filedname . '"');
    }
}
