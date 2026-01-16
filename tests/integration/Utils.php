<?php

declare(strict_types=1);


class Utils
{
    public static function httpPost($url, $data)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function httpPut($url, $data)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    public static function httpGet($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }


    public static function user($username, $email)
    {
        return array(
            "username" => $username,
            "firstname" => "John",
            "lastname" => "Doe",
            "email" => $email,
            "password" => "dummy"
        );
    }
    public static function group($groupName)
    {
        return array(
            "name" => $groupName,
            "description" => "Any user can create a group."
        );
    }

}