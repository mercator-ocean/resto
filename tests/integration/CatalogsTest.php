<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;

final class CatalogsTest extends TestCase
{
    public function testCanCreateCatalog(): void
    {
        //Create  catalog with group right
        $utils = new Utils();
        $userHasCatalogRight = uniqid("userwithcatalogright");
        $utils->createAPIUser($userHasCatalogRight);
        $userWithoutRights = uniqid("userwithoutrights");
        $utils->createAPIUser($userWithoutRights);
        $createCatalogRight = ["createCatalog" => true];

        $utils->adminAddRightsToUserAPI($userHasCatalogRight, $createCatalogRight);
        $catalogDefaultVisibility = Utils::catalog(uniqid("newcatalog"), ['default']);

        $catalogNoVisibility = Utils::catalog(uniqid("newcatalognovisibility"), []);
        // unset($catalogNoVisibility['visibility']);
        $utils->createCatalogAPI($userHasCatalogRight, $catalogNoVisibility);

        $response = Utils::httpPost("http://" . $userHasCatalogRight . ":dummy@localhost:5252/catalogs", json_encode($catalogDefaultVisibility));
        $decoded = json_decode($response);
        $this->assertSame($decoded->ErrorCode, 403, $response);

        $response = Utils::httpPost("http://" . $userWithoutRights . ":dummy@localhost:5252/catalogs", json_encode($catalogNoVisibility));
        $decoded = json_decode($response);
        $this->assertSame($decoded->ErrorCode, 403, $response);
 
        //Catalog test
        //check creation outiside of 'projects' and 'users' catalogs
        //catalog creation creates collection even without collection right?! type=collection...
    }

    public function testCanUpdateCatalog(): void
    {
        $utils = new Utils();
        $userHasCatalogRight = uniqid("userwithcatalogright");
        $utils->createAPIUser($userHasCatalogRight);
        $userWithoutRights = uniqid("userwithoutrights");
        $utils->createAPIUser($userWithoutRights);
        $createCatalogRight = ["createCatalog" => true];

        $utils->adminAddRightsToUserAPI($userHasCatalogRight, $createCatalogRight);
        $catalogNoVisibility = Utils::catalog(uniqid("newcatalognovisibility"), []);
        $utils->createCatalogAPI($userHasCatalogRight, $catalogNoVisibility);

        $catalogNoVisibility['description'] = "updated description";
        $catalogNoVisibility['title'] = uniqid('new title');

        $response = Utils::httpPut("http://" . $userHasCatalogRight . ":dummy@localhost:5252/catalogs/projects/" . $catalogNoVisibility['id'], json_encode($catalogNoVisibility));
        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);

        $response = Utils::httpGet("http://" . $userHasCatalogRight . ":dummy@localhost:5252/catalogs/projects/" . $catalogNoVisibility['id']);
        $decoded = json_decode($response);
        $this->assertSame($decoded->description, $catalogNoVisibility['description'], $response);
        $this->assertSame($decoded->title, $catalogNoVisibility['title'], $response);

        $catalogNoVisibility['description'] = "unauthorized updated description";
        $catalogNoVisibility['title'] = uniqid('unauthorized new title');

        $response = Utils::httpPut("http://" . $userWithoutRights . ":dummy@localhost:5252/catalogs/projects/" . $catalogNoVisibility['id'], json_encode($catalogNoVisibility));
        $decoded = json_decode($response);
        //TODO: shouldn't this be a 404 because the user without rights shouldn't even see the catalog?
        $this->assertSame($decoded->ErrorMessage, "updateCatalog - Insufficient rights to update a catalog", $response);
    }

    public function testCanDeleteCatalog(): void {
        $utils = new Utils();
        $userHasCatalogRight = uniqid("userwithcatalogright");
        $utils->createAPIUser($userHasCatalogRight);
        $userWithoutRights = uniqid("userwithoutrights");
        $utils->createAPIUser($userWithoutRights);
        $createCatalogRight = ["createCatalog" => true];

        $utils->adminAddRightsToUserAPI($userHasCatalogRight, $createCatalogRight);
        $catalogNoVisibility = Utils::catalog(uniqid("newcatalognovisibility"), []);
        $utils->createCatalogAPI($userHasCatalogRight, $catalogNoVisibility);

        $response = Utils::httpDelete("http://" . $userWithoutRights . ":dummy@localhost:5252/catalogs/projects/" . $catalogNoVisibility['id']);
        $decoded = json_decode($response); 
        //TODO: shouldn't this be a 404 because the user without rights shouldn't even see the catalog?
        $this->assertSame($decoded->ErrorCode, 403, $response);

        $response = Utils::httpDelete("http://" . $userHasCatalogRight . ":dummy@localhost:5252/catalogs/projects/" . $catalogNoVisibility['id']);
        $decoded = json_decode($response);
        $this->assertSame($decoded->status, "success", $response);
    }
}
