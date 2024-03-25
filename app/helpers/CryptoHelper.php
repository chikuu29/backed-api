<?php

namespace App\Helpers;
use Exception;
class CryptoHelper
{
    /**
     * Decrypt value using CryptoJS AES encryption
     *
     * @param string $jsonString Encrypted data in JSON format
     * @return mixed|null Decrypted value or null if decryption fails
     */
    public static function cryptoJsAesDecrypt($jsonString)
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
     * Encrypt value to a CryptoJS compatible JSON encoding string
     *
     * @param mixed $value Value to encrypt
     * @return string Encrypted data in JSON format
     */
    public static function cryptoJsAesEncrypt($value)
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
}
