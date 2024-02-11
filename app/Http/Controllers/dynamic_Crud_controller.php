<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class dynamic_Crud_controller extends Controller
{
    public function fetch(Request $request)
    {
        // {
        //     "table":"country_table",
        //     "projection":["*"],
        //     "whereConditions":{
        //     "country_name", "INDIA"
        //     }
        //  }  
        // } fetch data parametr formate 

        try {
            //code...


            $requestedData = $request->all();
            $whereConditions = isset($requestedData['whereConditions']) ? $requestedData['whereConditions'] : [];
            $table = isset($requestedData['table']) ? $requestedData['table'] : '';
            $projection = isset($requestedData['projection']) ? $requestedData['projection'] : [];
            $offset = isset($requestedData['offset']) ? $requestedData['offset'] : 0;
            $limit = isset($requestedData['limit']) ? $requestedData['limit'] : 1000;
            if (empty($table)) {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => 'You Provid Empty data',
                    'data' => [],
                    'e' => $request->input()
                );
            } else {


                // Get the total count of rows

                if (count($whereConditions) == 0) {
                    $fatchdata = DB::table($table)->skip($offset)
                        ->take($limit)->get($projection);
                } else {
                    $fatchdata = DB::table($table)->where($whereConditions)->skip($offset)
                        ->take($limit)->get($projection);
                }
                // echo '<pre>';
                // print_r($fatchdata);
                // exit;

                $total_count = DB::select('SELECT COUNT(1) AS total_count FROM $table');
                // dd($query);
                // $totalRows = $query->count();
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "totalCount" => $total_count['total_count'],
                    "count" => count($fatchdata),
                    "message" => 'Total Fetch Data ' . count($fatchdata),
                    "data" => $fatchdata
                );
            }
            return json_encode($user_arr);
        } catch (\Exception $e) {
            //throw $th;
            $user_arr = array(
                "status" => true,
                "success" => true,
                "totalCount" => 0,
                "count" => 0,
                "message" => 'Total Fetch Data 0' ,
                "data" => [],
                "erroe"=>$e
            );
            return json_encode($user_arr);

        }
    }

    public function save(Request $request)
    {


        $requestedData = $request->all();

        $data =  $requestedData['data'];
        $table = isset($requestedData['table']) ? $requestedData['table'] : '';

        $whereConditions = isset($requestedData['whereConditions']) ? $requestedData['whereConditions'] : [];
        $isJsonData = isset($requestedData['isJsonData']) ? $requestedData['isJsonData'] : false;
        if (empty($data) || empty($table)) {
            $response = [
                "status" => false,
                "success" => false,
                "message" => "Invalid request data",
            ];
            return response()->json($response, 400);
        }
        try {
            // $saveQuery = DB::table($table)->insert($data);

            if ($isJsonData) {
                $existingRecord = DB::table($table)->where($whereConditions)->first();

                if ($existingRecord) {


                    $firstKey = !empty($jsonDataID) ? array_keys($jsonDataID)[0] : null;
                    $existingData = json_decode($existingRecord->json_data, true);

                    $updatedData = array_merge($existingData, $data);
                    $jsonDataID = isset($requestedData['jsonDataID']) ? $requestedData['jsonDataID'] : [];

                    // Extract the first key from $jsonDataID

                    DB::table($table)->where($whereConditions)->update(['json_data' => json_encode($updatedData)]);


                    $response = [
                        "status" => true,
                        "success" => true,
                        "message" => "Update Successful",
                    ];
                } else {

                    $jsonDataID = isset($requestedData['jsonDataID']) ? $requestedData['jsonDataID'] : [];

                    // Extract the first key from $jsonDataID
                    $firstKey = !empty($jsonDataID) ? array_keys($jsonDataID)[0] : null;
                    DB::table($table)->insert([
                        'json_data' => json_encode($data),
                        $firstKey => $jsonDataID[$firstKey] // Add the key-value pair
                    ]);

                    $response = [
                        "status" => true,
                        "success" => true,
                        "message" => "Insert Successful",
                    ];
                }
            } else {
                DB::table($table)->insert($data);
                $response = [
                    "status" => true,
                    "success" => true,
                    "message" => "Insert Successful",
                ];
            }
        } catch (\Exception $e) {
            $response = [
                "status" => false,
                "success" => false,
                "message" => "Error: " . $e->getMessage(),
            ];
        }

        return json_encode($response);
    }

    public function update(Request $request)
    {
        // {
        //     "table":"country_table",
        //     "data":[],
        //     "whereConditions":[
        //         ["country_name", "INDIA"]
        //     ]
        // } Upadte data parametr formate 
        // $requestedData = json_decode(file_get_contents("php://input"));
        $requestedData = $request->all();
        $whereConditions = isset($requestedData['whereConditions']) ? $requestedData['whereConditions'] : [];
        $table = isset($requestedData['table']) ? $requestedData['table'] : '';;
        $data = isset($requestedData['data']) ? $requestedData['data'] : '';
        if (count($whereConditions) == 0 || $table == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => 'You Provid Empty data',
            );
        } else {
            // print_r($data);
            // return;
            $updateQuery = DB::table($table)->where($whereConditions)->update(
                $data
            );
            if ($updateQuery > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => 'Update Successfully! ',
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "error" => $updateQuery,
                    "message" => 'No Data Updated',
                );
            }
        }
        return json_encode($user_arr);
    }

    public function delete(Request $request)
    {
        // {
        //     "table":"country_table",
        //     "data":[],
        //     "whereConditions":[
        //         ["country_name", "INDIA"]
        //     ]
        // } Upadte data parametr formate 
        // $data = json_decode(file_get_contents('php:://input'));
        $requestedData = $request->all();
        // $id = empty($data->id) ? '' : $data->id;
        // $table = empty($data->table) ? '' : $data->table;

        $whereConditions = isset($requestedData['whereConditions']) ? $requestedData['whereConditions'] : [];
        $table = isset($requestedData['table']) ? $requestedData['table'] : '';
        // $data = $requestedData['data'];

        if (count($whereConditions) == 0 || $table == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => 'You Provid Empty data',
            );
        } else {
            $query = DB::table($table)->where($whereConditions)->delete();
            if ($query > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => 'Data Deleted Successfully!',
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => 'Data Not Deleted Successfully!',
                );
            }
        }

        return json_encode($user_arr);
    }
    public function makeActinForMultipulData(Request $request)
    {
        $requestedData = $request->all();

        $whereConditions = isset($requestedData['whereConditions']) ? $requestedData['whereConditions'] : [];
        // return gettype($whereConditions);
        $table = isset($requestedData['table']) ? $requestedData['table'] : '';
        $type = isset($requestedData['type']) ? $requestedData['type'] : '';
        $data = $requestedData['data'];
        if (count($whereConditions) == 0 || $table == '') {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => 'You Provid Empty data',
            );
        } else {
            $query = DB::table($table)->whereIn('Id', $whereConditions)->update(
                $data
            );
            if ($query > 0) {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "message" => 'Data ' . $type . ' Successfully!',
                );
            } else {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => 'Data Not ' . $type . ' Successfully!',
                );
            }
        }
        return json_encode($user_arr);
    }
    public function unsecuredFatchquary(Request $request)
    { {
            // {
            //     "table":"country_table",
            //     "projection":["*"],
            //     "whereConditions":{
            //     "country_name", "INDIA"
            //     }
            //  }  
            // } fetch data parametr formate 

            $requestedData = $request->all();
            $whereConditions = isset($requestedData['whereConditions']) ? $requestedData['whereConditions'] : [];
            $table = isset($requestedData['table']) ? $requestedData['table'] : '';
            $projection = isset($requestedData['projection']) ? $requestedData['projection'] : [];
            $selectedtavle = array('mother_tongue', 'religion', 'cast_table', 'sub_cast', 'country');
            if (in_array($table, $selectedtavle)) {
                if (empty($table)) {
                    $user_arr = array(
                        "status" => false,
                        "success" => false,
                        "message" => 'You Provid Empty data',
                        'data' => [],
                        'e' => $request->input()
                    );
                } else {
                    // Get the total count of rows
                    if (count($whereConditions) == 0) {
                        $fatchdata = DB::table($table)
                            ->get($projection);
                    } else {
                        $fatchdata = DB::table($table)->where($whereConditions)
                            ->get($projection);
                    }
                    $user_arr = array(
                        "status" => true,
                        "success" => true,
                        "data" => $fatchdata
                    );
                }
            } else {
                $user_arr = array(
                    "status" => true,
                    "success" => true,
                    "data" => 'Do not act SMART'
                );
            }

            return json_encode($user_arr);
        }
    }
}
