<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class GroupsTest extends TestCase
{
    public function testCanUpdateGroupRights(): void
    {
        $userName = uniqid("newuser");
        $response = Utils::httpPost("http://localhost:5252/users", json_encode(Utils::user($userName, uniqid("newUser") . "@toto.fr")));
        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);

        $groupName = uniqid("newGroup");
        $response = Utils::httpPost("http://" . $userName . ":dummy@localhost:5252/groups", json_encode(Utils::group($groupName)));
        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);

        $unauthorizedRight = array("createCollection" => true);
        $response = Utils::httpPost("http://" . $userName . ":dummy@localhost:5252/groups/" . $groupName . "/rights", json_encode($unauthorizedRight));
        $decoded = json_decode($response);
        $this->assertSame($decoded->ErrorCode, 400, $response);

        $goodRight = array(RestoGroup::createItemRight($groupName) => true);
        $response = Utils::httpPost("http://" . $userName . ":dummy@localhost:5252/groups/" . $groupName . "/rights", json_encode($goodRight));
        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);
    }

    public function testCanToto()
    {
        $utils = new Utils("");

        $groupOwnerUserName = uniqid("groupOwner");
        $utils->createAPIUser($groupOwnerUserName);

        $inGroupUserName = uniqid("userInGroup");
        $utils->createAPIUser($inGroupUserName);

        $randomUserName = uniqid("leQuentin");
        $utils->createAPIUser($randomUserName);

        $groupName = uniqid("itemCreationGroup");
        $utils->createAPIGroup($groupOwnerUserName, $groupName);

        $utils->addUserToGroupAPI($groupOwnerUserName, $groupName, $inGroupUserName);

        //user2 added to group
        //user3 not in group

        //user1 add group right to create items
        //user1 create collection
        //user2 create item in group collection

        //user3 cannot see group collection
        //user3 cannot create item in group collection
    }
}
