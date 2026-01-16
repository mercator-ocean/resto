<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


final class UsersTest extends TestCase
{
    public function testCanCreateUser(): void
    {
        $response = Utils::httpPost("http://localhost:5252/users", json_encode(Utils::user(uniqid("newuser"), uniqid("newUser") . "@toto.fr")));

        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);
    }

    public function testCanUpdateUser(): void
    {
        $userName = uniqid("newuser");
        $response = Utils::httpPost("http://localhost:5252/users", json_encode(Utils::user($userName, uniqid("newUser") . "@toto.fr")));

        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);
        $profile = array("bio" => "This is John Doe biography - pretty empty");
        $response = Utils::httpPut("http://" . $userName . ":" . "dummy@localhost:5252/users/" . $userName, json_encode($profile));

        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);

        $response = Utils::httpGet("http://" . $userName . ":" . "dummy@localhost:5252/users/" . $userName);

        $decoded = json_decode($response);
        $this->assertSame($decoded->bio, $profile['bio'], $response);    
    }

}
