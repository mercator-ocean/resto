<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;

final class ItemsTest extends TestCase
{
    #[Group('only')]
    public function testCanCreateItem(): void
    {
        $utils = new Utils();
        $userHasItemRight = uniqid("userwithitemright");
        $utils->createAPIUser($userHasItemRight);
        $userWithoutRights = uniqid("userwithoutrights");
        $utils->createAPIUser($userWithoutRights);
        $createItemRight = ["createItem" => true, "createCollection" => true, "createCatalog" => true];
        $utils->adminAddRightsToUserAPI($userHasItemRight, $createItemRight);

        $collectionName = uniqid("newcollection");
        $collectionNoVisibility = Utils::collection($collectionName, []);
        $utils->createCollectionAPI($userHasItemRight, $collectionNoVisibility);

        $catalogName = uniqid("newcatalog");
        $catalogNoVisibility = Utils::catalog($catalogName, []);
        $utils->createCatalogAPI($userHasItemRight, $catalogNoVisibility);

        $itemDefaultVisibility = Utils::item(uniqid("newitem"), ['default']);

        $itemNoVisibility = Utils::item(uniqid("newitemnovisibility"), []);
        $utils->createItemAPI($userHasItemRight, $collectionName, $itemNoVisibility);

        $response = Utils::httpPost("http://" . $userHasItemRight . ":dummy@localhost:5252/collections/" . $collectionName . "/items", json_encode($itemDefaultVisibility));
        $decoded = json_decode($response);
        //TODO why is this a success when the visibility is set to default?
        $this->assertSame($decoded->status, "success", $response);

        $response = Utils::httpPost("http://" . $userWithoutRights . ":dummy@localhost:5252/collections/" . $collectionName . "/items", json_encode($itemNoVisibility));
        $decoded = json_decode($response);
        $this->assertSame($decoded->ErrorCode, 404, $response);

        //TODO
        //add item to catalog

        //check creation in non existing collection
        //check creation with non existing catalog in collection path
    }
}
