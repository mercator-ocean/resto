<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;

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

        $unauthorizedRight = ["createCollection" => true];
        $response = Utils::httpPost("http://" . $userName . ":dummy@localhost:5252/groups/" . $groupName . "/rights", json_encode($unauthorizedRight));
        $decoded = json_decode($response);
        $this->assertSame($decoded->ErrorCode, 400, $response);

        $goodRight = [RestoGroup::createItemRight($groupName) => true];
        $response = Utils::httpPost("http://" . $userName . ":dummy@localhost:5252/groups/" . $groupName . "/rights", json_encode($goodRight));
        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);
    }

    #[Group('only')]
    public function testCanPlayWithGroupRightCreation(): void
    {
        $utils = new Utils();

        $groupOwnerUserName = uniqid("groupowner");
        $utils->createAPIUser($groupOwnerUserName);

        $inGroupUserName = uniqid("useringroup");
        $utils->createAPIUser($inGroupUserName);

        $randomUserName = uniqid("lequentin");
        $utils->createAPIUser($randomUserName);

        $groupName = uniqid("itemCreationGroup");
        $utils->createAPIGroup($groupOwnerUserName, $groupName);

        $utils->addUserToGroupAPI($groupOwnerUserName, $groupName, $inGroupUserName);

        $itemRight = [
            RestoGroup::createItemRight($groupName) => true,
        ];
        $collectionRight = [
            RestoGroup::createCollectionRight($groupName)=>true,
        ];
        $response = Utils::httpPost("http://" . $groupOwnerUserName . ":dummy@localhost:5252/groups/" . $groupName . "/rights", json_encode($itemRight));
        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);

        $response = Utils::httpPost("http://admin:admin@localhost:5252/users/" . $groupOwnerUserName . "/rights", json_encode($collectionRight));
        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);

        $collectionName =  uniqid("collection");
        $collection = Utils::collection($collectionName, [$groupName]);
        $utils->createCollectionAPI($groupOwnerUserName, $collection);

        //inGroupUser is forbidden to create collection in group
        $response = Utils::httpPost("http://" . $inGroupUserName . ":dummy@localhost:5252/collections", json_encode($collection));
        $decoded = json_decode($response);
        $this->assertSame($decoded->ErrorCode, 403, $response);

        //inGroupUser can create items in collection with group visibility
        $response = $utils->createItemAPI($inGroupUserName, $collectionName, Utils::item(uniqid("item1"), []));

        //randomUser cannot see collection if not in group with visibility
        $response= Utils::httpGet("http://" . $randomUserName . ":dummy@localhost:5252/collections/" . $collectionName);
        $decoded = json_decode($response);
        $this->assertSame($decoded->ErrorCode, 404, $response);

        //randomUser cannot create items in collection with group visibility
        $response = Utils::httpPost("http://" . $randomUserName . ":dummy@localhost:5252/collections/" . $collectionName . "/items", json_encode(Utils::item(uniqid("item2"), [])));
        $decoded = json_decode($response);
        $this->assertSame($decoded->ErrorCode, 404, $response);
    }
}
//TODO update colleciton
//TODO delete collection

