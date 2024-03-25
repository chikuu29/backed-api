<?php

use Illuminate\Support\Facades\Route;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['middleware' => ['App\Http\Middleware\JwtMiddleware']], function ($router) {

    $router->post("/fetch", "dynamic_Crud_controller@fetch");
    $router->post("/save", "dynamic_Crud_controller@save");
    $router->delete("/delete", "dynamic_Crud_controller@delete");
    $router->post("/update", "dynamic_Crud_controller@update");
    $router->post("/getDataFormQuery", "dynaController@dynaQuay");
    $router->post("/addUserDataFirstApi", "userController@addUserDataFirstApi");
    $router->post("/addUserDataSecondApi", "userController@addUserDataSecondApi");
    $router->post("/socialMediaLink", "addItemControler@socialMediaLink");
    $router->post("/getsocialMediaLink", "addItemControler@getsocialMediaLink");
    $router->post("/country", "addItemControler@country");
    $router->post("/state", "addItemControler@state");
    $router->post("/makeActinForMultipulData", "dynamic_Crud_controller@makeActinForMultipulData");
    $router->post("/insertData", "dynamic_Crud_controller@insertData");
    $router->post("/getprofile", "userController@fatchAllaDataByUserId");
    $router->post("/zodiacs", "addItemControler@zodiacs");
    $router->post("/nakshatra", "addItemControler@nakshatra");
    $router->post("/upload", "userController@uploadImage");
    $router->post("/annual_income", "addItemControler@annual_income");
    $router->post("/memberpaln", "memberController@memberpaln");
    $router->post("/getAllData", "memberController@getAllData");
    $router->post("/city", "addItemControler@city");
    $router->post("/privacypolicy", "addItemControler@privacypolicy");
    $router->post("/contactus", "addItemControler@contactus");
    $router->post("/termandcondition", "addItemControler@termandcondition");
    $router->post("/userActivation", "userController@userActivation");
    $router->post("/profileValidation", "userController@profileValidation");
    $router->post("/matches", "filterController@matches");
    $router->post("/matchPersent", "filterController@matchPersent");
    $router->post("/getplandata", "filterController@getplandata");
    $router->post("/getUserPlan", "memberController@getMembersheepPlan");
    $router->post("/callCalculation", "planCalculationController@callCalculation");
    $router->post("/sendMessageCalculation", "planCalculationController@sendMessageCalculation");
    $router->post("/horscopeCalculation", "planCalculationController@horscopeCalculation");
    $router->post("/contactViewOtherCalculation", "planCalculationController@contactViewOtherCalculation");
    $router->post("/contactViewCalculation", "planCalculationController@contactViewCalculation");
    $router->post("/logoUplode", "uplodeController@logoUplode");
    $router->post("/homeLogoUplode", "uplodeController@homeLogoUplode");
    $router->post("/bannerUplode", "uplodeController@bannerUplode");
    $router->post("/cast_matches", "filterController@matchByCast");
    //$router->post("/recommend_matches", "filterController@matches");
    $router->post("/premium_matches", "filterController@premimusMatches");
    $router->post("/getLoginCount", "activityController@getLoginCount");
    $router->post("/getLikeCount", "activityController@getLikeCount");
    $router->post("/secondPass", "forgetPasswordController@secondPass");
    $router->post("/firstPass", "forgetPasswordController@firstPass");
    $router->post("/passwordresetbyadmin", "forgetPasswordController@passwordresetbyadmin");
    $router->post("/updateEditedPlanDetails", "memberController@updateEditedPlanDetails");
    $router->post("/getUserBlockList", "activityController@getUserBlockList");
    $router->post("/waterMark", "uplodeController@waterMark");
    $router->post("/barCode", "uplodeController@barCode");
    $router->post("/profileView", "planCalculationController@profileView");
    $router->post("/sendEmail", "mailcontroller@sendEmail");
    $router->post("/getAllDataById", "userController@getAllDataById");
    $router->post("/filterData", "filterController@filterData");
    $router->post("/sendEmail", "mailcontroller@sendEmail");
    $router->post("/sendData", "mailcontroller@sendData");
    $router->post("/getAllDataById", "userController@getAllDataById");
    $router->post("/successStory", "successStoryConlroller@successStory");
    $router->post("/getAllCount", "AppController@getAllCount");
    $router->post("/checkpass", "forgetPasswordController@checkpass");
    $router->post("/reset_password", "AuthController@reset_password");
    $router->post("/idProofUplode", "uplodeController@idProofUplode");
    $router->post("/horoscopeUplode", "uplodeController@horoscopeUplode");
    $router->post("/paymentSlipUplode", "uplodeController@paymentSlipUplode");
    $router->post("/recommend_matches", "filterController@matchesforindivisual");
    $router->post("/RecentlyJoinedMatches", "filterController@RecentlyJoinedMatches");
    $router->post("/getOnlinedata", "filterController@getOnlinedata");
    $router->post("/Perfect_Match", "filterController@perfactMatch");
    $router->post("/spotlight", "filterController@getSpotlightdata");
});

$router->get("/setting", "AppController@settings");
$router->post("/adminLogin", "AuthController@adminLogin");
$router->post("/auth", "AuthController@userLogin");
$router->post("/generate_reset_password", "AuthController@generateResetLink");
$router->get("/reset_password_token_validator", "AuthController@validate_token");
$router->post("/addUserDataFirstApi", "userController@addUserDataFirstApi");
$router->post("/addUserDataSecondApi", "userController@addUserDataSecondApi");
$router->post("/getAllData", "memberController@getAllData");
$router->post("/aboutus", "addItemControler@aboutus");
$router->post("/unsecuredFatchquary", "dynamic_Crud_controller@unsecuredFatchquary");
$router->post("/feedback", "AppController@feedback");

$router->post("/byCastpremimusMatches", "filterController@byCastpremimusMatches");
$router->post("/byCastgetOnlinedata", "filterController@byCastgetOnlinedata");
$router->post("/byCastgetSpotlightdata", "filterController@byCastgetSpotlightdata");
$router->post("/byCastRecentlyJoinedMatches", "filterController@byCastRecentlyJoinedMatches");
$router->post("/byCastmatchesforindivisual", "filterController@byCastmatchesforindivisual");


$router->post("/byOtherCastpremimusMatches", "filterController@byOtherCastpremimusMatches");
$router->post("/byOtherCastgetOnlinedata", "filterController@byOtherCastgetOnlinedata");
$router->post("/byOtherCastgetSpotlightdata", "filterController@byOtherCastgetSpotlightdata");
$router->post("/byOtherCastRecentlyJoinedMatches", "filterController@byOtherCastRecentlyJoinedMatches");
$router->post("/byOtherCastmatchesforindivisual", "filterController@byOtherCastmatchesforindivisual");

















