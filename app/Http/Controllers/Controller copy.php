<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{ 

  


    // //
    // function getfile($extension)

    // {

       

    //     try{

    //         $mimetype = Storage::disk('local')->mimeType($extension);

    //         //dd($mimetype);

    //           //echo $extension;die;

    //         if (in_array($mimetype, ['image/jpeg', 'image/jpeg', 'image/png', 'image/gif'])) {

    //             $img = Image::make(Storage::disk('local')->get($extension));

    //             return $img->response($extension);

    //         } else if ($mimetype == 'application/pdf') {

    //             return Response::make(Storage::disk('local')->get($extension), 200)->header('Content-Type', $mimetype);

    //         } else if ($mimetype == 'video/mp4') {

    //             return Response::make(Storage::disk('local')->get($extension), 200)->header('Content-Type', "video/mp4");

    //         } else if ($mimetype == 'audio/mpeg' || $mimetype == 'audio/wav') {

    //             return Response::make(Storage::disk('local')->get($extension), 200)->header('Content-Type', $mimetype);

    //         } else if ($mimetype == 'application/vnd.ms-excel' || $mimetype == 'text/csv' || $mimetype == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {

    //             return Response::make(Storage::disk('local')->get($extension), 200)->header('Content-Type', $mimetype);

    //         } else if ($mimetype == 'application/zip') {

    //             return Response::make(Storage::disk('local')->get($extension), 200)->header('Content-Type', $mimetype);

    //         } else if ($mimetype == 'application/xml' ||  $mimetype == 'text/xml' ||  $mimetype == 'text/plain' || $mimetype == 'application/atom+xml' || $mimetype == 'application/xhtml+xml') {

    //             return Response::make(Storage::disk('local')->get($extension), 200)->header('Content-Type', $mimetype);

    //         } else {

    //             return 'Invalid File type';

    //         }

    //     } catch (\Exception $e){

    //         return abort(405);

    //     }

    // }
}
