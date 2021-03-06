<?php

namespace Rhubarb\Patterns\Tests\Mvp\Crud;

use Rhubarb\Crown\Request\WebRequest;
use Rhubarb\Crown\String\StringTools;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Leaf\Tests\Fixtures\Presenters\Cruds\CrudsAddPresenter;
use Rhubarb\Leaf\Tests\Fixtures\Presenters\Cruds\CrudsCollectionPresenter;
use Rhubarb\Leaf\Tests\Fixtures\Presenters\Cruds\CrudsDetailsPresenter;
use Rhubarb\Leaf\Tests\Fixtures\Presenters\Cruds\CrudsItemPresenter;
use Rhubarb\Leaf\Tests\Fixtures\Presenters\Cruds2\Cruds2EditPresenter;
use Rhubarb\Leaf\Tests\Fixtures\Presenters\Cruds2\Cruds2ItemPresenter;
use Rhubarb\Patterns\Mvp\Crud\CrudUrlHandler;
use Rhubarb\Stem\Tests\unit\Fixtures\Company;
use Rhubarb\Stem\Tests\unit\Fixtures\Example;
use Rhubarb\Stem\Tests\unit\Fixtures\User;

class CrudUrlHandlerTest extends RhubarbTestCase
{
    public function testCrudHandlerUsesFolderName()
    {
        $crud = new UnitTestCrudUrlHandlerTest(User::class, StringTools::getNamespaceFromClass(CrudsCollectionPresenter::class));

        $this->assertEquals(CrudsCollectionPresenter::class, $crud->GetCollectionPresenterClassName());
        $this->assertEquals(CrudsItemPresenter::class, $crud->GetItemPresenterClassName());
    }

    public function testCrudHandlerHandlesActions()
    {
        $crud = new CrudUrlHandler(User::class, StringTools::getNamespaceFromClass(CrudsCollectionPresenter::class));

        $crud->SetUrl("/users/");

        $request = new WebRequest();
        $request->urlPath = "/users/details/";
        $request->server("HTTP_ACCEPT", "text/html");
        $request->server("REQUEST_METHOD", "get");

        $response = $crud->GenerateResponse($request);
        $this->assertInstanceOf(CrudsDetailsPresenter::class, $response->GetGenerator());
    }

    public function testCrudHandlerHandlesAddAction()
    {
        $crud = new CrudUrlHandler(User::class, StringTools::getNamespaceFromClass(CrudsCollectionPresenter::class));

        $crud->SetUrl("/users/");

        $request = new WebRequest();
        $request->urlPath = "/users/add/";
        $request->server("HTTP_ACCEPT", "text/html");
        $request->server("REQUEST_METHOD", "get");

        $response = $crud->GenerateResponse($request);

        $this->assertInstanceOf(CrudsAddPresenter::class, $response->GetGenerator());
        $this->assertTrue($response->GetGenerator()->getRestModel()->IsNewRecord());
    }

    public function testNewModelHasRelationshipFieldsPopulated()
    {
        $company = new Company();
        $company->CompanyName = "GCD";
        $company->Save();

        $crudsNamespace = StringTools::getNamespaceFromClass(CrudsCollectionPresenter::class);
        $contactsHandler = new CrudUrlHandler(Example::class, $crudsNamespace);

        $usersHandler = new CrudUrlHandler(
            User::class,
            $crudsNamespace,
            [],
            [
                "contacts/" => $contactsHandler
            ]);

        $companyHandler = new CrudUrlHandler(
            Company::class,
            $crudsNamespace,
            [],
            [
                "users/" => $usersHandler
            ]);

        $companyHandler->SetUrl("/companies/");

        $request = new WebRequest();
        $request->server("HTTP_ACCEPT", "text/html");
        $request->server("REQUEST_METHOD", "get");
        $request->urlPath = "/companies/" . $company->CompanyID . "/users/add/";

        // Make sure the request is parsed.
        $companyHandler->GenerateResponse($request);

        $model = $usersHandler->getModelObject();
        $model->Save();

        $this->assertInstanceOf(User::class, $model);
        $this->assertEquals($company->UniqueIdentifier, $model[$company->UniqueIdentifierColumnName]);

        // This is a crazy test coming up. There is no relationship between user and contact objects however we currently
        // should get the user id being set on a contact object in the following scenario. A future improvement will
        // actually get new model relationship population working by actually using the relationship details in the
        // schema instead of assuming unique identifiers.

        $request->urlPath = "/companies/" . $company->CompanyID . "/users/" . $model->UniqueIdentifier . "/contacts/add/";

        // Make sure the request is parsed.
        $companyHandler->GenerateResponse($request);

        $contact = $contactsHandler->getModelObject();

        $this->assertInstanceOf(Example::class, $contact);
        $this->assertEquals($company->UniqueIdentifier, $contact[$company->UniqueIdentifierColumnName]);
        $this->assertEquals($model->UniqueIdentifier, $contact[$model->UniqueIdentifierColumnName]);
    }

    public function testAddUrlGetsItemPresenterIfNotAddPresenter()
    {
        $crud = new CrudUrlHandler(
            User::class,
            StringTools::getNamespaceFromClass(Cruds2ItemPresenter::class)
        );

        $crud->SetUrl("/users/");

        $request = new WebRequest();
        $request->urlPath = "/users/add/";
        $request->server("HTTP_ACCEPT", "text/html");
        $request->server("REQUEST_METHOD", "get");

        $response = $crud->GenerateResponse($request);

        $this->assertInstanceOf(Cruds2ItemPresenter::class, $response->GetGenerator());
        $this->assertTrue($response->GetGenerator()->GetRestModel()->IsNewRecord());
    }

    public function testUrlWithBothIDAndActionGetsRelevantPresenter()
    {
        $crud = new CrudUrlHandler(
            User::class,
            StringTools::getNamespaceFromClass(Cruds2ItemPresenter::class)
        );

        $crud->SetUrl("/users/");

        $user = new User();
        $user->Forename = "Goat";
        $user->Save();

        $request = new WebRequest();
        $request->urlPath = "/users/" . $user->UniqueIdentifier . "/edit/";
        $request->server("HTTP_ACCEPT", "text/html");
        $request->server("REQUEST_METHOD", "get");

        $response = $crud->GenerateResponse($request);

        $this->assertInstanceOf(Cruds2EditPresenter::class, $response->GetGenerator());
        $this->assertFalse($response->GetGenerator()->GetRestModel()->IsNewRecord());
        $this->assertEquals("Goat", $response->GetContent());

        $request = new WebRequest();
        $request->urlPath = "/users/" . $user->UniqueIdentifier . "/";
        $request->server("HTTP_ACCEPT", "text/html");
        $request->server("REQUEST_METHOD", "get");

        $response = $crud->GenerateResponse($request);

        $this->assertInstanceOf(Cruds2ItemPresenter::class, $response->GetGenerator());
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