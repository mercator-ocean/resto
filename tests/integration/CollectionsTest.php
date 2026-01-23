<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;

final class CollectionsTest extends TestCase
{
    #[Group('only')]
    public function testCanCreateCollection(): void
    {

        $utils = new Utils();
        $userHasCollectionRightName = uniqid("userwithcollectionright");
        $utils->createAPIUser($userHasCollectionRightName);
        //TODO give him CREATE_COLLECTION right
        $createCollectionRight = array("createCollection" => true);

        $utils->adminAddRightsToUserAPI($userHasCollectionRightName, $createCollectionRight);
        $collectionName = uniqid("newcollection");

        $collectionForbiddenVisibility = Utils::collection($collectionName, 'default');
    }
}
