<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class filterController extends Controller
{
    public function matches()
    {
        $data = json_decode(file_get_contents("php://input"));
        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {

            // try {
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            if (count($user_activities) > 0) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $outputString = implode(",", $quotedElements);
            } else {
                $outputString = '""';
            }
            $alldata = DB::select("SELECT * FROM user_info
            LEFT JOIN user_religion ON user_info.user_id = user_religion.user_ID
            LEFT JOIN user_locations ON user_info.user_id = user_locations.user_ID
            LEFT JOIN user_family ON user_info.user_id = user_family.user_ID
            LEFT JOIN user_physical_details ON user_info.user_id = user_physical_details.user_ID
            LEFT JOIN user_about ON user_info.user_id = user_about.user_ID
            LEFT JOIN user_diet_hobbies ON user_info.user_id = user_diet_hobbies.user_ID
            LEFT JOIN user_education_occupations ON user_info.user_id = user_education_occupations.user_ID
            LEFT JOIN auth_user ON user_info.user_id = auth_user.auth_ID
            LEFT JOIN user_horoscope ON  user_info.user_id = user_horoscope.user_id
            WHERE user_info.user_gender = '$gender'  AND user_info.user_id <> '$user_id' AND  user_info.user_status = 'Approved'   AND  user_info.user_id NOT IN ($outputString)  AND user_info.deleted = 1 AND user_info.status = 1 AND user_info.marriage_status = 0 ;");


            if (count($alldata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $alldata,
                    "message" => count($alldata) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            }
            // } catch (Exception $e) {
            //     $user_arr = array(
            //         "status" => false,
            //         "success" => false,
            //         "message" => "No Match Found",
            //     );
            // }

        }
        return json_encode($user_arr);
    }

    public function matchesforindivisual()
    {
        $data = json_decode(file_get_contents("php://input"));

        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {

            // try {
            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            //dd($user_partnerpreference);


            // dd($user_partnerpreference->user_employed_In);
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');



            $fatchdata = DB::table('user_info')
                ->select('*')
                ->join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
                ->join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
                ->join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
                ->join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
                ->join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
                ->join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
                ->join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
                ->join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
                ->join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id');

            if ($user_activities != '') {
                if (count($user_activities) > 0) {
                    $user_block_list = $user_activities[0]->user_block_list;
                    $elements = explode(',', $user_block_list);
                    $fatchdata = $fatchdata->whereNotIn('user_info.user_id', $elements);
                } else {
                    $outputString = '""';
                }
            }

            if ($user_partnerpreference->user_marital_status != '') {
                $fatchdata = $fatchdata->whereIn('user_info.user_marital_status', $user_partnerpreference->user_marital_status);
            } else {
                $user_marital_status = '""';
            }

            if ($user_partnerpreference->user_religion != '') {
                $user_religion = implode(',', $user_partnerpreference->user_religion);

                $fatchdata = $fatchdata->whereIn('user_religion.user_religion', $user_partnerpreference->user_religion);
            } else {
                $user_religion = '""';
            }

            if ($user_partnerpreference->user_employed_In != '') {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_employed_In', $user_partnerpreference->user_employed_In);
            } else {
                $user_employed_In = '""';
            }

            if ($user_partnerpreference->user_occupation != '') {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_occupation', $user_partnerpreference->user_occupation);
                $user_occupation = implode(',', $user_partnerpreference->user_occupation);
            } else {
                $user_occupation = '""';
            }

            if ($user_partnerpreference->user_mother_toungh != '') {
                $fatchdata = $fatchdata->whereIn('user_info.user_mother_toungh', $user_partnerpreference->user_mother_toungh);
                $user_mother_toungh = implode(',', $user_partnerpreference->user_mother_toungh);
            } else {
                $user_mother_toungh = '""';
            }



            if ($user_partnerpreference->user_country != '') {
                $fatchdata = $fatchdata->whereIn('user_locations.user_country', $user_partnerpreference->user_country);
                $user_country = implode(',', $user_partnerpreference->user_country);
            } else {
                $user_country = '""';
            }

            if ($user_partnerpreference->user_city != '') {
                $fatchdata = $fatchdata->whereIn('user_locations.user_city', $user_partnerpreference->user_city);
                $user_city = implode(',', $user_partnerpreference->user_city);
            } else {
                $user_city = '""';
            }

            if ($user_partnerpreference->user_state != '') {
                $fatchdata = $fatchdata->whereIn('user_locations.user_state', $user_partnerpreference->user_state);
                $user_state = implode(',', $user_partnerpreference->user_state);
            } else {
                $user_state = '""';
            }

            if ($user_partnerpreference->user_zodiacs != '') {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_zodiacs', $user_partnerpreference->user_zodiacs);
                $zodiacs = implode(',', $user_partnerpreference->user_zodiacs);
            } else {
                $zodiacs = '""';
            }

            if ($user_partnerpreference->user_nakshatra != '') {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_nakhyatra', $user_partnerpreference->user_nakshatra);
                $nakshatra = implode(',', $user_partnerpreference->user_nakshatra);
            } else {
                $nakshatra = '""';
            }

            if ($user_partnerpreference->user_gotra != '') {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_gotra', $user_partnerpreference->user_gotra);
                $gotra = implode(',', $user_partnerpreference->user_gotra);
            } else {
                $gotra = '""';
            }

            if ($user_partnerpreference->user_cast != '') {
                $fatchdata = $fatchdata->whereIn('user_religion.user_caste', $user_partnerpreference->user_cast);
                $user_cast = implode(',', $user_partnerpreference->user_cast);
            } else {
                $user_cast = '""';
            }
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            if ($user_min_height  != '' && $user_max_height != '') {
                $fatchdata = $fatchdata->whereBetween('user_physical_details.user_height', [$user_min_height, $user_max_height]);
            }
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
            if ($user_max_anual_income  != '' && $user_min_anual_income != '') {
                $fatchdata = $fatchdata->whereBetween('user_education_occupations' . 'user_anual_income', [$user_min_anual_income, $user_max_anual_income]);
            }
            $to_age = $user_partnerpreference->to_user_age;
            $from_age =  $user_partnerpreference->from_user_age;
            if ($to_age != '' && $from_age != '') {
                $fatchdata = $fatchdata->whereBetween('user_info.user_age', [$from_age, $to_age]);
            }
            $fatchdata = $fatchdata
                ->where('user_info.user_gender', $gender)
                ->where('user_info.user_id', '!=', $user_id)
                ->where('user_info.user_status', 'Approved')
                ->where('user_info.deleted', 1)
                ->where('user_info.status', 1)
                ->where('user_info.marriage_status', 0)
                ->get();
            //dd($fatchdata);

            $shuffledData = $fatchdata->shuffle();
            //shuffle($shuffledData);

            if (count($shuffledData) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $shuffledData,
                    "message" => count($shuffledData) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            }
            // } catch (Exception $e) {
            //     $user_arr = array(
            //         "status" => false,
            //         "success" => false,
            //         "message" => "No Match Found",
            //     );
            // }

        }
        return json_encode($user_arr);
    }
    public function matchPersent()
    {
        $data = json_decode(file_get_contents("php://input"));
        $loginuserid = isset($data->loginuserid) ? $data->loginuserid : '';
        $preferenceuserid = isset($data->preferenceuserid) ? $data->preferenceuserid : '';

        try {
            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $loginuserid)->get();

            $other_user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $preferenceuserid)->get();

            $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            $other_user_partnerpreference = json_decode($other_user_partnerpreference[0]->json_data);

            // print_r($user_partnerpreference);
            // print_r($other_user_partnerpreference);
            $matchPercentage = $this->calculateMatchPercentage($user_partnerpreference, $other_user_partnerpreference);
            //dd($user_partnerpreference);
            // $user_min_height = $user_partnerpreference->user_min_height;
            // $user_max_height = $user_partnerpreference->user_max_height;
            // //dd($user_height);
            // $user_religion = $user_partnerpreference->user_religion;
            // $user_country = $user_partnerpreference->user_country;
            // $user_marital_status = $user_partnerpreference->user_marital_status;
            // $user_city = $user_partnerpreference->user_city;
            // $user_state = $user_partnerpreference->user_state;
            // $user_employed_In = $user_partnerpreference->user_employed_In;
            // $user_occupation = $user_partnerpreference->user_occupation;
            // $user_mother_toungh = $user_partnerpreference->user_mother_toungh;
            // $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            // $user_max_anual_income = $user_partnerpreference->user_min_anual_income;
            // $income = array($user_max_anual_income, $user_max_anual_income);
            // $hight = array($user_min_height, $user_max_height);

            // $user_physical_details = DB::table('user_physical_details')->where('user_ID', $preferenceuserid)->whereBetween('user_height', $hight)->exists();
            // //dd($user_physical_details);
            // $user_religion = DB::table('user_religion')->where('user_ID', $preferenceuserid)->whereIn('user_caste', $user_religion)->exists();
            // $user_education_occupations = DB::table('user_education_occupations')->where('user_ID', $preferenceuserid)->whereIn('user_employed_In', $user_employed_In)->exists();
            // $user_education_occupations1 = DB::table('user_education_occupations')->where('user_ID', $preferenceuserid)->whereIn('user_occupation', $user_occupation)->exists();
            // $user_info = DB::table('user_info')->where('user_ID', $preferenceuserid)->whereIn('user_mother_toungh', $user_mother_toungh)->exists();
            // $user_education_occupations2 = DB::table('user_education_occupations')->where('user_ID', $preferenceuserid)->whereBetween('user_anual_income', $income)->exists();
            // $datafromstate = DB::table('user_locations')->where('user_ID', $preferenceuserid)->whereIn('user_state', $user_state)->exists();
            // $datafromcity = DB::table('user_locations')->where('user_ID', $preferenceuserid)->whereIn('user_city', $user_city)->exists();
            // $datafromcountry = DB::table('user_locations')->where('user_ID', $preferenceuserid)->whereIn('user_country', $user_country)->exists();
            // $user_marital_present = DB::table('user_info')->where('user_id', $preferenceuserid)->whereIn('user_marital_status', $user_marital_status)->exists();

            // if ($user_physical_details) {
            //     $one = 1;
            // } else {
            //     $one = 0;
            // }
            // if ($user_religion) {
            //     $two = 1;
            // } else {
            //     $two = 0;
            // }
            // if ($user_education_occupations) {
            //     $three = 1;
            // } else {
            //     $three = 0;
            // }
            // if ($user_education_occupations1) {
            //     $foure = 1;
            // } else {
            //     $foure = 0;
            // }
            // if ($user_info) {
            //     $five = 1;
            // } else {
            //     $five = 0;
            // }
            // if ($user_education_occupations2) {
            //     $six = 1;
            // } else {
            //     $six = 0;
            // }

            // if ($datafromstate) {
            //     $seven = 1;
            // } else {
            //     $seven = 0;
            // }
            // if ($datafromcity) {
            //     $eight = 1;
            // } else {
            //     $eight = 0;
            // }
            // if ($datafromcountry) {
            //     $nine = 1;
            // } else {
            //     $nine = 0;
            // }
            // if ($user_marital_present) {
            //     $ten = 1;
            // } else {
            //     $ten = 0;
            // }
            // $persent = ($one + $two + $three + $foure + $five + $six + $seven + $eight + $nine + $ten) / 10 * 100;
            // $user_arr = array(
            //     "status" => true,
            //     "success" => true,
            //     "matches_Count" => round($persent),
            // );
            // echo "Match Percentage: " . $matchPercentage . "%";
            $user_arr = array(
                "status" => true,
                "success" => true,
                "matches_Count" =>  round($matchPercentage),

            );
        } catch (Exception $e) {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "matches_Count" => 0,
                "message" => $e
            );
        }
        return json_encode($user_arr);
    }


    // Function to calculate match percentage
    function calculateMatchPercentage($user1, $user2)
    {
        // Define weights for each category
        $weights = [

            'user_min_height' => 1,
            'user_max_height' => 1,
            'user_religion' => 5,
            'user_country' => 2,
            'user_marital_status' => 4,
            'user_state' => 2,
            'user_city' => 2,
            'user_employed_In' => 2,
            'user_occupation' => 2,
            'user_mother_toungh' => 5,
            'user_min_anual_income' => 5,
            'user_max_anual_income' => 0,
            'user_nakshatra' => 5,
            'user_zodiacs' => 5,
            'user_gotra' => 5

            // Add other categories with appropriate weights
        ];

        // Initialize total score and max possible score
        $totalScore = 0;
        $maxPossibleScore = 0;

        // Calculate scores for each category
        foreach ($weights as $category => $weight) {
            $maxPossibleScore += $weight;
            // Compare user preferences and assign scores
            // if ($user1->$category === $user2->$category) {
            //     $totalScore += $weight;
            // }
            if (is_array($user1->$category) && is_array($user2->$category)) {
                // Check if there are common elements in the arrays
                $commonElements = array_intersect($user1->$category, $user2->$category);

                // If there are common elements, add the weight to the total score
                if (!empty($commonElements)) {
                    $totalScore += $weight;
                }
            } elseif ($user1->$category === $user2->$category) {
                // If not arrays, check if the values are equal
                $totalScore += $weight;
            }
        }

        // Calculate match percentage
        $matchPercentage = ($totalScore / $maxPossibleScore) * 100;

        return $matchPercentage;
    }


    public function getplandata(Request $res)
    {
        $data = json_decode(file_get_contents("php://input"));
        // dd($data);

        if ($data == []) {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => 'Come with verifyde sorse',
                "data" => [],
            );
        } else {
            $loginuserid = $data->loginuserid;
            if ($loginuserid == '' || $loginuserid == null) {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => 'Come with verifyde sorse',
                    "data" => [],
                );
            } else {
                $PLANACTIVEORNOT = DB::table('user_info')->where('user_id', $loginuserid)->where('user_membership_plan_active', 1)->where('deleted', 1)->where('status', 1)->exists();
                if ($PLANACTIVEORNOT) {
                    $getdata = DB::table('user_plan_deatils')->where('user_id', $loginuserid)->get('user_plan_id');
                    $membership_plan_id = $getdata[0]->user_plan_id;
                    $plandeatils = DB::table('membership_plan')->where('membership_plan_id', $membership_plan_id)->get();
                    if (count($getdata) > 0) {
                        $user_arr = array(
                            "status" => true,
                            "success" => true,
                            "message" => 'Done',
                            "data" => $plandeatils,
                        );
                    } else {
                        $user_arr = array(
                            "status" => false,
                            "success" => false,
                            "message" => 'Contact to admin',
                            "data" => [],
                        );
                    }
                } else {
                    $user_arr = array(
                        "status" => false,
                        "success" => false,
                        "message" => 'Contact to admin',
                        "data" => [],
                    );
                }
            }
        }
        return json_encode($user_arr);
    }
    public function matchByCast()
    {
        $data = json_decode(file_get_contents("php://input"));

        $user_id = isset($data->user_id) ? $data->user_id : '';

        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {
            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $user_religiontable = DB::table('user_religion')->where('user_ID', $user_id)->first(['user_caste']);
            $user_religion = $user_religiontable->user_caste;
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            if (count($user_activities) > 0) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                // Enclose each element in double quotes
                $user_marital_status = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $outputString = implode(",", $user_marital_status);
            } else {
                $outputString = '""';
            }
            if (count($user_partnerpreference->user_marital_status) > 0) {
                $elements = $user_partnerpreference->user_marital_status;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_marital_status = implode(",", $quotedElements);
            } else {
                $user_marital_status = '""';
            }
            if (count($user_partnerpreference->user_religion) > 0) {
                $elements = $user_partnerpreference->user_religion;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_religion = implode(",", $quotedElements);
            } else {
                $user_religion = '""';
            }
            if (count($user_partnerpreference->user_employed_In) > 0) {
                $elements = $user_partnerpreference->user_employed_In;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_employed_In = implode(",", $quotedElements);
            } else {
                $user_employed_In = '""';
            }
            if (count($user_partnerpreference->user_occupation) > 0) {
                $elements = $user_partnerpreference->user_occupation;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_occupation = implode(",", $quotedElements);
            } else {
                $user_occupation = '""';
            }
            if (count($user_partnerpreference->user_mother_toungh) > 0) {
                $elements = $user_partnerpreference->user_mother_toungh;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_mother_toungh = implode(",", $quotedElements);
            } else {
                $user_mother_toungh = '""';
            }
            if (count($user_partnerpreference->user_country) > 0) {
                $elements = $user_partnerpreference->user_country;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_country = implode(",", $quotedElements);
            } else {
                $user_country = '""';
            }
            if (count($user_partnerpreference->user_city) > 0) {
                $elements = $user_partnerpreference->user_city;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_city = implode(",", $quotedElements);
            } else {
                $user_city = '""';
            }
            if (count($user_partnerpreference->user_state) > 0) {
                $elements = $user_partnerpreference->user_state;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_state = implode(",", $quotedElements);
            } else {
                $user_state = '""';
            }
            //user_zodiacs
            if (count($user_partnerpreference->user_zodiacs) > 0) {
                $elements = $user_partnerpreference->user_zodiacs;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $zodiacs = implode(",", $quotedElements);
            } else {
                $zodiacs = '""';
            }
            //user_nakshatra
            if (count($user_partnerpreference->user_nakshatra) > 0) {
                $elements = $user_partnerpreference->user_nakshatra;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $nakshatra = implode(",", $quotedElements);
            } else {
                $nakshatra = '""';
            }
            // user_gotra
            if (count($user_partnerpreference->user_gotra) > 0) {
                $elements = $user_partnerpreference->user_gotra;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $gotra = implode(",", $quotedElements);
            } else {
                $gotra = '""';
            }
            // user_employed_In
            if (count($user_partnerpreference->user_employed_In) > 0) {
                $elements = $user_partnerpreference->user_employed_In;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $employed_In = implode(",", $quotedElements);
            } else {
                $employed_In = '""';
            }
            $alldata = DB::select("SELECT * FROM user_info
            LEFT JOIN user_religion ON user_info.user_id = user_religion.user_ID
            LEFT JOIN user_locations ON user_info.user_id = user_locations.user_ID
            LEFT JOIN user_family ON user_info.user_id = user_family.user_ID
            LEFT JOIN user_physical_details ON user_info.user_id = user_physical_details.user_ID
            LEFT JOIN user_about ON user_info.user_id = user_about.user_ID
            LEFT JOIN user_diet_hobbies ON user_info.user_id = user_diet_hobbies.user_ID
            LEFT JOIN user_education_occupations ON user_info.user_id = user_education_occupations.user_ID
            LEFT JOIN auth_user ON user_info.user_id = auth_user.auth_ID
            LEFT JOIN user_horoscope ON  user_info.user_id = user_horoscope.user_id
            WHERE
            (
             user_religion.user_religion IN ($user_religion)

             OR  user_info.user_marital_status IN ($user_marital_status)
             OR  user_education_occupations.user_employed_In IN  ($user_employed_In)
             OR  user_locations.user_country IN ($user_country)
             OR  user_locations.user_city IN ($user_city)
             OR  user_locations.user_state IN ($user_state)
             OR  user_education_occupations.user_occupation IN ($user_occupation)
             OR  user_info.user_mother_toungh IN ($user_mother_toungh)
             OR  user_horoscope.user_zodiacs IN ($zodiacs)
             OR  user_horoscope.user_nakhyatra IN ($nakshatra)
             OR  user_horoscope.user_gotra IN ($gotra)
             OR  user_education_occupations.user_employed_In  IN ($employed_In)
             OR  user_education_occupations.user_anual_income BETWEEN '$user_min_anual_income' AND '$user_max_anual_income'
             OR  user_physical_details.user_height BETWEEN '$user_min_height' AND '$user_max_height')
            AND
            ( user_info.user_gender = '$gender'  AND user_info.user_status = 'Approved' AND user_info.deleted = 1 AND user_info.status = 1 AND user_info.user_id NOT IN ($outputString)  AND user_info.marriage_status = 0 AND user_religion.user_caste = '$user_religion');");

            if (count($alldata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $alldata,
                    "message" => count($alldata) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "data" => [],
                    "message" => "No Match Found",
                );
            }
        }
        return json_encode($user_arr);
    }
    public function premimusMatches()
    {
        $data = json_decode(file_get_contents("php://input"));

        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {



            // try {

            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            //dd($user_partnerpreference);
            $membership_plan = DB::table('membership_plan')->where('membership_plan_default', 1)->get(['membership_plan_type']);
            $membership_plan_type = $membership_plan[0]->membership_plan_type;
            //dd($membership_plan_type);
            // dd($user_partnerpreference->user_employed_In);
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');



            $fatchdata = DB::table('user_info')
                ->select('*')
                ->join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
                ->join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
                ->join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
                ->join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
                ->join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
                ->join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
                ->join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
                ->join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
                ->join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id');

            if ($user_activities != '') {
                if (count($user_activities) > 0) {
                    $user_block_list = $user_activities[0]->user_block_list;
                    $elements = explode(',', $user_block_list);
                    $fatchdata = $fatchdata->whereNotIn('user_info.user_id', $elements);
                } else {
                    $outputString = '""';
                }
            }

            if ($user_partnerpreference->user_marital_status != '') {
                $fatchdata = $fatchdata->whereIn('user_info.user_marital_status', $user_partnerpreference->user_marital_status);
            } else {
                $user_marital_status = '""';
            }

            if ($user_partnerpreference->user_religion != '') {
                $user_religion = implode(',', $user_partnerpreference->user_religion);

                $fatchdata = $fatchdata->whereIn('user_religion.user_religion', $user_partnerpreference->user_religion);
            } else {
                $user_religion = '""';
            }

            if ($user_partnerpreference->user_employed_In != '') {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_employed_In', $user_partnerpreference->user_employed_In);
            } else {
                $user_employed_In = '""';
            }

            if ($user_partnerpreference->user_occupation != '') {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_occupation', $user_partnerpreference->user_occupation);
                $user_occupation = implode(',', $user_partnerpreference->user_occupation);
            } else {
                $user_occupation = '""';
            }

            if ($user_partnerpreference->user_mother_toungh != '') {
                $fatchdata = $fatchdata->whereIn('user_info.user_mother_toungh', $user_partnerpreference->user_mother_toungh);
                $user_mother_toungh = implode(',', $user_partnerpreference->user_mother_toungh);
            } else {
                $user_mother_toungh = '""';
            }



            if ($user_partnerpreference->user_country != '') {
                $fatchdata = $fatchdata->whereIn('user_locations.user_country', $user_partnerpreference->user_country);
                $user_country = implode(',', $user_partnerpreference->user_country);
            } else {
                $user_country = '""';
            }

            if ($user_partnerpreference->user_city != '') {
                $fatchdata = $fatchdata->whereIn('user_locations.user_city', $user_partnerpreference->user_city);
                $user_city = implode(',', $user_partnerpreference->user_city);
            } else {
                $user_city = '""';
            }

            if ($user_partnerpreference->user_state != '') {
                $fatchdata = $fatchdata->whereIn('user_locations.user_state', $user_partnerpreference->user_state);
                $user_state = implode(',', $user_partnerpreference->user_state);
            } else {
                $user_state = '""';
            }

            if ($user_partnerpreference->user_zodiacs != '') {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_zodiacs', $user_partnerpreference->user_zodiacs);
                $zodiacs = implode(',', $user_partnerpreference->user_zodiacs);
            } else {
                $zodiacs = '""';
            }

            if ($user_partnerpreference->user_nakshatra != '') {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_nakhyatra', $user_partnerpreference->user_nakshatra);
                $nakshatra = implode(',', $user_partnerpreference->user_nakshatra);
            } else {
                $nakshatra = '""';
            }

            if ($user_partnerpreference->user_gotra != '') {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_gotra', $user_partnerpreference->user_gotra);
                $gotra = implode(',', $user_partnerpreference->user_gotra);
            } else {
                $gotra = '""';
            }

            if ($user_partnerpreference->user_cast != '') {
                $fatchdata = $fatchdata->whereIn('user_religion.user_caste', $user_partnerpreference->user_cast);
                $user_cast = implode(',', $user_partnerpreference->user_cast);
            } else {
                $user_cast = '""';
            }
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            if ($user_min_height  != '' && $user_max_height != '') {
                $fatchdata = $fatchdata->whereBetween('user_physical_details.user_height', [$user_min_height, $user_max_height]);
            }
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
            if ($user_max_anual_income  != '' && $user_min_anual_income != '') {
                $fatchdata = $fatchdata->whereBetween('user_education_occupations' . 'user_anual_income', [$user_min_anual_income, $user_max_anual_income]);
            }
            $to_age = $user_partnerpreference->to_user_age;
            $from_age =  $user_partnerpreference->from_user_age;
            if ($to_age != '' && $from_age != '') {
                $fatchdata = $fatchdata->whereBetween('user_info.user_age', [$from_age, $to_age]);
            }
            $fatchdata = $fatchdata
                ->where('user_info.user_gender', $gender)
                ->where('user_info.user_id', '!=', $user_id)
                ->where('user_info.user_status', 'Approved')
                ->where('user_info.deleted', 1)
                ->where('user_info.user_membership_plan_type', '<>', $membership_plan_type)
                ->where('user_info.status', 1)
                ->where('user_info.marriage_status', 0)
                ->get();


            // dd($alldata);
            if (count($fatchdata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $fatchdata,
                    "message" => count($fatchdata) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            }
            // } catch (Exception $e) {
            //     $user_arr = array(
            //         "status" => false,
            //         "success" => false,
            //         "message" => "No Match Found",
            //     );
            // }

        }
        return json_encode($user_arr);
    }
    public function filterData()
    {
        $data = json_decode(file_get_contents("php://input"));
        // return $data;
        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {



            // try {
            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            //dd($user_partnerpreference);
            $membership_plan = DB::table('membership_plan')->where('membership_plan_default', 1)->get(['membership_plan_type']);
            $membership_plan_type = $membership_plan[0]->membership_plan_type;
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
            $user_zodiacs = $user_partnerpreference->user_zodiacs;

            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            if (count($user_activities) > 0) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                // Enclose each element in double quotes
                $user_marital_status = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $outputString = implode(",", $user_marital_status);
            } else {
                $outputString = '""';
            }
            if (count($user_partnerpreference->user_marital_status) > 0) {
                $elements = $user_partnerpreference->user_marital_status;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_marital_status = implode(",", $quotedElements);
            } else {
                $user_marital_status = '""';
            }
            if (count($user_partnerpreference->user_religion) > 0) {
                $elements = $user_partnerpreference->user_religion;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_religion = implode(",", $quotedElements);
            } else {
                $user_religion = '""';
            }
            if (count($user_partnerpreference->user_employed_In) > 0) {
                $elements = $user_partnerpreference->user_employed_In;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_employed_In = implode(",", $quotedElements);
            } else {
                $user_employed_In = '""';
            }
            if (count($user_partnerpreference->user_occupation) > 0) {
                $elements = $user_partnerpreference->user_occupation;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_occupation = implode(",", $quotedElements);
            } else {
                $user_occupation = '""';
            }
            if (count($user_partnerpreference->user_mother_toungh) > 0) {
                $elements = $user_partnerpreference->user_mother_toungh;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_mother_toungh = implode(",", $quotedElements);
            } else {
                $user_mother_toungh = '""';
            }
            if (count($user_partnerpreference->user_country) > 0) {
                $elements = $user_partnerpreference->user_country;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_country = implode(",", $quotedElements);
            } else {
                $user_country = '""';
            }
            if (count($user_partnerpreference->user_city) > 0) {
                $elements = $user_partnerpreference->user_city;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_city = implode(",", $quotedElements);
            } else {
                $user_city = '""';
            }
            if (count($user_partnerpreference->user_state) > 0) {
                $elements = $user_partnerpreference->user_state;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_state = implode(",", $quotedElements);
            } else {
                $user_state = '""';
            }
            //user_zodiacs
            if (count($user_partnerpreference->user_zodiacs) > 0) {
                $elements = $user_partnerpreference->user_zodiacs;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $zodiacs = implode(",", $quotedElements);
            } else {
                $zodiacs = '""';
            }
            //user_nakshatra
            if (count($user_partnerpreference->user_nakshatra) > 0) {
                $elements = $user_partnerpreference->user_nakshatra;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $nakshatra = implode(",", $quotedElements);
            } else {
                $nakshatra = '""';
            }
            // user_gotra
            if (count($user_partnerpreference->user_gotra) > 0) {
                $elements = $user_partnerpreference->user_gotra;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $gotra = implode(",", $quotedElements);
            } else {
                $gotra = '""';
            }
            // user_employed_In
            if (count($user_partnerpreference->user_employed_In) > 0) {
                $elements = $user_partnerpreference->user_employed_In;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $employed_In = implode(",", $quotedElements);
            } else {
                $employed_In = '""';
            }
            // OR user_height BETWEEN '$user_min_height' AND '$user_max_height'

            $alldata = DB::select("SELECT * FROM user_info
            LEFT JOIN user_religion ON user_info.user_id = user_religion.user_ID
            LEFT JOIN user_locations ON user_info.user_id = user_locations.user_ID
            LEFT JOIN user_family ON user_info.user_id = user_family.user_ID
            LEFT JOIN user_physical_details ON user_info.user_id = user_physical_details.user_ID
            LEFT JOIN user_about ON user_info.user_id = user_about.user_ID
            LEFT JOIN user_diet_hobbies ON user_info.user_id = user_diet_hobbies.user_ID
            LEFT JOIN user_education_occupations ON user_info.user_id = user_education_occupations.user_ID
            LEFT JOIN auth_user ON user_info.user_id = auth_user.auth_ID
            LEFT JOIN user_horoscope ON  user_info.user_id = user_horoscope.user_id

            WHERE
            (
             user_religion.user_religion IN ($user_religion)

             OR  user_info.user_marital_status IN ($user_marital_status)
             OR  user_education_occupations.user_employed_In IN  ($user_employed_In)
             OR  user_locations.user_country IN ($user_country)
             OR  user_locations.user_city IN ($user_city)
             OR  user_locations.user_state IN ($user_state)
             OR  user_education_occupations.user_occupation IN ($user_occupation)
             OR  user_info.user_mother_toungh IN ($user_mother_toungh)
             OR  user_horoscope.user_zodiacs IN ($zodiacs)
             OR  user_horoscope.user_nakhyatra IN ($nakshatra)
             OR  user_horoscope.user_gotra IN ($gotra)
             OR  user_education_occupations.user_employed_In  IN ($employed_In)
             OR  user_education_occupations.user_anual_income BETWEEN '$user_min_anual_income' AND '$user_max_anual_income'
             AND user_info.user_id <> '$user_id'
             AND user_info.user_gender = '$gender'
             AND user_info.user_status = 'Approved'
             AND  user_info.user_id NOT IN ($outputString)
             AND user_info.deleted = 1
             AND user_info.status = 1
             AND user_info.marriage_status = 0
              ;
             )");
            // WHERE         AND user_info.status = 1 AND user_info.marriage_status = 0 AND user_info.user_has_complete_profile = 1 ;");

            // dd($alldata);
            if (count($alldata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $alldata,
                    "message" => count($alldata) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            }
            // } catch (Exception $e) {
            //     $user_arr = array(
            //         "status" => false,
            //         "success" => false,
            //         "message" => "No Match Found",
            //     );
            // }

        }
        return json_encode($user_arr);
    }


    public function perfactMatch()
    {
        $data = json_decode(file_get_contents("php://input"));

        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {

            // try {
            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            if (!empty($user_partnerpreference)) {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            } else {


                $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
                //dd($user_partnerpreference);
                $user_min_height = $user_partnerpreference->user_min_height;
                $user_max_height = $user_partnerpreference->user_max_height;
                $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
                $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
                $to_age = $user_partnerpreference->to_user_age;
                $from_age =  $user_partnerpreference->from_user_age;
                $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
                $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
                $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
                if (count($user_activities) > 0) {
                    $user_block_list = $user_activities[0]->user_block_list;
                    $elements = explode(',', $user_block_list);
                    // Enclose each element in double quotes
                    $user_marital_status1 = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $elements);
                    // Join the elements with commas
                    $outputString = implode(",", $user_marital_status1);
                } else {
                    $outputString = '""';
                }
                if (count($user_partnerpreference->user_marital_status) > 0) {
                    $elements = $user_partnerpreference->user_marital_status;
                    // Enclose each element in double quotes
                    $quotedElements = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $elements);
                    // Join the elements with commas
                    $user_marital_status = implode(",", $quotedElements);
                } else {
                    $user_marital_status = '""';
                }
                if (count($user_partnerpreference->user_religion) > 0) {
                    $elements = $user_partnerpreference->user_religion;
                    // Enclose each element in double quotes
                    $quotedElements = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $elements);
                    // Join the elements with commas
                    $user_religion = implode(",", $quotedElements);
                } else {
                    $user_religion = '""';
                }
                if (count($user_partnerpreference->user_employed_In) > 0) {
                    $elements = $user_partnerpreference->user_employed_In;
                    // Enclose each element in double quotes
                    $quotedElements = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $elements);
                    // Join the elements with commas
                    $user_employed_In = implode(",", $quotedElements);
                } else {
                    $user_employed_In = '""';
                }
                if (count($user_partnerpreference->user_occupation) > 0) {
                    $elements = $user_partnerpreference->user_occupation;
                    // Enclose each element in double quotes
                    $quotedElements = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $elements);
                    // Join the elements with commas
                    $user_occupation = implode(",", $quotedElements);
                } else {
                    $user_occupation = '""';
                }
                if (count($user_partnerpreference->user_mother_toungh) > 0) {
                    $elements = $user_partnerpreference->user_mother_toungh;
                    // Enclose each element in double quotes
                    $quotedElements = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $elements);
                    // Join the elements with commas
                    $user_mother_toungh = implode(",", $quotedElements);
                } else {
                    $user_mother_toungh = '""';
                }
                if (count($user_partnerpreference->user_country) > 0) {
                    $elements = $user_partnerpreference->user_country;
                    // Enclose each element in double quotes
                    $quotedElements = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $elements);
                    // Join the elements with commas
                    $user_country = implode(",", $quotedElements);
                } else {
                    $user_country = '""';
                }
                if (count($user_partnerpreference->user_city) > 0) {
                    $elements = $user_partnerpreference->user_city;
                    // Enclose each element in double quotes
                    $quotedElements = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $elements);
                    // Join the elements with commas
                    $user_city = implode(",", $quotedElements);
                } else {
                    $user_city = '""';
                }
                if (count($user_partnerpreference->user_state) > 0) {
                    $elements = $user_partnerpreference->user_state;
                    // Enclose each element in double quotes
                    $quotedElements = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $elements);
                    // Join the elements with commas
                    $user_state = implode(",", $quotedElements);
                } else {
                    $user_state = '""';
                }
                //user_zodiacs
                if (count($user_partnerpreference->user_zodiacs) > 0) {
                    $elements = $user_partnerpreference->user_zodiacs;
                    // Enclose each element in double quotes
                    $quotedElements = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $elements);
                    // Join the elements with commas
                    $zodiacs = implode(",", $quotedElements);
                } else {
                    $zodiacs = '""';
                }
                //user_nakshatra
                if (count($user_partnerpreference->user_nakshatra) > 0) {
                    $elements = $user_partnerpreference->user_nakshatra;
                    // Enclose each element in double quotes
                    $quotedElements = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $elements);
                    // Join the elements with commas
                    $nakshatra = implode(",", $quotedElements);
                } else {
                    $nakshatra = '""';
                }
                // user_gotra
                if (count($user_partnerpreference->user_gotra) > 0) {
                    $elements = $user_partnerpreference->user_gotra;
                    // Enclose each element in double quotes
                    $quotedElements = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $elements);
                    // Join the elements with commas
                    $gotra = implode(",", $quotedElements);
                } else {
                    $gotra = '""';
                }
                // user_employed_In
                if (count($user_partnerpreference->user_cast) > 0) {
                    $elements = $user_partnerpreference->user_cast;
                    // Enclose each element in double quotes
                    $quotedElements = array_map(function ($element) {
                        return '"' . $element . '"';
                    }, $elements);
                    // Join the elements with commas
                    $user_cast = implode(",", $quotedElements);
                } else {
                    $user_cast = '""';
                }
                // OR user_height BETWEEN '$user_min_height' AND '$user_max_height'

                $alldata = DB::select("SELECT * FROM user_info
            INNER JOIN user_religion ON user_info.user_id = user_religion.user_ID
            INNER JOIN user_locations ON user_info.user_id = user_locations.user_ID
            INNER JOIN user_family ON user_info.user_id = user_family.user_ID
            INNER JOIN user_physical_details ON user_info.user_id = user_physical_details.user_ID
            INNER JOIN user_about ON user_info.user_id = user_about.user_ID
            INNER JOIN user_diet_hobbies ON user_info.user_id = user_diet_hobbies.user_ID
            INNER JOIN user_education_occupations ON user_info.user_id = user_education_occupations.user_ID
            INNER JOIN auth_user ON user_info.user_id = auth_user.auth_ID
            INNER JOIN user_horoscope ON  user_info.user_id = user_horoscope.user_id
            WHERE
            (
                  user_religion.user_religion IN ($user_religion)
             AND  user_info.user_marital_status IN ($user_marital_status)
             AND  user_education_occupations.user_employed_In IN  ($user_employed_In)
             AND  user_locations.user_country IN ($user_country)
             AND  user_locations.user_city IN ($user_city)
             AND  user_locations.user_state IN ($user_state)
             AND  user_education_occupations.user_occupation IN ($user_occupation)
             AND  user_info.user_mother_toungh IN ($user_mother_toungh)
             AND  user_horoscope.user_zodiacs IN ($zodiacs)
             AND  user_horoscope.user_nakhyatra IN ($nakshatra)
             AND  user_horoscope.user_gotra IN ($gotra)
             AND  user_religion.user_caste  IN ($user_cast)
             AND  user_education_occupations.user_anual_income BETWEEN '$user_min_anual_income' AND '$user_max_anual_income'
             AND  user_physical_details.user_height BETWEEN '$user_min_height' AND '$user_max_height')
             AND user_info.user_age BETWEEN '$from_age' AND '$to_age'
            AND
            ( user_info.user_gender = '$gender'  AND user_info.user_status = 'Approved' AND user_info.deleted = 1 AND user_info.status = 1 AND user_info.user_id NOT IN ($outputString)  AND user_info.marriage_status = 0 );");
                if (count($alldata) > 0) {
                    $user_arr = array(
                        "status" => true,
                        "success" => true,
                        "data" => $alldata,
                        "message" => count($alldata) . ' records Match'
                    );
                } else {
                    $user_arr = array(
                        "status" => false,
                        "success" => false,
                        "message" => "No Match Found",
                    );
                }
                // } catch (Exception $e) {
                //     $user_arr = array(
                //         "status" => false,
                //         "success" => false,
                //         "message" => "No Match Found",
                //     );
                // }
            }
        }
        return json_encode($user_arr);
    }

    public function getSpotlightdata()
    {
        $data = json_decode(file_get_contents("php://input"));
        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {

            // try {
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            if (count($user_activities) > 0) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $outputString = implode(",", $quotedElements);
            } else {
                $outputString = '""';
            }
            $alldata = DB::select("SELECT * FROM user_info
            LEFT JOIN user_religion ON user_info.user_id = user_religion.user_ID
            LEFT JOIN user_locations ON user_info.user_id = user_locations.user_ID
            LEFT JOIN user_family ON user_info.user_id = user_family.user_ID
            LEFT JOIN user_physical_details ON user_info.user_id = user_physical_details.user_ID
            LEFT JOIN user_about ON user_info.user_id = user_about.user_ID
            LEFT JOIN user_diet_hobbies ON user_info.user_id = user_diet_hobbies.user_ID
            LEFT JOIN user_education_occupations ON user_info.user_id = user_education_occupations.user_ID
            LEFT JOIN auth_user ON user_info.user_id = auth_user.auth_ID
            LEFT JOIN user_horoscope ON  user_info.user_id = user_horoscope.user_id
            WHERE user_info.user_gender = '$gender' AND user_info.user_id <> '$user_id' AND user_info.spotlight = 1   AND  user_info.user_id NOT IN ($outputString) AND user_info.user_status = 'Approved' AND user_info.deleted = 1 ;");


            if (count($alldata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $alldata,
                    "message" => count($alldata) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            }
            // } catch (Exception $e) {
            //     $user_arr = array(
            //         "status" => false,
            //         "success" => false,
            //         "message" => "No Match Found",
            //     );
            // }

        }
        return json_encode($user_arr);
    }
    public function getOnlinedata()
    {
        $data = json_decode(file_get_contents("php://input"));
        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parameters",
            );
        } else {
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            if (count($user_activities) > 0) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $outputString = implode(",", $quotedElements);
            } else {
                $outputString = '""';
            }
            $alldata = DB::select("SELECT * FROM user_info
        LEFT JOIN user_religion ON user_info.user_id = user_religion.user_ID
        LEFT JOIN user_locations ON user_info.user_id = user_locations.user_ID
        LEFT JOIN user_family ON user_info.user_id = user_family.user_ID
        LEFT JOIN user_physical_details ON user_info.user_id = user_physical_details.user_ID
        LEFT JOIN user_about ON user_info.user_id = user_about.user_ID
        LEFT JOIN user_diet_hobbies ON user_info.user_id = user_diet_hobbies.user_ID
        LEFT JOIN user_education_occupations ON user_info.user_id = user_education_occupations.user_ID
        LEFT JOIN auth_user ON user_info.user_id = auth_user.auth_ID
        LEFT JOIN user_horoscope ON  user_info.user_id = user_horoscope.user_id
        WHERE user_info.user_gender = '$gender'
         AND user_info.user_status = 'Approved'
          AND  user_info.user_id NOT IN ($outputString)
          AND user_info.user_id <> '$user_id'
          AND user_info.deleted = 1
          AND user_info.status = 1
          AND user_info.online_status = 1
          AND user_info.marriage_status = 0
          ;");



            // Check if $alldata count is less than 5
            if (count($alldata) < 5) {
                // Merge $alldata1 and $alldata and return output
                $alldata1 = DB::select("SELECT * FROM user_info
            LEFT JOIN user_religion ON user_info.user_id = user_religion.user_ID
            LEFT JOIN user_locations ON user_info.user_id = user_locations.user_ID
            LEFT JOIN user_family ON user_info.user_id = user_family.user_ID
            LEFT JOIN user_physical_details ON user_info.user_id = user_physical_details.user_ID
            LEFT JOIN user_about ON user_info.user_id = user_about.user_ID
            LEFT JOIN user_diet_hobbies ON user_info.user_id = user_diet_hobbies.user_ID
            LEFT JOIN user_education_occupations ON user_info.user_id = user_education_occupations.user_ID
            LEFT JOIN auth_user ON user_info.user_id = auth_user.auth_ID
            LEFT JOIN user_horoscope ON  user_info.user_id = user_horoscope.user_id
            WHERE
                user_info.user_gender = '$gender'
            AND user_info.user_id NOT IN ($outputString)
            AND user_info.user_id <> '$user_id'
            AND user_info.online_status = 0
            AND user_info.user_status = 'Approved'
            AND user_info.deleted = 1
            AND user_info.user_all_table_complited = 1
            ORDER BY RAND() LIMIT 10
            ");
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => array_merge($alldata1, $alldata), // Merging $alldata1 and $alldata
                    "message" => (count($alldata) + count($alldata1)) . ' records Match'
                );
                return json_encode($user_arr);
            }

            $user_arr = array(
                "status" => true,
                "success" => true,
                "data" => $alldata,
                "message" => count($alldata) . ' records Match'
            );
            return json_encode($user_arr);
        }
    }
    //  RecentlyJoined filterData
    public function RecentlyJoined()
    {
        $data = json_decode(file_get_contents("php://input"));
        // return $data;
        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {



            // try {
            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            //dd($user_partnerpreference);
            $membership_plan = DB::table('membership_plan')->where('membership_plan_default', 1)->get(['membership_plan_type']);
            $membership_plan_type = $membership_plan[0]->membership_plan_type;
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
            $user_zodiacs = $user_partnerpreference->user_zodiacs;

            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            if (count($user_activities) > 0) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                // Enclose each element in double quotes
                $user_marital_status = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $outputString = implode(",", $user_marital_status);
            } else {
                $outputString = '""';
            }
            if (count($user_partnerpreference->user_marital_status) > 0) {
                $elements = $user_partnerpreference->user_marital_status;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_marital_status = implode(",", $quotedElements);
            } else {
                $user_marital_status = '""';
            }
            if (count($user_partnerpreference->user_religion) > 0) {
                $elements = $user_partnerpreference->user_religion;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_religion = implode(",", $quotedElements);
            } else {
                $user_religion = '""';
            }
            if (count($user_partnerpreference->user_employed_In) > 0) {
                $elements = $user_partnerpreference->user_employed_In;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_employed_In = implode(",", $quotedElements);
            } else {
                $user_employed_In = '""';
            }
            if (count($user_partnerpreference->user_occupation) > 0) {
                $elements = $user_partnerpreference->user_occupation;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_occupation = implode(",", $quotedElements);
            } else {
                $user_occupation = '""';
            }
            if (count($user_partnerpreference->user_mother_toungh) > 0) {
                $elements = $user_partnerpreference->user_mother_toungh;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_mother_toungh = implode(",", $quotedElements);
            } else {
                $user_mother_toungh = '""';
            }
            if (count($user_partnerpreference->user_country) > 0) {
                $elements = $user_partnerpreference->user_country;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_country = implode(",", $quotedElements);
            } else {
                $user_country = '""';
            }
            if (count($user_partnerpreference->user_city) > 0) {
                $elements = $user_partnerpreference->user_city;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_city = implode(",", $quotedElements);
            } else {
                $user_city = '""';
            }
            if (count($user_partnerpreference->user_state) > 0) {
                $elements = $user_partnerpreference->user_state;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $user_state = implode(",", $quotedElements);
            } else {
                $user_state = '""';
            }
            //user_zodiacs
            if (count($user_partnerpreference->user_zodiacs) > 0) {
                $elements = $user_partnerpreference->user_zodiacs;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $zodiacs = implode(",", $quotedElements);
            } else {
                $zodiacs = '""';
            }
            //user_nakshatra
            if (count($user_partnerpreference->user_nakshatra) > 0) {
                $elements = $user_partnerpreference->user_nakshatra;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $nakshatra = implode(",", $quotedElements);
            } else {
                $nakshatra = '""';
            }
            // user_gotra
            if (count($user_partnerpreference->user_gotra) > 0) {
                $elements = $user_partnerpreference->user_gotra;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $gotra = implode(",", $quotedElements);
            } else {
                $gotra = '""';
            }
            // user_employed_In
            if (count($user_partnerpreference->user_employed_In) > 0) {
                $elements = $user_partnerpreference->user_employed_In;
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $employed_In = implode(",", $quotedElements);
            } else {
                $employed_In = '""';
            }
            // OR user_height BETWEEN '$user_min_height' AND '$user_max_height'
            $user_creation_date = date('Y-m-d', strtotime('-5 days'));
            $alldata = DB::select("SELECT * FROM user_info
            LEFT JOIN user_religion ON user_info.user_id = user_religion.user_ID
            LEFT JOIN user_locations ON user_info.user_id = user_locations.user_ID
            LEFT JOIN user_family ON user_info.user_id = user_family.user_ID
            LEFT JOIN user_physical_details ON user_info.user_id = user_physical_details.user_ID
            LEFT JOIN user_about ON user_info.user_id = user_about.user_ID
            LEFT JOIN user_diet_hobbies ON user_info.user_id = user_diet_hobbies.user_ID
            LEFT JOIN user_education_occupations ON user_info.user_id = user_education_occupations.user_ID
            LEFT JOIN auth_user ON user_info.user_id = auth_user.auth_ID
            LEFT JOIN user_horoscope ON  user_info.user_id = user_horoscope.user_id

            WHERE
            (
             user_religion.user_religion IN ($user_religion)

             OR  user_info.user_marital_status IN ($user_marital_status)
             OR  user_education_occupations.user_employed_In IN  ($user_employed_In)
             OR  user_locations.user_country IN ($user_country)
             OR  user_locations.user_city IN ($user_city)
             OR  user_locations.user_state IN ($user_state)
             OR  user_education_occupations.user_occupation IN ($user_occupation)
             OR  user_info.user_mother_toungh IN ($user_mother_toungh)
             OR  user_horoscope.user_zodiacs IN ($zodiacs)
             OR  user_horoscope.user_nakhyatra IN ($nakshatra)
             OR  user_horoscope.user_gotra IN ($gotra)
             OR  user_education_occupations.user_employed_In  IN ($employed_In)
             OR  user_education_occupations.user_anual_income BETWEEN '$user_min_anual_income' AND '$user_max_anual_income'
             AND user_info.user_creation_date_time >= '$user_creation_date'
             AND user_info.user_id <> '$user_id'
             AND user_info.user_gender = '$gender'
             AND user_info.user_status = 'Approved'
             AND  user_info.user_id NOT IN ($outputString)
             AND user_info.deleted = 1
             AND user_info.status = 1
             AND user_info.marriage_status = 0
              ;
             )");
            // WHERE         AND user_info.status = 1 AND user_info.marriage_status = 0 AND user_info.user_has_complete_profile = 1 ;");

            // dd($alldata);
            if (count($alldata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $alldata,
                    "message" => count($alldata) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            }
        }
        return json_encode($user_arr);
    }
    //  RecentlyJoined matches
    public function RecentlyJoinedMatches()
    {
        $data = json_decode(file_get_contents("php://input"));
        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {

            // try {
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            if (count($user_activities) > 0) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                // Enclose each element in double quotes
                $quotedElements = array_map(function ($element) {
                    return '"' . $element . '"';
                }, $elements);
                // Join the elements with commas
                $outputString = implode(",", $quotedElements);
            } else {
                $outputString = '""';
            }
            $user_creation_date = date('Y-m-d H:i:s', strtotime('-30 days'));

            $alldata = DB::select("SELECT * FROM user_info
            LEFT JOIN user_religion ON user_info.user_id = user_religion.user_ID
            LEFT JOIN user_locations ON user_info.user_id = user_locations.user_ID
            LEFT JOIN user_family ON user_info.user_id = user_family.user_ID
            LEFT JOIN user_physical_details ON user_info.user_id = user_physical_details.user_ID
            LEFT JOIN user_about ON user_info.user_id = user_about.user_ID
            LEFT JOIN user_diet_hobbies ON user_info.user_id = user_diet_hobbies.user_ID
            LEFT JOIN user_education_occupations ON user_info.user_id = user_education_occupations.user_ID
            LEFT JOIN auth_user ON user_info.user_id = auth_user.auth_ID
            LEFT JOIN user_horoscope ON  user_info.user_id = user_horoscope.user_id
            WHERE user_info.user_gender = '$gender'  AND user_info.user_id <> '$user_id' AND  user_info.user_status = 'Approved'   AND  user_info.user_id NOT IN ($outputString)  AND user_info.deleted = 1 AND user_info.status = 1 AND user_info.marriage_status = 0  AND user_info.user_creation_date_time >= '$user_creation_date' ;");


            if (count($alldata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $alldata,
                    "message" => count($alldata) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            }
            // } catch (Exception $e) {
            //     $user_arr = array(
            //         "status" => false,
            //         "success" => false,
            //         "message" => "No Match Found",
            //     );
            // }

        }
        return json_encode($user_arr);
    }









    public function byCastmatchesforindivisual() //done
    {
        $data = json_decode(file_get_contents("php://input"));

        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {

            // try {
            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            //dd($user_partnerpreference);


            // dd($user_partnerpreference->user_employed_In);
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            // dd(count($user_activities));


            $fatchdata = DB::table('user_info')
                ->select('*')
                ->join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
                ->join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
                ->join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
                ->join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
                ->join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
                ->join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
                ->join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
                ->join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
                ->join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id');
            if ($user_activities != '' && count($user_activities) > 0 && $user_activities != null) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                $fatchdata = $fatchdata->whereNotIn('user_info.user_id', $elements);
            }

            // Validate each field in a similar manner
            if ($user_partnerpreference->user_marital_status != '' && count($user_partnerpreference->user_marital_status) > 0 && $user_partnerpreference->user_marital_status != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_marital_status', $user_partnerpreference->user_marital_status);
            }

            if ($user_partnerpreference->user_religion != '' && count($user_partnerpreference->user_religion) > 0 && $user_partnerpreference->user_religion != null) {
                $fatchdata = $fatchdata->whereIn('user_religion.user_religion', $user_partnerpreference->user_religion);
            } else {
                $user_religion = '""';
            }

            if ($user_partnerpreference->user_employed_In != '' && count($user_partnerpreference->user_employed_In) > 0 && $user_partnerpreference->user_employed_In != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_employed_In', $user_partnerpreference->user_employed_In);
            } else {
                $user_employed_In = '""';
            }

            if ($user_partnerpreference->user_occupation != '' && count($user_partnerpreference->user_occupation) > 0 && $user_partnerpreference->user_occupation != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_occupation', $user_partnerpreference->user_occupation);
                $user_occupation = implode(',', $user_partnerpreference->user_occupation);
            } else {
                $user_occupation = '""';
            }

            if ($user_partnerpreference->user_mother_toungh != '' && count($user_partnerpreference->user_mother_toungh) > 0 && $user_partnerpreference->user_mother_toungh != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_mother_toungh', $user_partnerpreference->user_mother_toungh);
                $user_mother_toungh = implode(',', $user_partnerpreference->user_mother_toungh);
            } else {
                $user_mother_toungh = '""';
            }

            if ($user_partnerpreference->user_country != '' && count($user_partnerpreference->user_country) > 0 && $user_partnerpreference->user_country != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_country', $user_partnerpreference->user_country);
                $user_country = implode(',', $user_partnerpreference->user_country);
            } else {
                $user_country = '""';
            }

            if ($user_partnerpreference->user_city != '' && count($user_partnerpreference->user_city) > 0 && $user_partnerpreference->user_city != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_city', $user_partnerpreference->user_city);
                $user_city = implode(',', $user_partnerpreference->user_city);
            } else {
                $user_city = '""';
            }

            if ($user_partnerpreference->user_state != '' && count($user_partnerpreference->user_state) > 0 && $user_partnerpreference->user_state != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_state', $user_partnerpreference->user_state);
                $user_state = implode(',', $user_partnerpreference->user_state);
            } else {
                $user_state = '""';
            }

            if ($user_partnerpreference->user_zodiacs != '' && count($user_partnerpreference->user_zodiacs) > 0 && $user_partnerpreference->user_zodiacs != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_zodiacs', $user_partnerpreference->user_zodiacs);
                $zodiacs = implode(',', $user_partnerpreference->user_zodiacs);
            } else {
                $zodiacs = '""';
            }

            if ($user_partnerpreference->user_nakshatra != '' && count($user_partnerpreference->user_nakshatra) > 0 && $user_partnerpreference->user_nakshatra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_nakhyatra', $user_partnerpreference->user_nakshatra);
                $nakshatra = implode(',', $user_partnerpreference->user_nakshatra);
            } else {
                $nakshatra = '""';
            }

            if ($user_partnerpreference->user_gotra != '' && count($user_partnerpreference->user_gotra) > 0 && $user_partnerpreference->user_gotra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_gotra', $user_partnerpreference->user_gotra);
                $gotra = implode(',', $user_partnerpreference->user_gotra);
            } else {
                $gotra = '""';
            }

            if ($user_partnerpreference->user_cast != '' && count($user_partnerpreference->user_cast) > 0 && $user_partnerpreference->user_cast != null) {
                $fatchdata = $fatchdata->whereIn('user_religion.user_caste', $user_partnerpreference->user_cast);
                $user_cast = implode(',', $user_partnerpreference->user_cast);
            } else {
                $user_cast = '""';
            }
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            if ($user_min_height  != '' && $user_max_height != '') {
                $fatchdata = $fatchdata->whereBetween('user_physical_details.user_height', [$user_min_height, $user_max_height]);
            }
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
            if ($user_max_anual_income  != '' && $user_min_anual_income != '') {
                $fatchdata = $fatchdata->whereBetween('user_education_occupations.user_anual_income', [$user_min_anual_income, $user_max_anual_income]);
            }
            $to_age = $user_partnerpreference->to_user_age;
            $from_age =  $user_partnerpreference->from_user_age;
            if ($to_age != '' && $from_age != '') {
                $fatchdata = $fatchdata->whereBetween('user_info.user_age', [$from_age, $to_age]);
            }
            $fatchdata = $fatchdata
                ->where('user_info.user_gender', $gender)
                ->where('user_info.user_id', '!=', $user_id)
                ->where('user_info.user_status', 'Approved')
                ->where('user_info.deleted', 1)
                ->where('user_info.status', 1)
                ->where('user_info.marriage_status', 0)
                ->get();
            //dd($fatchdata);

            $shuffledData = $fatchdata->shuffle();
            //shuffle($shuffledData);

            if (count($shuffledData) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $shuffledData,
                    "message" => count($shuffledData) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            }
        }
        return json_encode($user_arr);
    }
    public function byCastRecentlyJoinedMatches() //done
    {
        $data = json_decode(file_get_contents("php://input"));
        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {



            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            //dd($user_partnerpreference);


            // dd($user_partnerpreference->user_employed_In);
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            //dd($user_activities);


            $fatchdata = DB::table('user_info')
                ->select('*')
                ->join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
                ->join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
                ->join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
                ->join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
                ->join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
                ->join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
                ->join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
                ->join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
                ->join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id');

            if ($user_activities != '' && count($user_activities) > 0 && $user_activities != null) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                $fatchdata = $fatchdata->whereNotIn('user_info.user_id', $elements);
            }

            // Validate each field in a similar manner
            if ($user_partnerpreference->user_marital_status != '' && count($user_partnerpreference->user_marital_status) > 0 && $user_partnerpreference->user_marital_status != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_marital_status', $user_partnerpreference->user_marital_status);
            }

            if ($user_partnerpreference->user_religion != '' && count($user_partnerpreference->user_religion) > 0 && $user_partnerpreference->user_religion != null) {
                $fatchdata = $fatchdata->whereIn('user_religion.user_religion', $user_partnerpreference->user_religion);
            } else {
                $user_religion = '""';
            }

            if ($user_partnerpreference->user_employed_In != '' && count($user_partnerpreference->user_employed_In) > 0 && $user_partnerpreference->user_employed_In != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_employed_In', $user_partnerpreference->user_employed_In);
            } else {
                $user_employed_In = '""';
            }

            if ($user_partnerpreference->user_occupation != '' && count($user_partnerpreference->user_occupation) > 0 && $user_partnerpreference->user_occupation != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_occupation', $user_partnerpreference->user_occupation);
                $user_occupation = implode(',', $user_partnerpreference->user_occupation);
            } else {
                $user_occupation = '""';
            }

            if ($user_partnerpreference->user_mother_toungh != '' && count($user_partnerpreference->user_mother_toungh) > 0 && $user_partnerpreference->user_mother_toungh != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_mother_toungh', $user_partnerpreference->user_mother_toungh);
                $user_mother_toungh = implode(',', $user_partnerpreference->user_mother_toungh);
            } else {
                $user_mother_toungh = '""';
            }

            if ($user_partnerpreference->user_country != '' && count($user_partnerpreference->user_country) > 0 && $user_partnerpreference->user_country != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_country', $user_partnerpreference->user_country);
                $user_country = implode(',', $user_partnerpreference->user_country);
            } else {
                $user_country = '""';
            }

            if ($user_partnerpreference->user_city != '' && count($user_partnerpreference->user_city) > 0 && $user_partnerpreference->user_city != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_city', $user_partnerpreference->user_city);
                $user_city = implode(',', $user_partnerpreference->user_city);
            } else {
                $user_city = '""';
            }

            if ($user_partnerpreference->user_state != '' && count($user_partnerpreference->user_state) > 0 && $user_partnerpreference->user_state != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_state', $user_partnerpreference->user_state);
                $user_state = implode(',', $user_partnerpreference->user_state);
            } else {
                $user_state = '""';
            }

            if ($user_partnerpreference->user_zodiacs != '' && count($user_partnerpreference->user_zodiacs) > 0 && $user_partnerpreference->user_zodiacs != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_zodiacs', $user_partnerpreference->user_zodiacs);
                $zodiacs = implode(',', $user_partnerpreference->user_zodiacs);
            } else {
                $zodiacs = '""';
            }

            if ($user_partnerpreference->user_nakshatra != '' && count($user_partnerpreference->user_nakshatra) > 0 && $user_partnerpreference->user_nakshatra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_nakhyatra', $user_partnerpreference->user_nakshatra);
                $nakshatra = implode(',', $user_partnerpreference->user_nakshatra);
            } else {
                $nakshatra = '""';
            }

            if ($user_partnerpreference->user_gotra != '' && count($user_partnerpreference->user_gotra) > 0 && $user_partnerpreference->user_gotra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_gotra', $user_partnerpreference->user_gotra);
                $gotra = implode(',', $user_partnerpreference->user_gotra);
            } else {
                $gotra = '""';
            }

            if ($user_partnerpreference->user_cast != '' && count($user_partnerpreference->user_cast) > 0 && $user_partnerpreference->user_cast != null) {
                $fatchdata = $fatchdata->whereIn('user_religion.user_caste', $user_partnerpreference->user_cast);
                $user_cast = implode(',', $user_partnerpreference->user_cast);
            } else {
                $user_cast = '""';
            }
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            if ($user_min_height  != '' && $user_max_height != '') {
                $fatchdata = $fatchdata->whereBetween('user_physical_details.user_height', [$user_min_height, $user_max_height]);
            }
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
            if ($user_max_anual_income  != '' && $user_min_anual_income != '') {
                $fatchdata = $fatchdata->whereBetween('user_education_occupations.user_anual_income', [$user_min_anual_income, $user_max_anual_income]);
            }
            $to_age = $user_partnerpreference->to_user_age;
            $from_age =  $user_partnerpreference->from_user_age;
            if ($to_age != '' && $from_age != '') {
                $fatchdata = $fatchdata->whereBetween('user_info.user_age', [$from_age, $to_age]);
            }
            $user_creation_date = date('Y-m-d H:i:s', strtotime('-30 days'));
            $fatchdata = $fatchdata
                ->where('user_info.user_gender', $gender)
                ->where('user_info.user_id', '!=', $user_id)
                ->where('user_info.user_status', 'Approved')
                ->where('user_info.deleted', 1)
                ->where('user_info.status', 1)
                ->where('user_info.marriage_status', 0)
                ->where('user_info.user_creation_date_time', '>=', $user_creation_date)
                ->get();
            if (count($fatchdata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $fatchdata,
                    "message" => count($fatchdata) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            }
        }
        return json_encode($user_arr);
    }
    public function byCastgetSpotlightdata() //done
    {
        $data = json_decode(file_get_contents("php://input"));
        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {


            // try {
            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            //dd($user_partnerpreference);


            // dd($user_partnerpreference->user_employed_In);
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            //dd($user_activities);


            $fatchdata = DB::table('user_info')
                ->select('*')
                ->join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
                ->join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
                ->join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
                ->join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
                ->join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
                ->join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
                ->join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
                ->join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
                ->join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id');

            if ($user_activities != '' && count($user_activities) > 0 && $user_activities != null) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                $fatchdata = $fatchdata->whereNotIn('user_info.user_id', $elements);
            }

            // Validate each field in a similar manner
            if ($user_partnerpreference->user_marital_status != '' && count($user_partnerpreference->user_marital_status) > 0 && $user_partnerpreference->user_marital_status != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_marital_status', $user_partnerpreference->user_marital_status);
            }

            if ($user_partnerpreference->user_religion != '' && count($user_partnerpreference->user_religion) > 0 && $user_partnerpreference->user_religion != null) {
                $fatchdata = $fatchdata->whereIn('user_religion.user_religion', $user_partnerpreference->user_religion);
            } else {
                $user_religion = '""';
            }

            if ($user_partnerpreference->user_employed_In != '' && count($user_partnerpreference->user_employed_In) > 0 && $user_partnerpreference->user_employed_In != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_employed_In', $user_partnerpreference->user_employed_In);
            } else {
                $user_employed_In = '""';
            }

            if ($user_partnerpreference->user_occupation != '' && count($user_partnerpreference->user_occupation) > 0 && $user_partnerpreference->user_occupation != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_occupation', $user_partnerpreference->user_occupation);
                $user_occupation = implode(',', $user_partnerpreference->user_occupation);
            } else {
                $user_occupation = '""';
            }

            if ($user_partnerpreference->user_mother_toungh != '' && count($user_partnerpreference->user_mother_toungh) > 0 && $user_partnerpreference->user_mother_toungh != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_mother_toungh', $user_partnerpreference->user_mother_toungh);
                $user_mother_toungh = implode(',', $user_partnerpreference->user_mother_toungh);
            } else {
                $user_mother_toungh = '""';
            }

            if ($user_partnerpreference->user_country != '' && count($user_partnerpreference->user_country) > 0 && $user_partnerpreference->user_country != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_country', $user_partnerpreference->user_country);
                $user_country = implode(',', $user_partnerpreference->user_country);
            } else {
                $user_country = '""';
            }

            if ($user_partnerpreference->user_city != '' && count($user_partnerpreference->user_city) > 0 && $user_partnerpreference->user_city != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_city', $user_partnerpreference->user_city);
                $user_city = implode(',', $user_partnerpreference->user_city);
            } else {
                $user_city = '""';
            }

            if ($user_partnerpreference->user_state != '' && count($user_partnerpreference->user_state) > 0 && $user_partnerpreference->user_state != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_state', $user_partnerpreference->user_state);
                $user_state = implode(',', $user_partnerpreference->user_state);
            } else {
                $user_state = '""';
            }

            if ($user_partnerpreference->user_zodiacs != '' && count($user_partnerpreference->user_zodiacs) > 0 && $user_partnerpreference->user_zodiacs != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_zodiacs', $user_partnerpreference->user_zodiacs);
                $zodiacs = implode(',', $user_partnerpreference->user_zodiacs);
            } else {
                $zodiacs = '""';
            }

            if ($user_partnerpreference->user_nakshatra != '' && count($user_partnerpreference->user_nakshatra) > 0 && $user_partnerpreference->user_nakshatra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_nakhyatra', $user_partnerpreference->user_nakshatra);
                $nakshatra = implode(',', $user_partnerpreference->user_nakshatra);
            } else {
                $nakshatra = '""';
            }

            if ($user_partnerpreference->user_gotra != '' && count($user_partnerpreference->user_gotra) > 0 && $user_partnerpreference->user_gotra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_gotra', $user_partnerpreference->user_gotra);
                $gotra = implode(',', $user_partnerpreference->user_gotra);
            } else {
                $gotra = '""';
            }

            if ($user_partnerpreference->user_cast != '' && count($user_partnerpreference->user_cast) > 0 && $user_partnerpreference->user_cast != null) {
                $fatchdata = $fatchdata->whereIn('user_religion.user_caste', $user_partnerpreference->user_cast);
                $user_cast = implode(',', $user_partnerpreference->user_cast);
            } else {
                $user_cast = '""';
            }
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            if ($user_min_height  != '' && $user_max_height != '') {
                $fatchdata = $fatchdata->whereBetween('user_physical_details.user_height', [$user_min_height, $user_max_height]);
            }
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
            if ($user_max_anual_income  != '' && $user_min_anual_income != '') {
                $fatchdata = $fatchdata->whereBetween('user_education_occupations.user_anual_income', [$user_min_anual_income, $user_max_anual_income]);
            }
            $to_age = $user_partnerpreference->to_user_age;
            $from_age =  $user_partnerpreference->from_user_age;
            if ($to_age != '' && $from_age != '') {
                $fatchdata = $fatchdata->whereBetween('user_info.user_age', [$from_age, $to_age]);
            }
            $fatchdata = $fatchdata
                ->where('user_info.user_gender', $gender)
                ->where('user_info.user_id', '!=', $user_id)
                ->where('user_info.user_status', 'Approved')
                ->where('user_info.deleted', 1)
                ->where('user_info.status', 1)
                ->where('user_info.marriage_status', 0)
                ->where('user_info.spotlight', 1)
                ->get();
            if (count($fatchdata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $fatchdata,
                    "message" => count($fatchdata) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            }
        }
        return json_encode($user_arr);
    }
    public function byCastgetOnlinedata() //done
    {
        $data = json_decode(file_get_contents("php://input"));
        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parameters",
            );
        } else {

            // try {
            $user_partnerpreference1 = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            //return $user_partnerpreference;

            $user_partnerpreference = json_decode($user_partnerpreference1[0]->json_data);


            // dd($user_partnerpreference->user_employed_In);
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            //dd($user_activities);


            $fatchdata = DB::table('user_info')
                ->select('*')
                ->join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
                ->join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
                ->join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
                ->join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
                ->join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
                ->join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
                ->join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
                ->join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
                ->join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id');

            if ($user_activities != '' && count($user_activities) > 0 && $user_activities != null) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                $fatchdata = $fatchdata->whereNotIn('user_info.user_id', $elements);
            }

            // Validate each field in a similar manner
            if ($user_partnerpreference->user_marital_status != '' && count($user_partnerpreference->user_marital_status) > 0 && $user_partnerpreference->user_marital_status != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_marital_status', $user_partnerpreference->user_marital_status);
            }

            if ($user_partnerpreference->user_religion != '' && count($user_partnerpreference->user_religion) > 0 && $user_partnerpreference->user_religion != null) {
                $fatchdata = $fatchdata->whereIn('user_religion.user_religion', $user_partnerpreference->user_religion);
            } else {
                $user_religion = '""';
            }

            if ($user_partnerpreference->user_employed_In != '' && count($user_partnerpreference->user_employed_In) > 0 && $user_partnerpreference->user_employed_In != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_employed_In', $user_partnerpreference->user_employed_In);
            } else {
                $user_employed_In = '""';
            }

            if ($user_partnerpreference->user_occupation != '' && count($user_partnerpreference->user_occupation) > 0 && $user_partnerpreference->user_occupation != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_occupation', $user_partnerpreference->user_occupation);
                $user_occupation = implode(',', $user_partnerpreference->user_occupation);
            } else {
                $user_occupation = '""';
            }

            if ($user_partnerpreference->user_mother_toungh != '' && count($user_partnerpreference->user_mother_toungh) > 0 && $user_partnerpreference->user_mother_toungh != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_mother_toungh', $user_partnerpreference->user_mother_toungh);
                $user_mother_toungh = implode(',', $user_partnerpreference->user_mother_toungh);
            } else {
                $user_mother_toungh = '""';
            }

            if ($user_partnerpreference->user_country != '' && count($user_partnerpreference->user_country) > 0 && $user_partnerpreference->user_country != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_country', $user_partnerpreference->user_country);
                $user_country = implode(',', $user_partnerpreference->user_country);
            } else {
                $user_country = '""';
            }

            if ($user_partnerpreference->user_city != '' && count($user_partnerpreference->user_city) > 0 && $user_partnerpreference->user_city != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_city', $user_partnerpreference->user_city);
                $user_city = implode(',', $user_partnerpreference->user_city);
            } else {
                $user_city = '""';
            }

            if ($user_partnerpreference->user_state != '' && count($user_partnerpreference->user_state) > 0 && $user_partnerpreference->user_state != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_state', $user_partnerpreference->user_state);
                $user_state = implode(',', $user_partnerpreference->user_state);
            } else {
                $user_state = '""';
            }

            if ($user_partnerpreference->user_zodiacs != '' && count($user_partnerpreference->user_zodiacs) > 0 && $user_partnerpreference->user_zodiacs != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_zodiacs', $user_partnerpreference->user_zodiacs);
                $zodiacs = implode(',', $user_partnerpreference->user_zodiacs);
            } else {
                $zodiacs = '""';
            }

            if ($user_partnerpreference->user_nakshatra != '' && count($user_partnerpreference->user_nakshatra) > 0 && $user_partnerpreference->user_nakshatra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_nakhyatra', $user_partnerpreference->user_nakshatra);
                $nakshatra = implode(',', $user_partnerpreference->user_nakshatra);
            } else {
                $nakshatra = '""';
            }

            if ($user_partnerpreference->user_gotra != '' && count($user_partnerpreference->user_gotra) > 0 && $user_partnerpreference->user_gotra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_gotra', $user_partnerpreference->user_gotra);
                $gotra = implode(',', $user_partnerpreference->user_gotra);
            } else {
                $gotra = '""';
            }

            if ($user_partnerpreference->user_cast != '' && count($user_partnerpreference->user_cast) > 0 && $user_partnerpreference->user_cast != null) {
                $fatchdata = $fatchdata->whereIn('user_religion.user_caste', $user_partnerpreference->user_cast);
                $user_cast = implode(',', $user_partnerpreference->user_cast);
            } else {
                $user_cast = '""';
            }
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            if ($user_min_height  != '' && $user_max_height != '') {
                $fatchdata = $fatchdata->whereBetween('user_physical_details.user_height', [$user_min_height, $user_max_height]);
            }
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
            if ($user_max_anual_income  != '' && $user_min_anual_income != '') {
                $fatchdata = $fatchdata->whereBetween('user_education_occupations.user_anual_income', [$user_min_anual_income, $user_max_anual_income]);
            }
            $to_age = $user_partnerpreference->to_user_age;
            $from_age =  $user_partnerpreference->from_user_age;
            if ($to_age != '' && $from_age != '') {
                $fatchdata = $fatchdata->whereBetween('user_info.user_age', [$from_age, $to_age]);
            }
            $fatchdata = $fatchdata
                ->where('user_info.user_gender', $gender)
                ->where('user_info.user_id', '!=', $user_id)
                ->where('user_info.user_status', 'Approved')
                ->where('user_info.deleted', 1)
                ->where('user_info.status', 1)
                ->where('user_info.marriage_status', 0)
                ->where('user_info.online_status', 1)
                ->get();
            // dd($fatchdata);
            if (count($fatchdata) < 5) {
                //dd('');
                $fatchdataone = DB::table('user_info')
                    ->select('*')
                    ->join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
                    ->join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
                    ->join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
                    ->join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
                    ->join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
                    ->join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
                    ->join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
                    ->join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
                    ->join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id');

                if ($user_activities != '' && count($user_activities) > 0 && $user_activities != null) {
                    $user_block_list = $user_activities[0]->user_block_list;
                    $elements = explode(',', $user_block_list);
                    $fatchdata = $fatchdata->whereNotIn('user_info.user_id', $elements);
                }

                // Validate each field in a similar manner
                if ($user_partnerpreference->user_marital_status != '' && count($user_partnerpreference->user_marital_status) > 0 && $user_partnerpreference->user_marital_status != null) {
                    $fatchdata = $fatchdata->whereIn('user_info.user_marital_status', $user_partnerpreference->user_marital_status);
                }

                if ($user_partnerpreference->user_religion != '' && count($user_partnerpreference->user_religion) > 0 && $user_partnerpreference->user_religion != null) {
                    $fatchdata = $fatchdata->whereIn('user_religion.user_religion', $user_partnerpreference->user_religion);
                } else {
                    $user_religion = '""';
                }

                if ($user_partnerpreference->user_employed_In != '' && count($user_partnerpreference->user_employed_In) > 0 && $user_partnerpreference->user_employed_In != null) {
                    $fatchdata = $fatchdata->whereIn('user_education_occupations.user_employed_In', $user_partnerpreference->user_employed_In);
                } else {
                    $user_employed_In = '""';
                }

                if ($user_partnerpreference->user_occupation != '' && count($user_partnerpreference->user_occupation) > 0 && $user_partnerpreference->user_occupation != null) {
                    $fatchdata = $fatchdata->whereIn('user_education_occupations.user_occupation', $user_partnerpreference->user_occupation);
                    $user_occupation = implode(',', $user_partnerpreference->user_occupation);
                } else {
                    $user_occupation = '""';
                }

                if ($user_partnerpreference->user_mother_toungh != '' && count($user_partnerpreference->user_mother_toungh) > 0 && $user_partnerpreference->user_mother_toungh != null) {
                    $fatchdata = $fatchdata->whereIn('user_info.user_mother_toungh', $user_partnerpreference->user_mother_toungh);
                    $user_mother_toungh = implode(',', $user_partnerpreference->user_mother_toungh);
                } else {
                    $user_mother_toungh = '""';
                }

                if ($user_partnerpreference->user_country != '' && count($user_partnerpreference->user_country) > 0 && $user_partnerpreference->user_country != null) {
                    $fatchdata = $fatchdata->whereIn('user_locations.user_country', $user_partnerpreference->user_country);
                    $user_country = implode(',', $user_partnerpreference->user_country);
                } else {
                    $user_country = '""';
                }

                if ($user_partnerpreference->user_city != '' && count($user_partnerpreference->user_city) > 0 && $user_partnerpreference->user_city != null) {
                    $fatchdata = $fatchdata->whereIn('user_locations.user_city', $user_partnerpreference->user_city);
                    $user_city = implode(',', $user_partnerpreference->user_city);
                } else {
                    $user_city = '""';
                }

                if ($user_partnerpreference->user_state != '' && count($user_partnerpreference->user_state) > 0 && $user_partnerpreference->user_state != null) {
                    $fatchdata = $fatchdata->whereIn('user_locations.user_state', $user_partnerpreference->user_state);
                    $user_state = implode(',', $user_partnerpreference->user_state);
                } else {
                    $user_state = '""';
                }

                if ($user_partnerpreference->user_zodiacs != '' && count($user_partnerpreference->user_zodiacs) > 0 && $user_partnerpreference->user_zodiacs != null) {
                    $fatchdata = $fatchdata->whereIn('user_horoscope.user_zodiacs', $user_partnerpreference->user_zodiacs);
                    $zodiacs = implode(',', $user_partnerpreference->user_zodiacs);
                } else {
                    $zodiacs = '""';
                }

                if ($user_partnerpreference->user_nakshatra != '' && count($user_partnerpreference->user_nakshatra) > 0 && $user_partnerpreference->user_nakshatra != null) {
                    $fatchdata = $fatchdata->whereIn('user_horoscope.user_nakhyatra', $user_partnerpreference->user_nakshatra);
                    $nakshatra = implode(',', $user_partnerpreference->user_nakshatra);
                } else {
                    $nakshatra = '""';
                }

                if ($user_partnerpreference->user_gotra != '' && count($user_partnerpreference->user_gotra) > 0 && $user_partnerpreference->user_gotra != null) {
                    $fatchdata = $fatchdata->whereIn('user_horoscope.user_gotra', $user_partnerpreference->user_gotra);
                    $gotra = implode(',', $user_partnerpreference->user_gotra);
                } else {
                    $gotra = '""';
                }

                if ($user_partnerpreference->user_cast != '' && count($user_partnerpreference->user_cast) > 0 && $user_partnerpreference->user_cast != null) {
                    $fatchdata = $fatchdata->whereIn('user_religion.user_caste', $user_partnerpreference->user_cast);
                    $user_cast = implode(',', $user_partnerpreference->user_cast);
                } else {
                    $user_cast = '""';
                }
                $user_min_height = $user_partnerpreference->user_min_height;
                $user_max_height = $user_partnerpreference->user_max_height;
                if ($user_min_height  != '' && $user_max_height != '') {
                    $fatchdataone = $fatchdataone->whereBetween('user_physical_details.user_height', [$user_min_height, $user_max_height]);
                }
                $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
                $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
                if ($user_max_anual_income  != '' && $user_min_anual_income != '') {
                    $fatchdataone = $fatchdataone->whereBetween('user_education_occupations.user_anual_income', [$user_min_anual_income, $user_max_anual_income]);
                }
                $to_age = $user_partnerpreference->to_user_age;
                $from_age =  $user_partnerpreference->from_user_age;
                if ($to_age != '' && $from_age != '') {
                    $fatchdataone = $fatchdataone->whereBetween('user_info.user_age', [$from_age, $to_age]);
                }
                $fatchdataone = $fatchdataone
                    ->where('user_info.user_gender', $gender)
                    ->where('user_info.user_id', '!=', $user_id)
                    ->where('user_info.user_status', 'Approved')
                    ->where('user_info.deleted', 1)
                    ->where('user_info.status', 1)
                    ->where('user_info.marriage_status', 0)
                    ->inRandomOrder()
                    ->limit(10)
                    ->get();

                $alldataofuser = array_merge($fatchdata->toArray(), $fatchdataone->toArray());
                foreach ($alldataofuser as $item) {
                    $id = $item->user_id;
                    if (!isset($uniqueArray[$id])) {
                        $uniqueArray[$id] = $item;
                    }
                }
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $alldataofuser,
                    "message" => (count($alldataofuser)) . ' records Match'
                );
                return json_encode($user_arr);
            } else {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $fatchdata,
                    "message" => count($fatchdata) . ' records Match'
                );
                return json_encode($user_arr);
            }
        }
    }
    public function byCastpremimusMatches() //done
    {
        $data = json_decode(file_get_contents("php://input"));

        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {



            // try {

            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            //dd($user_partnerpreference);
            $membership_plan = DB::table('membership_plan')->where('membership_plan_default', 1)->get(['membership_plan_type']);
            $membership_plan_type = $membership_plan[0]->membership_plan_type;
            //dd($membership_plan_type);
            // dd($user_partnerpreference->user_employed_In);
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');



            $fatchdata = DB::table('user_info')
                ->select('*')
                ->join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
                ->join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
                ->join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
                ->join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
                ->join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
                ->join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
                ->join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
                ->join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
                ->join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id');

            if ($user_activities != '' && count($user_activities) > 0 && $user_activities != null) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                $fatchdata = $fatchdata->whereNotIn('user_info.user_id', $elements);
            }

            // Validate each field in a similar manner
            if ($user_partnerpreference->user_marital_status != '' && count($user_partnerpreference->user_marital_status) > 0 && $user_partnerpreference->user_marital_status != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_marital_status', $user_partnerpreference->user_marital_status);
            }

            if ($user_partnerpreference->user_religion != '' && count($user_partnerpreference->user_religion) > 0 && $user_partnerpreference->user_religion != null) {
                $fatchdata = $fatchdata->whereIn('user_religion.user_religion', $user_partnerpreference->user_religion);
            } else {
                $user_religion = '""';
            }

            if ($user_partnerpreference->user_employed_In != '' && count($user_partnerpreference->user_employed_In) > 0 && $user_partnerpreference->user_employed_In != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_employed_In', $user_partnerpreference->user_employed_In);
            } else {
                $user_employed_In = '""';
            }

            if ($user_partnerpreference->user_occupation != '' && count($user_partnerpreference->user_occupation) > 0 && $user_partnerpreference->user_occupation != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_occupation', $user_partnerpreference->user_occupation);
                $user_occupation = implode(',', $user_partnerpreference->user_occupation);
            } else {
                $user_occupation = '""';
            }

            if ($user_partnerpreference->user_mother_toungh != '' && count($user_partnerpreference->user_mother_toungh) > 0 && $user_partnerpreference->user_mother_toungh != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_mother_toungh', $user_partnerpreference->user_mother_toungh);
                $user_mother_toungh = implode(',', $user_partnerpreference->user_mother_toungh);
            } else {
                $user_mother_toungh = '""';
            }

            if ($user_partnerpreference->user_country != '' && count($user_partnerpreference->user_country) > 0 && $user_partnerpreference->user_country != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_country', $user_partnerpreference->user_country);
                $user_country = implode(',', $user_partnerpreference->user_country);
            } else {
                $user_country = '""';
            }

            if ($user_partnerpreference->user_city != '' && count($user_partnerpreference->user_city) > 0 && $user_partnerpreference->user_city != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_city', $user_partnerpreference->user_city);
                $user_city = implode(',', $user_partnerpreference->user_city);
            } else {
                $user_city = '""';
            }

            if ($user_partnerpreference->user_state != '' && count($user_partnerpreference->user_state) > 0 && $user_partnerpreference->user_state != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_state', $user_partnerpreference->user_state);
                $user_state = implode(',', $user_partnerpreference->user_state);
            } else {
                $user_state = '""';
            }

            if ($user_partnerpreference->user_zodiacs != '' && count($user_partnerpreference->user_zodiacs) > 0 && $user_partnerpreference->user_zodiacs != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_zodiacs', $user_partnerpreference->user_zodiacs);
                $zodiacs = implode(',', $user_partnerpreference->user_zodiacs);
            } else {
                $zodiacs = '""';
            }

            if ($user_partnerpreference->user_nakshatra != '' && count($user_partnerpreference->user_nakshatra) > 0 && $user_partnerpreference->user_nakshatra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_nakhyatra', $user_partnerpreference->user_nakshatra);
                $nakshatra = implode(',', $user_partnerpreference->user_nakshatra);
            } else {
                $nakshatra = '""';
            }

            if ($user_partnerpreference->user_gotra != '' && count($user_partnerpreference->user_gotra) > 0 && $user_partnerpreference->user_gotra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_gotra', $user_partnerpreference->user_gotra);
                $gotra = implode(',', $user_partnerpreference->user_gotra);
            } else {
                $gotra = '""';
            }

            if ($user_partnerpreference->user_cast != '' && count($user_partnerpreference->user_cast) > 0 && $user_partnerpreference->user_cast != null) {
                $fatchdata = $fatchdata->whereIn('user_religion.user_caste', $user_partnerpreference->user_cast);
                $user_cast = implode(',', $user_partnerpreference->user_cast);
            } else {
                $user_cast = '""';
            }
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            if ($user_min_height  != '' && $user_max_height != '') {
                $fatchdata = $fatchdata->whereBetween('user_physical_details.user_height', [$user_min_height, $user_max_height]);
            }
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
            if ($user_max_anual_income  != '' && $user_min_anual_income != '') {
                $fatchdata = $fatchdata->whereBetween('user_education_occupations.user_anual_income', [$user_min_anual_income, $user_max_anual_income]);
            }
            $to_age = $user_partnerpreference->to_user_age;
            $from_age =  $user_partnerpreference->from_user_age;
            if ($to_age != '' && $from_age != '') {
                $fatchdata = $fatchdata->whereBetween('user_info.user_age', [$from_age, $to_age]);
            }
            $fatchdata = $fatchdata
                ->where('user_info.user_gender', $gender)
                ->where('user_info.user_id', '!=', $user_id)
                ->where('user_info.user_status', 'Approved')
                ->where('user_info.deleted', 1)
                ->where('user_info.user_membership_plan_type', '<>', $membership_plan_type)
                ->where('user_info.status', 1)
                ->where('user_info.marriage_status', 0)
                ->get();
            if (count($fatchdata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $fatchdata,
                    "message" => count($fatchdata) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            }
        }
        return json_encode($user_arr);
    }


    public function byOtherCastmatchesforindivisual() //done
    {
        $data = json_decode(file_get_contents("php://input"));

        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {

            // try {
            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            //dd($user_partnerpreference);


            // dd($user_partnerpreference->user_employed_In);
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            //dd($user_activities);


            $fatchdata = DB::table('user_info')
                ->select('*')
                ->join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
                ->join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
                ->join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
                ->join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
                ->join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
                ->join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
                ->join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
                ->join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
                ->join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id');

            if ($user_activities != '' && count($user_activities) > 0 && $user_activities != null) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                $fatchdata = $fatchdata->whereNotIn('user_info.user_id', $elements);
            }

            // Validate each field in a similar manner
            if ($user_partnerpreference->user_marital_status != '' && count($user_partnerpreference->user_marital_status) > 0 && $user_partnerpreference->user_marital_status != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_marital_status', $user_partnerpreference->user_marital_status);
            }

            if ($user_partnerpreference->user_religion != '' && count($user_partnerpreference->user_religion) > 0 && $user_partnerpreference->user_religion != null) {
                $fatchdata = $fatchdata->whereIn('user_religion.user_religion', $user_partnerpreference->user_religion);
            } else {
                $user_religion = '""';
            }

            if ($user_partnerpreference->user_employed_In != '' && count($user_partnerpreference->user_employed_In) > 0 && $user_partnerpreference->user_employed_In != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_employed_In', $user_partnerpreference->user_employed_In);
            } else {
                $user_employed_In = '""';
            }

            if ($user_partnerpreference->user_occupation != '' && count($user_partnerpreference->user_occupation) > 0 && $user_partnerpreference->user_occupation != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_occupation', $user_partnerpreference->user_occupation);
                $user_occupation = implode(',', $user_partnerpreference->user_occupation);
            } else {
                $user_occupation = '""';
            }

            if ($user_partnerpreference->user_mother_toungh != '' && count($user_partnerpreference->user_mother_toungh) > 0 && $user_partnerpreference->user_mother_toungh != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_mother_toungh', $user_partnerpreference->user_mother_toungh);
                $user_mother_toungh = implode(',', $user_partnerpreference->user_mother_toungh);
            } else {
                $user_mother_toungh = '""';
            }

            if ($user_partnerpreference->user_country != '' && count($user_partnerpreference->user_country) > 0 && $user_partnerpreference->user_country != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_country', $user_partnerpreference->user_country);
                $user_country = implode(',', $user_partnerpreference->user_country);
            } else {
                $user_country = '""';
            }

            if ($user_partnerpreference->user_city != '' && count($user_partnerpreference->user_city) > 0 && $user_partnerpreference->user_city != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_city', $user_partnerpreference->user_city);
                $user_city = implode(',', $user_partnerpreference->user_city);
            } else {
                $user_city = '""';
            }

            if ($user_partnerpreference->user_state != '' && count($user_partnerpreference->user_state) > 0 && $user_partnerpreference->user_state != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_state', $user_partnerpreference->user_state);
                $user_state = implode(',', $user_partnerpreference->user_state);
            } else {
                $user_state = '""';
            }

            if ($user_partnerpreference->user_zodiacs != '' && count($user_partnerpreference->user_zodiacs) > 0 && $user_partnerpreference->user_zodiacs != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_zodiacs', $user_partnerpreference->user_zodiacs);
                $zodiacs = implode(',', $user_partnerpreference->user_zodiacs);
            } else {
                $zodiacs = '""';
            }

            if ($user_partnerpreference->user_nakshatra != '' && count($user_partnerpreference->user_nakshatra) > 0 && $user_partnerpreference->user_nakshatra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_nakhyatra', $user_partnerpreference->user_nakshatra);
                $nakshatra = implode(',', $user_partnerpreference->user_nakshatra);
            } else {
                $nakshatra = '""';
            }

            if ($user_partnerpreference->user_gotra != '' && count($user_partnerpreference->user_gotra) > 0 && $user_partnerpreference->user_gotra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_gotra', $user_partnerpreference->user_gotra);
                $gotra = implode(',', $user_partnerpreference->user_gotra);
            } else {
                $gotra = '""';
            }

            if ($user_partnerpreference->user_cast != '' && count($user_partnerpreference->user_cast) > 0 && $user_partnerpreference->user_cast != null) {
                $fatchdata = $fatchdata->whereNotIn('user_religion.user_caste', $user_partnerpreference->user_cast);
                $user_cast = implode(',', $user_partnerpreference->user_cast);
            } else {
                $user_cast = '""';
            }
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            if ($user_min_height  != '' && $user_max_height != '') {
                $fatchdata = $fatchdata->whereBetween('user_physical_details.user_height', [$user_min_height, $user_max_height]);
            }
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
            if ($user_max_anual_income  != '' && $user_min_anual_income != '') {
                $fatchdata = $fatchdata->whereBetween('user_education_occupations.user_anual_income', [$user_min_anual_income, $user_max_anual_income]);
            }
            $to_age = $user_partnerpreference->to_user_age;
            $from_age =  $user_partnerpreference->from_user_age;
            if ($to_age != '' && $from_age != '') {
                $fatchdata = $fatchdata->whereBetween('user_info.user_age', [$from_age, $to_age]);
            }
            $fatchdata = $fatchdata
                ->where('user_info.user_gender', $gender)
                ->where('user_info.user_id', '!=', $user_id)
                ->where('user_info.user_status', 'Approved')
                ->where('user_info.deleted', 1)
                ->where('user_info.status', 1)
                ->where('user_info.marriage_status', 0)
                ->get();
            //dd($fatchdata);

            $shuffledData = $fatchdata->shuffle();
            //shuffle($shuffledData);

            if (count($shuffledData) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $shuffledData,
                    "message" => count($shuffledData) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            }
        }
        return json_encode($user_arr);
    }
    public function byOtherCastRecentlyJoinedMatches() //done
    {
        $data = json_decode(file_get_contents("php://input"));
        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {



            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            //dd($user_partnerpreference);


            // dd($user_partnerpreference->user_employed_In);
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            //dd($user_activities);


            $fatchdata = DB::table('user_info')
                ->select('*')
                ->join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
                ->join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
                ->join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
                ->join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
                ->join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
                ->join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
                ->join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
                ->join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
                ->join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id');

            if ($user_activities != '' && count($user_activities) > 0 && $user_activities != null) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                $fatchdata = $fatchdata->whereNotIn('user_info.user_id', $elements);
            }

            // Validate each field in a similar manner
            if ($user_partnerpreference->user_marital_status != '' && count($user_partnerpreference->user_marital_status) > 0 && $user_partnerpreference->user_marital_status != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_marital_status', $user_partnerpreference->user_marital_status);
            }

            if ($user_partnerpreference->user_religion != '' && count($user_partnerpreference->user_religion) > 0 && $user_partnerpreference->user_religion != null) {
                $fatchdata = $fatchdata->whereIn('user_religion.user_religion', $user_partnerpreference->user_religion);
            } else {
                $user_religion = '""';
            }

            if ($user_partnerpreference->user_employed_In != '' && count($user_partnerpreference->user_employed_In) > 0 && $user_partnerpreference->user_employed_In != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_employed_In', $user_partnerpreference->user_employed_In);
            } else {
                $user_employed_In = '""';
            }

            if ($user_partnerpreference->user_occupation != '' && count($user_partnerpreference->user_occupation) > 0 && $user_partnerpreference->user_occupation != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_occupation', $user_partnerpreference->user_occupation);
                $user_occupation = implode(',', $user_partnerpreference->user_occupation);
            } else {
                $user_occupation = '""';
            }

            if ($user_partnerpreference->user_mother_toungh != '' && count($user_partnerpreference->user_mother_toungh) > 0 && $user_partnerpreference->user_mother_toungh != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_mother_toungh', $user_partnerpreference->user_mother_toungh);
                $user_mother_toungh = implode(',', $user_partnerpreference->user_mother_toungh);
            } else {
                $user_mother_toungh = '""';
            }

            if ($user_partnerpreference->user_country != '' && count($user_partnerpreference->user_country) > 0 && $user_partnerpreference->user_country != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_country', $user_partnerpreference->user_country);
                $user_country = implode(',', $user_partnerpreference->user_country);
            } else {
                $user_country = '""';
            }

            if ($user_partnerpreference->user_city != '' && count($user_partnerpreference->user_city) > 0 && $user_partnerpreference->user_city != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_city', $user_partnerpreference->user_city);
                $user_city = implode(',', $user_partnerpreference->user_city);
            } else {
                $user_city = '""';
            }

            if ($user_partnerpreference->user_state != '' && count($user_partnerpreference->user_state) > 0 && $user_partnerpreference->user_state != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_state', $user_partnerpreference->user_state);
                $user_state = implode(',', $user_partnerpreference->user_state);
            } else {
                $user_state = '""';
            }

            if ($user_partnerpreference->user_zodiacs != '' && count($user_partnerpreference->user_zodiacs) > 0 && $user_partnerpreference->user_zodiacs != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_zodiacs', $user_partnerpreference->user_zodiacs);
                $zodiacs = implode(',', $user_partnerpreference->user_zodiacs);
            } else {
                $zodiacs = '""';
            }

            if ($user_partnerpreference->user_nakshatra != '' && count($user_partnerpreference->user_nakshatra) > 0 && $user_partnerpreference->user_nakshatra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_nakhyatra', $user_partnerpreference->user_nakshatra);
                $nakshatra = implode(',', $user_partnerpreference->user_nakshatra);
            } else {
                $nakshatra = '""';
            }

            if ($user_partnerpreference->user_gotra != '' && count($user_partnerpreference->user_gotra) > 0 && $user_partnerpreference->user_gotra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_gotra', $user_partnerpreference->user_gotra);
                $gotra = implode(',', $user_partnerpreference->user_gotra);
            } else {
                $gotra = '""';
            }

            if ($user_partnerpreference->user_cast != '' && count($user_partnerpreference->user_cast) > 0 && $user_partnerpreference->user_cast != null) {
                $fatchdata = $fatchdata->whereNotIn('user_religion.user_caste', $user_partnerpreference->user_cast);
                $user_cast = implode(',', $user_partnerpreference->user_cast);
            } else {
                $user_cast = '""';
            }
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            if ($user_min_height  != '' && $user_max_height != '') {
                $fatchdata = $fatchdata->whereBetween('user_physical_details.user_height', [$user_min_height, $user_max_height]);
            }
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
            if ($user_max_anual_income  != '' && $user_min_anual_income != '') {
                $fatchdata = $fatchdata->whereBetween('user_education_occupations.user_anual_income', [$user_min_anual_income, $user_max_anual_income]);
            }
            $to_age = $user_partnerpreference->to_user_age;
            $from_age =  $user_partnerpreference->from_user_age;
            if ($to_age != '' && $from_age != '') {
                $fatchdata = $fatchdata->whereBetween('user_info.user_age', [$from_age, $to_age]);
            }
            $user_creation_date = date('Y-m-d H:i:s', strtotime('-30 days'));
            $fatchdata = $fatchdata
                ->where('user_info.user_gender', $gender)
                ->where('user_info.user_id', '!=', $user_id)
                ->where('user_info.user_status', 'Approved')
                ->where('user_info.deleted', 1)
                ->where('user_info.status', 1)
                ->where('user_info.marriage_status', 0)
                ->where('user_info.user_creation_date_time', '>=', $user_creation_date)
                ->get();
            //dd($fatchdata);

























































































            // try {
            // $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            // $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            // $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            // $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            // $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            // if (count($user_activities) > 0) {
            //     $user_block_list = $user_activities[0]->user_block_list;
            //     $elements = explode(',', $user_block_list);
            //     // Enclose each element in double quotes
            //     $quotedElements = array_map(function ($element) {
            //         return '"' . $element . '"';
            //     }, $elements);
            //     // Join the elements with commas
            //     $outputString = $elements;
            //     //implode(",", $quotedElements);
            // } else {
            //     $outputString = [];
            // }
            //



            // $alldata = DB::table('user_info')
            //     ->select('*')
            //     ->Join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
            //     ->Join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
            //     ->Join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
            //     ->Join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
            //     ->Join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
            //     ->Join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
            //     ->Join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
            //     ->Join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
            //     ->Join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id')
            //     ->where('user_info.user_gender', $gender)
            //     ->where('user_info.user_id', '<>', $user_id)
            //     ->where('user_info.user_status', 'Approved')
            //     ->whereNotIn('user_info.user_id', $outputString)
            //     ->where('user_info.deleted', 1)
            //     ->where('user_info.status', 1)
            //     ->where('user_info.marriage_status', 0)
            //


            // if ($user_partnerpreference->user_cast != '') {
            //     $alldata = $alldata->whereIn('user_religion.user_caste', $user_partnerpreference->user_cast);
            //     $user_cast = implode(',', $user_partnerpreference->user_cast);
            // } else {
            //     $user_cast = '""';
            // }
            // $alldata = $alldata->get();






            if (count($fatchdata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $fatchdata,
                    "message" => count($fatchdata) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            }
            // } catch (Exception $e) {
            //     $user_arr = array(
            //         "status" => false,
            //         "success" => false,
            //         "message" => "No Match Found",
            //     );
            // }

        }
        return json_encode($user_arr);
    }
    public function byOtherCastgetSpotlightdata() //done
    {
        $data = json_decode(file_get_contents("php://input"));
        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {


            // try {
            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            //dd($user_partnerpreference);


            // dd($user_partnerpreference->user_employed_In);
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            //dd($user_activities);


            $fatchdata = DB::table('user_info')
                ->select('*')
                ->join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
                ->join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
                ->join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
                ->join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
                ->join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
                ->join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
                ->join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
                ->join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
                ->join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id');

            if ($user_activities != '' && count($user_activities) > 0 && $user_activities != null) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                $fatchdata = $fatchdata->whereNotIn('user_info.user_id', $elements);
            }

            // Validate each field in a similar manner
            if ($user_partnerpreference->user_marital_status != '' && count($user_partnerpreference->user_marital_status) > 0 && $user_partnerpreference->user_marital_status != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_marital_status', $user_partnerpreference->user_marital_status);
            }

            if ($user_partnerpreference->user_religion != '' && count($user_partnerpreference->user_religion) > 0 && $user_partnerpreference->user_religion != null) {
                $fatchdata = $fatchdata->whereIn('user_religion.user_religion', $user_partnerpreference->user_religion);
            } else {
                $user_religion = '""';
            }

            if ($user_partnerpreference->user_employed_In != '' && count($user_partnerpreference->user_employed_In) > 0 && $user_partnerpreference->user_employed_In != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_employed_In', $user_partnerpreference->user_employed_In);
            } else {
                $user_employed_In = '""';
            }

            if ($user_partnerpreference->user_occupation != '' && count($user_partnerpreference->user_occupation) > 0 && $user_partnerpreference->user_occupation != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_occupation', $user_partnerpreference->user_occupation);
                $user_occupation = implode(',', $user_partnerpreference->user_occupation);
            } else {
                $user_occupation = '""';
            }

            if ($user_partnerpreference->user_mother_toungh != '' && count($user_partnerpreference->user_mother_toungh) > 0 && $user_partnerpreference->user_mother_toungh != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_mother_toungh', $user_partnerpreference->user_mother_toungh);
                $user_mother_toungh = implode(',', $user_partnerpreference->user_mother_toungh);
            } else {
                $user_mother_toungh = '""';
            }

            if ($user_partnerpreference->user_country != '' && count($user_partnerpreference->user_country) > 0 && $user_partnerpreference->user_country != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_country', $user_partnerpreference->user_country);
                $user_country = implode(',', $user_partnerpreference->user_country);
            } else {
                $user_country = '""';
            }

            if ($user_partnerpreference->user_city != '' && count($user_partnerpreference->user_city) > 0 && $user_partnerpreference->user_city != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_city', $user_partnerpreference->user_city);
                $user_city = implode(',', $user_partnerpreference->user_city);
            } else {
                $user_city = '""';
            }

            if ($user_partnerpreference->user_state != '' && count($user_partnerpreference->user_state) > 0 && $user_partnerpreference->user_state != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_state', $user_partnerpreference->user_state);
                $user_state = implode(',', $user_partnerpreference->user_state);
            } else {
                $user_state = '""';
            }

            if ($user_partnerpreference->user_zodiacs != '' && count($user_partnerpreference->user_zodiacs) > 0 && $user_partnerpreference->user_zodiacs != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_zodiacs', $user_partnerpreference->user_zodiacs);
                $zodiacs = implode(',', $user_partnerpreference->user_zodiacs);
            } else {
                $zodiacs = '""';
            }

            if ($user_partnerpreference->user_nakshatra != '' && count($user_partnerpreference->user_nakshatra) > 0 && $user_partnerpreference->user_nakshatra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_nakhyatra', $user_partnerpreference->user_nakshatra);
                $nakshatra = implode(',', $user_partnerpreference->user_nakshatra);
            } else {
                $nakshatra = '""';
            }

            if ($user_partnerpreference->user_gotra != '' && count($user_partnerpreference->user_gotra) > 0 && $user_partnerpreference->user_gotra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_gotra', $user_partnerpreference->user_gotra);
                $gotra = implode(',', $user_partnerpreference->user_gotra);
            } else {
                $gotra = '""';
            }

            if ($user_partnerpreference->user_cast != '' && count($user_partnerpreference->user_cast) > 0 && $user_partnerpreference->user_cast != null) {
                $fatchdata = $fatchdata->whereNotIn('user_religion.user_caste', $user_partnerpreference->user_cast);
                $user_cast = implode(',', $user_partnerpreference->user_cast);
            } else {
                $user_cast = '""';
            }
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            if ($user_min_height  != '' && $user_max_height != '') {
                $fatchdata = $fatchdata->whereBetween('user_physical_details.user_height', [$user_min_height, $user_max_height]);
            }
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
            if ($user_max_anual_income  != '' && $user_min_anual_income != '') {
                $fatchdata = $fatchdata->whereBetween('user_education_occupations.user_anual_income', [$user_min_anual_income, $user_max_anual_income]);
            }
            $to_age = $user_partnerpreference->to_user_age;
            $from_age =  $user_partnerpreference->from_user_age;
            if ($to_age != '' && $from_age != '') {
                $fatchdata = $fatchdata->whereBetween('user_info.user_age', [$from_age, $to_age]);
            }
            $fatchdata = $fatchdata
                ->where('user_info.user_gender', $gender)
                ->where('user_info.user_id', '!=', $user_id)
                ->where('user_info.user_status', 'Approved')
                ->where('user_info.deleted', 1)
                ->where('user_info.status', 1)
                ->where('user_info.marriage_status', 0)
                ->where('user_info.spotlight', 1)
                ->get();

































































































            // // try {
            // $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            // $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            // $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            // $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            // $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            // if (count($user_activities) > 0) {
            //     $user_block_list = $user_activities[0]->user_block_list;
            //     $elements = explode(',', $user_block_list);
            //     // Enclose each element in double quotes
            //     $quotedElements = array_map(function ($element) {
            //         return '"' . $element . '"';
            //     }, $elements);
            //     // Join the elements with commas
            //     $outputString = $quotedElements;
            //     //implode(",", $quotedElements);
            // } else {
            //     $outputString = [];
            // }
            // $alldata = DB::table('user_info')
            //     ->select('*')
            //     ->Join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
            //     ->Join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
            //     ->Join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
            //     ->Join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
            //     ->Join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
            //     ->Join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
            //     ->Join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
            //     ->Join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
            //     ->Join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id')
            //     ->where('user_info.user_gender', $gender)
            //     ->where('user_info.user_id', '<>', $user_id)
            //     ->where('user_info.spotlight', 1)
            //     ->whereNotIn('user_info.user_id', $outputString)
            //     ->where('user_info.user_status', 'Approved')
            //     ->where('user_info.deleted', 1);
            // if ($user_partnerpreference->user_cast != '') {
            //     $alldata = $alldata->whereIn('user_religion.user_caste', $user_partnerpreference->user_cast);
            //     $user_cast = implode(',', $user_partnerpreference->user_cast);
            // } else {
            //     $user_cast = '""';
            // }
            // $alldata = $alldata->get();


            if (count($fatchdata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $fatchdata,
                    "message" => count($fatchdata) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            }
            // } catch (Exception $e) {
            //     $user_arr = array(
            //         "status" => false,
            //         "success" => false,
            //         "message" => "No Match Found",
            //     );
            // }

        }
        return json_encode($user_arr);
    }
    public function byOtherCastgetOnlinedata() //done
    {
        $data = json_decode(file_get_contents("php://input"));
        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parameters",
            );
        } else {

            // try {
            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            //dd($user_partnerpreference);


            // dd($user_partnerpreference->user_employed_In);
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');
            //dd($user_activities);


            $fatchdata = DB::table('user_info')
                ->select('*')
                ->join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
                ->join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
                ->join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
                ->join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
                ->join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
                ->join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
                ->join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
                ->join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
                ->join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id');

            if ($user_activities != '' && count($user_activities) > 0 && $user_activities != null) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                $fatchdata = $fatchdata->whereNotIn('user_info.user_id', $elements);
            }

            // Validate each field in a similar manner
            if ($user_partnerpreference->user_marital_status != '' && count($user_partnerpreference->user_marital_status) > 0 && $user_partnerpreference->user_marital_status != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_marital_status', $user_partnerpreference->user_marital_status);
            }

            if ($user_partnerpreference->user_religion != '' && count($user_partnerpreference->user_religion) > 0 && $user_partnerpreference->user_religion != null) {
                $fatchdata = $fatchdata->whereIn('user_religion.user_religion', $user_partnerpreference->user_religion);
            } else {
                $user_religion = '""';
            }

            if ($user_partnerpreference->user_employed_In != '' && count($user_partnerpreference->user_employed_In) > 0 && $user_partnerpreference->user_employed_In != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_employed_In', $user_partnerpreference->user_employed_In);
            } else {
                $user_employed_In = '""';
            }

            if ($user_partnerpreference->user_occupation != '' && count($user_partnerpreference->user_occupation) > 0 && $user_partnerpreference->user_occupation != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_occupation', $user_partnerpreference->user_occupation);
                $user_occupation = implode(',', $user_partnerpreference->user_occupation);
            } else {
                $user_occupation = '""';
            }

            if ($user_partnerpreference->user_mother_toungh != '' && count($user_partnerpreference->user_mother_toungh) > 0 && $user_partnerpreference->user_mother_toungh != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_mother_toungh', $user_partnerpreference->user_mother_toungh);
                $user_mother_toungh = implode(',', $user_partnerpreference->user_mother_toungh);
            } else {
                $user_mother_toungh = '""';
            }

            if ($user_partnerpreference->user_country != '' && count($user_partnerpreference->user_country) > 0 && $user_partnerpreference->user_country != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_country', $user_partnerpreference->user_country);
                $user_country = implode(',', $user_partnerpreference->user_country);
            } else {
                $user_country = '""';
            }

            if ($user_partnerpreference->user_city != '' && count($user_partnerpreference->user_city) > 0 && $user_partnerpreference->user_city != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_city', $user_partnerpreference->user_city);
                $user_city = implode(',', $user_partnerpreference->user_city);
            } else {
                $user_city = '""';
            }

            if ($user_partnerpreference->user_state != '' && count($user_partnerpreference->user_state) > 0 && $user_partnerpreference->user_state != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_state', $user_partnerpreference->user_state);
                $user_state = implode(',', $user_partnerpreference->user_state);
            } else {
                $user_state = '""';
            }

            if ($user_partnerpreference->user_zodiacs != '' && count($user_partnerpreference->user_zodiacs) > 0 && $user_partnerpreference->user_zodiacs != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_zodiacs', $user_partnerpreference->user_zodiacs);
                $zodiacs = implode(',', $user_partnerpreference->user_zodiacs);
            } else {
                $zodiacs = '""';
            }

            if ($user_partnerpreference->user_nakshatra != '' && count($user_partnerpreference->user_nakshatra) > 0 && $user_partnerpreference->user_nakshatra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_nakhyatra', $user_partnerpreference->user_nakshatra);
                $nakshatra = implode(',', $user_partnerpreference->user_nakshatra);
            } else {
                $nakshatra = '""';
            }

            if ($user_partnerpreference->user_gotra != '' && count($user_partnerpreference->user_gotra) > 0 && $user_partnerpreference->user_gotra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_gotra', $user_partnerpreference->user_gotra);
                $gotra = implode(',', $user_partnerpreference->user_gotra);
            } else {
                $gotra = '""';
            }

            if ($user_partnerpreference->user_cast != '' && count($user_partnerpreference->user_cast) > 0 && $user_partnerpreference->user_cast != null) {
                $fatchdata = $fatchdata->whereNotIn('user_religion.user_caste', $user_partnerpreference->user_cast);
                $user_cast = implode(',', $user_partnerpreference->user_cast);
            } else {
                $user_cast = '""';
            }
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            if ($user_min_height  != '' && $user_max_height != '') {
                $fatchdata = $fatchdata->whereBetween('user_physical_details.user_height', [$user_min_height, $user_max_height]);
            }
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
            if ($user_max_anual_income  != '' && $user_min_anual_income != '') {
                $fatchdata = $fatchdata->whereBetween('user_education_occupations.user_anual_income', [$user_min_anual_income, $user_max_anual_income]);
            }
            $to_age = $user_partnerpreference->to_user_age;
            $from_age =  $user_partnerpreference->from_user_age;
            if ($to_age != '' && $from_age != '') {
                $fatchdata = $fatchdata->whereBetween('user_info.user_age', [$from_age, $to_age]);
            }
            $fatchdata = $fatchdata
                ->where('user_info.user_gender', $gender)
                ->where('user_info.user_id', '!=', $user_id)
                ->where('user_info.user_status', 'Approved')
                ->where('user_info.deleted', 1)
                ->where('user_info.status', 1)
                ->where('user_info.marriage_status', 0)
                ->where('user_info.online_status', 1)
                ->get();

            if (count($fatchdata) < 5) {
                $fatchdataone = DB::table('user_info')
                    ->select('*')
                    ->join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
                    ->join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
                    ->join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
                    ->join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
                    ->join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
                    ->join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
                    ->join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
                    ->join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
                    ->join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id');

                if ($user_activities != '') {
                    if (count($user_activities) > 0) {
                        $user_block_list = $user_activities[0]->user_block_list;
                        $elements = explode(',', $user_block_list);

                        $fatchdataone = $fatchdataone->whereNotIn('user_info.user_id', $elements);
                    } else {
                        $outputString = '""';
                    }
                }

                if ($user_partnerpreference->user_marital_status != '') {
                    $fatchdataone = $fatchdataone->whereIn('user_info.user_marital_status', $user_partnerpreference->user_marital_status);
                } else {
                    $user_marital_status = '""';
                }

                if ($user_partnerpreference->user_religion != '') {
                    $user_religion = implode(',', $user_partnerpreference->user_religion);

                    $fatchdataone = $fatchdataone->whereIn('user_religion.user_religion', $user_partnerpreference->user_religion);
                } else {
                    $user_religion = '""';
                }

                if ($user_partnerpreference->user_employed_In != '') {
                    $fatchdataone = $fatchdataone->whereIn('user_education_occupations.user_employed_In', $user_partnerpreference->user_employed_In);
                } else {
                    $user_employed_In = '""';
                }

                if ($user_partnerpreference->user_occupation != '') {
                    $fatchdataone = $fatchdataone->whereIn('user_education_occupations.user_occupation', $user_partnerpreference->user_occupation);
                    $user_occupation = implode(',', $user_partnerpreference->user_occupation);
                } else {
                    $user_occupation = '""';
                }

                if ($user_partnerpreference->user_mother_toungh != '') {
                    $fatchdataone = $fatchdataone->whereIn('user_info.user_mother_toungh', $user_partnerpreference->user_mother_toungh);
                    $user_mother_toungh = implode(',', $user_partnerpreference->user_mother_toungh);
                } else {
                    $user_mother_toungh = '""';
                }



                if ($user_partnerpreference->user_country != '') {
                    $fatchdataone = $fatchdataone->whereIn('user_locations.user_country', $user_partnerpreference->user_country);
                    $user_country = implode(',', $user_partnerpreference->user_country);
                } else {
                    $user_country = '""';
                }

                if ($user_partnerpreference->user_city != '') {
                    $fatchdataone = $fatchdataone->whereIn('user_locations.user_city', $user_partnerpreference->user_city);
                    $user_city = implode(',', $user_partnerpreference->user_city);
                } else {
                    $user_city = '""';
                }

                if ($user_partnerpreference->user_state != '') {
                    $fatchdataone = $fatchdataone->whereIn('user_locations.user_state', $user_partnerpreference->user_state);
                    $user_state = implode(',', $user_partnerpreference->user_state);
                } else {
                    $user_state = '""';
                }

                if ($user_partnerpreference->user_zodiacs != '') {
                    $fatchdataone = $fatchdataone->whereIn('user_horoscope.user_zodiacs', $user_partnerpreference->user_zodiacs);
                    $zodiacs = implode(',', $user_partnerpreference->user_zodiacs);
                } else {
                    $zodiacs = '""';
                }

                if ($user_partnerpreference->user_nakshatra != '') {
                    $fatchdataone = $fatchdataone->whereIn('user_horoscope.user_nakhyatra', $user_partnerpreference->user_nakshatra);
                    $nakshatra = implode(',', $user_partnerpreference->user_nakshatra);
                } else {
                    $nakshatra = '""';
                }

                if ($user_partnerpreference->user_gotra != '') {
                    $fatchdataone = $fatchdataone->whereIn('user_horoscope.user_gotra', $user_partnerpreference->user_gotra);
                    $gotra = implode(',', $user_partnerpreference->user_gotra);
                } else {
                    $gotra = '""';
                }

                if ($user_partnerpreference->user_cast != '') {
                    $fatchdataone = $fatchdataone->whereNotIn('user_religion.user_caste', $user_partnerpreference->user_cast);
                    $user_cast = implode(',', $user_partnerpreference->user_cast);
                } else {
                    $user_cast = '""';
                }
                $user_min_height = $user_partnerpreference->user_min_height;
                $user_max_height = $user_partnerpreference->user_max_height;
                if ($user_min_height  != '' && $user_max_height != '') {
                    $fatchdataone = $fatchdataone->whereBetween('user_physical_details.user_height', [$user_min_height, $user_max_height]);
                }
                $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
                $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
                if ($user_max_anual_income  != '' && $user_min_anual_income != '') {
                    $fatchdataone = $fatchdataone->whereBetween('user_education_occupations.user_anual_income', [$user_min_anual_income, $user_max_anual_income]);
                }
                $to_age = $user_partnerpreference->to_user_age;
                $from_age =  $user_partnerpreference->from_user_age;
                if ($to_age != '' && $from_age != '') {
                    $fatchdataone = $fatchdataone->whereBetween('user_info.user_age', [$from_age, $to_age]);
                }
                $fatchdataone = $fatchdataone
                    ->where('user_info.user_gender', $gender)
                    ->where('user_info.user_id', '!=', $user_id)
                    ->where('user_info.user_status', 'Approved')
                    ->where('user_info.deleted', 1)
                    ->where('user_info.status', 1)
                    ->where('user_info.marriage_status', 0)
                    ->inRandomOrder()
                    ->limit(10)
                    ->get();
                $uniqueArray = [];
                $alldataofuser = array_merge($fatchdata->toArray(), $fatchdataone->toArray());
                foreach ($alldataofuser as $item) {
                    $id = $item->user_id;
                    if (!isset($uniqueArray[$id])) {
                        $uniqueArray[$id] = $item;
                    }
                }
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $alldataofuser,
                    "message" => (count($alldataofuser)) . ' records Match'
                );
                return json_encode($user_arr);
            } else {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $fatchdata,
                    "message" => count($fatchdata) . ' records Match'
                );
                return json_encode($user_arr);
            }
        }
    }
    public function byOtherCastpremimusMatches() //done
    {
        $data = json_decode(file_get_contents("php://input"));

        $user_id = isset($data->user_id) ? $data->user_id : '';
        if ($user_id == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Please enter required parametes",
            );
        } else {



            // try {

            $user_partnerpreference = DB::table('user_partnerpreference')->where('user_ID', $user_id)->get();
            $user_partnerpreference = json_decode($user_partnerpreference[0]->json_data);
            //dd($user_partnerpreference);
            $membership_plan = DB::table('membership_plan')->where('membership_plan_default', 1)->get(['membership_plan_type']);
            $membership_plan_type = $membership_plan[0]->membership_plan_type;
            //dd($membership_plan_type);
            // dd($user_partnerpreference->user_employed_In);
            $user = DB::table('user_info')->where('user_ID', $user_id)->get('user_gender');
            $gender = $user[0]->user_gender == "male" ? 'female' : 'male';
            $user_activities = DB::table('user_activities')->where('user_id', $user_id)->get('user_block_list');



            $fatchdata = DB::table('user_info')
                ->select('*')
                ->join('user_religion', 'user_info.user_id', '=', 'user_religion.user_ID')
                ->join('user_locations', 'user_info.user_id', '=', 'user_locations.user_ID')
                ->join('user_family', 'user_info.user_id', '=', 'user_family.user_ID')
                ->join('user_physical_details', 'user_info.user_id', '=', 'user_physical_details.user_ID')
                ->join('user_about', 'user_info.user_id', '=', 'user_about.user_ID')
                ->join('user_diet_hobbies', 'user_info.user_id', '=', 'user_diet_hobbies.user_ID')
                ->join('user_education_occupations', 'user_info.user_id', '=', 'user_education_occupations.user_ID')
                ->join('auth_user', 'user_info.user_id', '=', 'auth_user.auth_ID')
                ->join('user_horoscope', 'user_info.user_id', '=', 'user_horoscope.user_id');

            if ($user_activities != '' && count($user_activities) > 0 && $user_activities != null) {
                $user_block_list = $user_activities[0]->user_block_list;
                $elements = explode(',', $user_block_list);
                $fatchdata = $fatchdata->whereNotIn('user_info.user_id', $elements);
            }

            // Validate each field in a similar manner
            if ($user_partnerpreference->user_marital_status != '' && count($user_partnerpreference->user_marital_status) > 0 && $user_partnerpreference->user_marital_status != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_marital_status', $user_partnerpreference->user_marital_status);
            }

            if ($user_partnerpreference->user_religion != '' && count($user_partnerpreference->user_religion) > 0 && $user_partnerpreference->user_religion != null) {
                $fatchdata = $fatchdata->whereIn('user_religion.user_religion', $user_partnerpreference->user_religion);
            } else {
                $user_religion = '""';
            }

            if ($user_partnerpreference->user_employed_In != '' && count($user_partnerpreference->user_employed_In) > 0 && $user_partnerpreference->user_employed_In != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_employed_In', $user_partnerpreference->user_employed_In);
            } else {
                $user_employed_In = '""';
            }

            if ($user_partnerpreference->user_occupation != '' && count($user_partnerpreference->user_occupation) > 0 && $user_partnerpreference->user_occupation != null) {
                $fatchdata = $fatchdata->whereIn('user_education_occupations.user_occupation', $user_partnerpreference->user_occupation);
                $user_occupation = implode(',', $user_partnerpreference->user_occupation);
            } else {
                $user_occupation = '""';
            }

            if ($user_partnerpreference->user_mother_toungh != '' && count($user_partnerpreference->user_mother_toungh) > 0 && $user_partnerpreference->user_mother_toungh != null) {
                $fatchdata = $fatchdata->whereIn('user_info.user_mother_toungh', $user_partnerpreference->user_mother_toungh);
                $user_mother_toungh = implode(',', $user_partnerpreference->user_mother_toungh);
            } else {
                $user_mother_toungh = '""';
            }

            if ($user_partnerpreference->user_country != '' && count($user_partnerpreference->user_country) > 0 && $user_partnerpreference->user_country != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_country', $user_partnerpreference->user_country);
                $user_country = implode(',', $user_partnerpreference->user_country);
            } else {
                $user_country = '""';
            }

            if ($user_partnerpreference->user_city != '' && count($user_partnerpreference->user_city) > 0 && $user_partnerpreference->user_city != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_city', $user_partnerpreference->user_city);
                $user_city = implode(',', $user_partnerpreference->user_city);
            } else {
                $user_city = '""';
            }

            if ($user_partnerpreference->user_state != '' && count($user_partnerpreference->user_state) > 0 && $user_partnerpreference->user_state != null) {
                $fatchdata = $fatchdata->whereIn('user_locations.user_state', $user_partnerpreference->user_state);
                $user_state = implode(',', $user_partnerpreference->user_state);
            } else {
                $user_state = '""';
            }

            if ($user_partnerpreference->user_zodiacs != '' && count($user_partnerpreference->user_zodiacs) > 0 && $user_partnerpreference->user_zodiacs != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_zodiacs', $user_partnerpreference->user_zodiacs);
                $zodiacs = implode(',', $user_partnerpreference->user_zodiacs);
            } else {
                $zodiacs = '""';
            }

            if ($user_partnerpreference->user_nakshatra != '' && count($user_partnerpreference->user_nakshatra) > 0 && $user_partnerpreference->user_nakshatra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_nakhyatra', $user_partnerpreference->user_nakshatra);
                $nakshatra = implode(',', $user_partnerpreference->user_nakshatra);
            } else {
                $nakshatra = '""';
            }

            if ($user_partnerpreference->user_gotra != '' && count($user_partnerpreference->user_gotra) > 0 && $user_partnerpreference->user_gotra != null) {
                $fatchdata = $fatchdata->whereIn('user_horoscope.user_gotra', $user_partnerpreference->user_gotra);
                $gotra = implode(',', $user_partnerpreference->user_gotra);
            } else {
                $gotra = '""';
            }

            if ($user_partnerpreference->user_cast != '' && count($user_partnerpreference->user_cast) > 0 && $user_partnerpreference->user_cast != null) {
                $fatchdata = $fatchdata->whereNotIn('user_religion.user_caste', $user_partnerpreference->user_cast);
                $user_cast = implode(',', $user_partnerpreference->user_cast);
            } else {
                $user_cast = '""';
            }
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            if ($user_min_height  != '' && $user_max_height != '') {
                $fatchdata = $fatchdata->whereBetween('user_physical_details.user_height', [$user_min_height, $user_max_height]);
            }
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
            if ($user_max_anual_income  != '' && $user_min_anual_income != '') {
                $fatchdata = $fatchdata->whereBetween('user_education_occupations.user_anual_income', [$user_min_anual_income, $user_max_anual_income]);
            }
            $to_age = $user_partnerpreference->to_user_age;
            $from_age =  $user_partnerpreference->from_user_age;
            if ($to_age != '' && $from_age != '') {
                $fatchdata = $fatchdata->whereBetween('user_info.user_age', [$from_age, $to_age]);
            }
            $fatchdata = $fatchdata
                ->where('user_info.user_gender', $gender)
                ->where('user_info.user_id', '!=', $user_id)
                ->where('user_info.user_status', 'Approved')
                ->where('user_info.deleted', 1)
                ->where('user_info.user_membership_plan_type', '<>', $membership_plan_type)
                ->where('user_info.status', 1)
                ->where('user_info.marriage_status', 0)
                ->get();


            // dd($alldata);
            if (count($fatchdata) > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => $fatchdata,
                    "message" => count($fatchdata) . ' records Match'
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "No Match Found",
                );
            }
            // } catch (Exception $e) {
            //     $user_arr = array(
            //         "status" => false,
            //         "success" => false,
            //         "message" => "No Match Found",
            //     );
            // }

        }
        return json_encode($user_arr);
    }
}
