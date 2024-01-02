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
            WHERE user_info.user_gender = '$gender'  AND user_info.user_status = 'Approved'   AND  user_info.user_id NOT IN ($outputString) AND user_info.user_status = 'Approved' AND user_info.deleted = 1 AND user_info.status = 1 AND user_info.marriage_status = 0 AND user_info.user_has_complete_profile = 1 ;");


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
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
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
             OR  user_physical_details.user_height BETWEEN '$user_min_height' AND '$user_max_height')
            AND
            ( user_info.user_gender = '$gender'  AND user_info.user_status = 'Approved' AND user_info.deleted = 1 AND user_info.status = 1 AND user_info.user_id NOT IN ($outputString)  AND user_info.marriage_status = 0 AND user_info.user_all_table_complited = 1);");

            //  return "SELECT * FROM user_info
            //  LEFT JOIN user_religion ON user_info.user_id = user_religion.user_ID
            //  LEFT JOIN user_locations ON user_info.user_id = user_locations.user_ID
            //  LEFT JOIN user_family ON user_info.user_id = user_family.user_ID
            //  LEFT JOIN user_physical_details ON user_info.user_id = user_physical_details.user_ID
            //  LEFT JOIN user_about ON user_info.user_id = user_about.user_ID
            //  LEFT JOIN user_diet_hobbies ON user_info.user_id = user_diet_hobbies.user_ID
            //  LEFT JOIN user_education_occupations ON user_info.user_id = user_education_occupations.user_ID
            //  LEFT JOIN auth_user ON user_info.user_id = auth_user.auth_ID
            //  LEFT JOIN user_horoscope ON  user_info.user_id = user_horoscope.user_id
            //  WHERE
            //  (
            //   user_religion.user_religion IN ($user_religion)

            //   OR  user_info.user_marital_status IN ($user_marital_status)
            //   OR  user_education_occupations.user_employed_In IN  ($user_employed_In)
            //   OR  user_locations.user_country IN ($user_country)
            //   OR  user_locations.user_city IN ($user_city)
            //   OR  user_locations.user_state IN ($user_state)
            //   OR  user_education_occupations.user_occupation IN ($user_occupation)
            //   OR  user_info.user_mother_toungh IN ($user_mother_toungh)
            //   OR  user_horoscope.user_zodiacs IN ($zodiacs)
            //   OR  user_horoscope.user_nakhyatra IN ($nakshatra)
            //   OR  user_horoscope.user_gotra IN ($gotra)
            //   OR  user_education_occupations.user_employed_In  IN ($employed_In)
            //   OR  user_education_occupations.user_anual_income BETWEEN '$user_min_anual_income' AND '$user_max_anual_income')
            //   OR  user_physical_details.user_height BETWEEN '$user_min_height' AND '$user_max_height'
            //  AND
            //  ( user_info.user_gender = '$gender'  AND user_info.user_status = 'Approved' AND user_info.deleted = 1 AND user_info.status = 1 AND user_info.user_id NOT IN ($outputString)  AND user_info.marriage_status = 0 AND user_info.user_all_table_complited = 1);";
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
            $matchPercentage =$this->calculateMatchPercentage($user_partnerpreference, $other_user_partnerpreference);
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
                 "message"=> $e
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
            ( user_info.user_gender = '$gender'  AND user_info.user_status = 'Approved' AND user_info.deleted = 1 AND user_info.status = 1 AND user_info.user_id NOT IN ($outputString)  AND user_info.marriage_status = 0 AND user_religion.user_caste = '$user_religion' AND user_info.user_all_table_complited = 1);");

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
            $user_min_height = $user_partnerpreference->user_min_height;
            $user_max_height = $user_partnerpreference->user_max_height;
            $user_max_anual_income = $user_partnerpreference->user_max_anual_income;
            $user_min_anual_income = $user_partnerpreference->user_min_anual_income;
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
             OR  user_physical_details.user_height BETWEEN '$user_min_height' AND '$user_max_height')
            AND
            ( user_info.user_gender = '$gender'  AND user_info.user_status = 'Approved' AND user_info.deleted = 1 AND user_info.status = 1 AND user_info.user_id NOT IN ($outputString)  AND user_info.marriage_status = 0 AND user_info.user_membership_plan_type <> '$membership_plan_type' AND user_info.user_all_table_complited = 1);");

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
             AND user_info.user_id <> '$user_id')");

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
}
