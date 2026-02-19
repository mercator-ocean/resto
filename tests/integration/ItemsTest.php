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


        $itemDefaultVisibility = Utils::item(uniqid("newitem"), ['default']);

        $itemNoVisibility = Utils::item(uniqid("newitemnovisibility"), []);
        // $utils->createItemAPI($userHasItemRight, $collectionName, $itemNoVisibility);

        $response = Utils::httpPost("http://" . $userHasItemRight . ":dummy@localhost:5252/collections/" . $collectionName . "/items", json_encode($itemDefaultVisibility));
        $decoded = json_decode($response);
        //TODO why is this a success when the visibility is set to default?
        //clean json ? check if visibility is deleted, change verify and return error 
        $this->assertSame($decoded->ErrorMessage, "You are not allowed to set the visibility of the default group", $response);

        $response = Utils::httpPost("http://" . $userWithoutRights . ":dummy@localhost:5252/collections/" . $collectionName . "/items", json_encode($itemNoVisibility));
        $decoded = json_decode($response); 
        $this->assertSame($decoded->ErrorCode, 404, $response);

        //add item to catalog
        $catalogName = uniqid("newcatalog");
        $catalogNoVisibility = Utils::catalog($catalogName, []);
        $utils->createCatalogAPI($userHasItemRight, $catalogNoVisibility);

        $response = Utils::httpPost("http://" . $userHasItemRight . ":dummy@localhost:5252/catalogs/projects/" . $catalogName . "/items", json_encode($itemNoVisibility));
        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);



    }
}
