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
        $utils = new Utils();

        $groupOwnerUserName = uniqid("groupOwner");
        $utils->createAPIUser($groupOwnerUserName);

        $inGroupUserName = uniqid("userInGroup");
        $utils->createAPIUser($inGroupUserName);

        $randomUserName = uniqid("leQuentin");
        $utils->createAPIUser($randomUserName);

        $groupName = uniqid("itemCreationGroup");
        $utils->createAPIGroup($groupOwnerUserName, $groupName);

        $utils->addUserToGroupAPI($groupOwnerUserName, $groupName, $inGroupUserName);

        $goodRight = array(RestoGroup::createItemRight($groupName) => true);
        $response = Utils::httpPost("http://" . $groupOwnerUserName . ":dummy@localhost:5252/groups/" . $groupName . "/rights", json_encode($goodRight));
        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);

        $collectionName =  uniqid("collection");
        $collection=Utils::collection($collectionName,array($groupName));
        $utils->createCollectionAPI($groupOwnerUserName, $collection);


        //user1 create collection ->  si il cree un colection dans son group visible  de son group
        //user2 create item in group collection
        //user2 cannot create collection

        //user3 cannot see group collection
        //user3 cannot create item in group collection
    }
}
