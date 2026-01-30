<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;

final class CollectionsTest extends TestCase
{
    public function testCanCreateCollection(): void
    {
        $utils = new Utils();
        $userHasCollectionRightName = uniqid("userwithcollectionright");
        $utils->createAPIUser($userHasCollectionRightName);
        $userWithoutRights = uniqid("userwithoutrights");
        $utils->createAPIUser($userWithoutRights);
        //TODO give him CREATE_COLLECTION right
        $createCollectionRight = ["createCollection" => true];

        $utils->adminAddRightsToUserAPI($userHasCollectionRightName, $createCollectionRight);
        $collectionName = uniqid("newcollection");
        $collectionDefaultVisibility = Utils::collection($collectionName, ['default']);

        $collectionName = uniqid("newcollectionnovisibility");
        $collectionNoVisibility = Utils::collection($collectionName, []);
        $utils->createCollectionAPI($userHasCollectionRightName, $collectionNoVisibility);

        $response = Utils::httpPost("http://" . $userHasCollectionRightName . ":dummy@localhost:5252/collections", json_encode($collectionDefaultVisibility));
        $decoded = json_decode($response);
        $this->assertSame($decoded->ErrorMessage, "cleanJSON - You are not allowed to set the visibility of the default group", $response);

        $response = Utils::httpPost("http://" . $userWithoutRights . ":dummy@localhost:5252/collections", json_encode($collectionNoVisibility));
        $decoded = json_decode($response);
        $this->assertSame($decoded->ErrorMessage, "createCollection - Forbidden", $response);
    }
}
