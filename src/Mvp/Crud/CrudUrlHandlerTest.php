<?php

namespace Rhubarb\Patterns\Mvp\Crud;

use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\UnitTesting\CoreTestCase;
use Rhubarb\Stem\UnitTesting\Company;
use Rhubarb\Stem\UnitTesting\User;

class CrudUrlHandlerTest extends CoreTestCase
{
    public function testCrudHandlerUsesFolderName()
    {
        $crud = new UnitTestCrudUrlHandlerTest(
            "Rhubarb\Stem\UnitTesting\User",
            "Rhubarb\Leaf\UnitTesting\Presenters\Cruds");

        $this->assertEquals("Rhubarb\Leaf\UnitTesting\Presenters\Cruds\CrudsCollectionPresenter", $crud->GetCollectionPresenterClassName());
        $this->assertEquals("Rhubarb\Leaf\UnitTesting\Presenters\Cruds\CrudsItemPresenter", $crud->GetItemPresenterClassName());
    }

    public function testCrudHandlerHandlesActions()
    {
        $crud = new CrudUrlHandler(
            "Rhubarb\Stem\UnitTesting\User",
            "Rhubarb\Leaf\UnitTesting\Presenters\Cruds");

        $crud->SetUrl("/users/");

        $request = new WebRequest();
        $request->UrlPath = "/users/details/";
        $request->Server("HTTP_ACCEPT", "text/html");
        $request->Server("REQUEST_METHOD", "get");

        $response = $crud->GenerateResponse($request);
        $this->assertInstanceOf("Rhubarb\Leaf\UnitTesting\Presenters\Cruds\CrudsDetailsPresenter", $response->GetGenerator());
    }

    public function testCrudHandlerHandlesAddAction()
    {
        $crud = new CrudUrlHandler(
            "Rhubarb\Stem\UnitTesting\User",
            "Rhubarb\Leaf\UnitTesting\Presenters\Cruds"
        );

        $crud->SetUrl("/users/");

        $request = new WebRequest();
        $request->UrlPath = "/users/add/";
        $request->Server("HTTP_ACCEPT", "text/html");
        $request->Server("REQUEST_METHOD", "get");

        $response = $crud->GenerateResponse($request);

        $this->assertInstanceOf("Rhubarb\Leaf\UnitTesting\Presenters\Cruds\CrudsAddPresenter", $response->GetGenerator());
        $this->assertTrue($response->GetGenerator()->GetRestModel()->IsNewRecord());
    }

    public function testNewModelHasRelationshipFieldsPopulated()
    {
        $company = new Company();
        $company->CompanyName = "GCD";
        $company->Save();

        $contactsHandler = new CrudUrlHandler(
            "Rhubarb\Stem\UnitTesting\Example",
            "Rhubarb\Leaf\UnitTesting\Presenters\Cruds"
        );

        $usersHandler = new CrudUrlHandler(
            "Rhubarb\Stem\UnitTesting\User",
            "Rhubarb\Leaf\UnitTesting\Presenters\Cruds",
            [],
            [
                "contacts/" => $contactsHandler
            ]);

        $companyHandler = new CrudUrlHandler(
            "Rhubarb\Stem\UnitTesting\Company",
            "Rhubarb\Leaf\UnitTesting\Presenters\Cruds",
            [],
            [
                "users/" => $usersHandler
            ]);

        $companyHandler->SetUrl("/companies/");

        $request = new WebRequest();
        $request->Server("HTTP_ACCEPT", "text/html");
        $request->Server("REQUEST_METHOD", "get");
        $request->UrlPath = "/companies/" . $company->CompanyID . "/users/add/";

        // Make sure the request is parsed.
        $companyHandler->GenerateResponse($request);

        $model = $usersHandler->getModelObject();
        $model->Save();

        $this->assertInstanceOf("Rhubarb\Stem\UnitTesting\User", $model);
        $this->assertEquals($company->UniqueIdentifier, $model[$company->UniqueIdentifierColumnName]);

        // This is a crazy test coming up. There is no relationship between user and contact objects however we currently
        // should get the user id being set on a contact object in the following scenario. A future improvement will
        // actually get new model relationship population working by actually using the relationship details in the
        // schema instead of assuming unique identifiers.

        $request->UrlPath = "/companies/" . $company->CompanyID . "/users/" . $model->UniqueIdentifier . "/contacts/add/";

        // Make sure the request is parsed.
        $companyHandler->GenerateResponse($request);

        $contact = $contactsHandler->getModelObject();

        $this->assertInstanceOf("Rhubarb\Stem\UnitTesting\Example", $contact);
        $this->assertEquals($company->UniqueIdentifier, $contact[$company->UniqueIdentifierColumnName]);
        $this->assertEquals($model->UniqueIdentifier, $contact[$model->UniqueIdentifierColumnName]);
    }

    public function testAddUrlGetsItemPresenterIfNotAddPresenter()
    {
        $crud = new CrudUrlHandler(
            "Rhubarb\Stem\UnitTesting\User",
            "Rhubarb\Leaf\UnitTesting\Presenters\Cruds2"
        );

        $crud->SetUrl("/users/");

        $request = new WebRequest();
        $request->UrlPath = "/users/add/";
        $request->Server("HTTP_ACCEPT", "text/html");
        $request->Server("REQUEST_METHOD", "get");

        $response = $crud->GenerateResponse($request);

        $this->assertInstanceOf("Rhubarb\Leaf\UnitTesting\Presenters\Cruds2\Cruds2ItemPresenter", $response->GetGenerator());
        $this->assertTrue($response->GetGenerator()->GetRestModel()->IsNewRecord());
    }

    public function testUrlWithBothIDAndActionGetsRelevantPresenter()
    {
        $crud = new CrudUrlHandler(
            "Rhubarb\Stem\UnitTesting\User",
            "Rhubarb\Leaf\UnitTesting\Presenters\Cruds2"
        );

        $crud->SetUrl("/users/");

        $user = new User();
        $user->Forename = "Goat";
        $user->Save();

        $request = new WebRequest();
        $request->UrlPath = "/users/" . $user->UniqueIdentifier . "/edit/";
        $request->Server("HTTP_ACCEPT", "text/html");
        $request->Server("REQUEST_METHOD", "get");

        $response = $crud->GenerateResponse($request);

        $this->assertInstanceOf("Rhubarb\Leaf\UnitTesting\Presenters\Cruds2\Cruds2EditPresenter", $response->GetGenerator());
        $this->assertFalse($response->GetGenerator()->GetRestModel()->IsNewRecord());
        $this->assertEquals("Goat", $response->GetContent());

        $request = new WebRequest();
        $request->UrlPath = "/users/" . $user->UniqueIdentifier . "/";
        $request->Server("HTTP_ACCEPT", "text/html");
        $request->Server("REQUEST_METHOD", "get");

        $response = $crud->GenerateResponse($request);

        $this->assertInstanceOf("Rhubarb\Leaf\UnitTesting\Presenters\Cruds2\Cruds2ItemPresenter", $response->GetGenerator());
    }
}

class UnitTestCrudUrlHandlerTest extends CrudUrlHandler
{
    public function GetCollectionPresenterClassName()
    {
        return $this->collectionPresenterClassName;
    }

    public function GetItemPresenterClassName()
    {
        return $this->itemPresenterClassName;
    }
}