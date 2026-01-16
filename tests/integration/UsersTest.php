<?php

declare(strict_types=1);

namespace test;

use PHPUnit\Framework\TestCase;

function httpPost($url, $data)
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

function httpPut($url, $data)
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

function httpGet($url)
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


function user($username, $email)
{
    return array(
        "username" => $username,
        "firstname" => "John",
        "lastname" => "Doe",
        "email" => $email,
        "password" => "dummy"
    );
}

final class UsersTest extends TestCase
{
    public function testCanCreateUser(): void
    {
        $response = httpPost("http://localhost:5252/users", json_encode(user(uniqid("newuser"), uniqid("newUser") . "@toto.fr")));

        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);
    }

    public function testCanUpdateUser(): void
    {
        $userName = uniqid("newuser");
        $response = httpPost("http://localhost:5252/users", json_encode(user($userName, uniqid("newUser") . "@toto.fr")));

        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);
        $profile = array("bio" => "This is John Doe biography - pretty empty");
        $response = httpPut("http://" . $userName . ":" . "dummy@localhost:5252/users/" . $userName, json_encode($profile));

        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);

        $response = httpGet("http://" . $userName . ":" . "dummy@localhost:5252/users/" . $userName);

        $decoded = json_decode($response);
        $this->assertSame($decoded->bio, $profile['bio'], $response);    }

}
