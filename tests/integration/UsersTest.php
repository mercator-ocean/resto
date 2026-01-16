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
        $response = httpPost("http://localhost:5252/users", json_encode(user("newuser2", "newUser2@toto.fr")));

        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);
    }
}
