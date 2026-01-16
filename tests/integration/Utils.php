<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class Utils extends TestCase
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


    public function createAPIUser($userName)
    {
        $response = Utils::httpPost("http://localhost:5252/users", json_encode(Utils::user($userName, $userName . "@toto.fr")));
        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);
    }

    public function createAPIGroup($userName, $groupName)
    {
        $response = Utils::httpPost("http://" . $userName . ":dummy@localhost:5252/groups", json_encode(Utils::group($groupName)));
        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);
    }

    public function addUserToGroupAPI($ownerName, $groupName, $userName)
    {
        $response = Utils::httpPost("http://" . $ownerName . ":dummy@localhost:5252/groups/" . $groupName . "/users", json_encode(array("username" => $userName)));
        // $decoded = json_decode($response);
        // $this->assertSame($decoded->status, "success", $response);
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
