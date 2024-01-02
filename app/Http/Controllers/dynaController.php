<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class dynaController extends Controller
{
    public function dynaQuay()
    {
        $data = cryptoJsAesDecrypt(file_get_contents("php://input"));
        // $data=file_get_contents("php://input");
        if ($data !== null) {
            $query = $data['query'];
            $match = array("CREATE", "DROP", "UPDATE", "DELETE", 'INSERT', 'TRUNCATE', 'ALTER');
            $pattern = '/\b(' . implode('|', $match) . ')\b/i';
            $found = preg_match($pattern, $query);
            if ($found) {
                $user_arr = array(
                    "status" => false,
                    "success" => false,
                    "message" => "Unauthorized Access",
                );
            } else {
                try {
                    $dataoftable = DB::select($query);
                    if (count($dataoftable) > 0) {
                        $user_arr = array(
                            "status" => true,
                            "success" => true,
                            "data" => $dataoftable,
                        );
                    } else {
                        $user_arr = array(
                            "status" => true,
                            "success" => true,
                            "data" => [],
                        );
                    }
                } catch (\Exception $e) {
                    $user_arr = array(
                        "status" => false,
                        "success" => false,
                        "data" => [],
                        "message" => (object)$e,
                    );
                }
            }
        } else {
            $user_arr = array(
                "status" => false,
                "success" => false,
                "message" => "Unauthorized Access",
            );
        }
        // return json_encode($user_arr);
        return cryptoJsAesEncrypt($user_arr);
    }
}


function cryptoJsAesDecrypt($jsonString)
{
    $passphrase = '1E99412323A4ED2WAYWALASECRET_KEY';
    $jsondata = json_decode($jsonString, true);
    try {
        $salt = hex2bin($jsondata["s"]);
        $iv  = hex2bin($jsondata["iv"]);
    } catch (Exception $e) {
        return null;
    }
    $ct = base64_decode($jsondata["ct"]);
    $concatedPassphrase = $passphrase . $salt;
    $md5 = array();
    $md5[0] = md5($concatedPassphrase, true);
    $result = $md5[0];
    for ($i = 1; $i < 3; $i++) {
        $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
        $result .= $md5[$i];
    }
    $key = substr($result, 0, 32);
    $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    return json_decode($data, true);
}
/**
 * Encrypt value to a cryptojs compatiable json encoding string
 *
 * @param mixed $passphrase
 * @param mixed $value
 * @return string
 */
function cryptoJsAesEncrypt($value)
{
    $passphrase = '1E99412323A4ED2WAYWALASECRET_KEY';
    $salt = openssl_random_pseudo_bytes(8);
    $salted = '';
    $dx = '';
    while (strlen($salted) < 48) {
        $dx = md5($dx . $passphrase . $salt, true);
        $salted .= $dx;
    }
    $key = substr($salted, 0, 32);
    $iv  = substr($salted, 32, 16);
    $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
    $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
    return json_encode($data);
}
