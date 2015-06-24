<?php

namespace Rhubarb\Patterns\Tests\Mvp\BoilerPlates\Login;

use Rhubarb\Crown\Tests\RhubarbTestCase;
use Rhubarb\Leaf\Tests\Fixtures\Presenters\UnitTestView;
use Rhubarb\Patterns\Mvp\BoilerPlates\Login\LoginPresenter;
use Rhubarb\Stem\Tests\Fixtures\TestLoginProvider;
use Rhubarb\Stem\Tests\Fixtures\User;

RhubarbTestCase::setUpBeforeClass();

class LoginPresenterTest extends RhubarbTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $user = new User();
        $user->Username = "acuthbert";
        $user->Password = '$6$rounds=10000$saltyfish$xsdN77OODY/XmxLdlkFW9CNxuE4H6NjEGG7K7tGJbzHUyDrVDHROL/FqG.ANet3dcd6WqGOOvaDjLv/WeAtcK0';
        $user->Active = 1;
        $user->Save();

        $user = new User();
        $user->Username = "joesmith";
        $user->Password = '$6$rounds=10000$saltyfish$xsdN77OODY/XmxLdlkFW9CNxuE4H6NjEGG7K7tGJbzHUyDrVDHROL/FqG.ANet3dcd6WqGOOvaDjLv/WeAtcK0';
        $user->Active = 0;
        $user->Save();

        $testLoginProvider = new TestLoginProvider();
        $testLoginProvider->LogOut();
    }

    private $presenter;
    private $view;

    protected function setUp()
    {
        parent::setUp();

        $this->presenter = new LoginPresenterUnitTest();
        $this->view = new UnitTestView();

        $this->presenter->AttachMockView($this->view);
        $this->presenter->Initialise();
    }


    public function testLoginCanFail()
    {
        $this->presenter->Username = "billy";
        $this->presenter->Password = "bob";

        $this->view->SimulateEvent("AttemptLogin");

        $this->presenter->GenerateResponse();

        $this->assertTrue($this->view->failed);
    }

    public function testDisabledLoginCanFail()
    {
        $this->presenter->Username = "joesmith";
        $this->presenter->Password = "abc123";

        $this->view->SimulateEvent("AttemptLogin");
        $this->presenter->GenerateResponse();

        $this->assertTrue($this->view->failed);
        $this->assertTrue($this->view->disabled);
    }

    public function testLoginCanBeSuccessful()
    {
        $this->presenter->Username = "acuthbert";
        $this->presenter->Password = "abc123";
        $this->view->SimulateEvent("AttemptLogin");
        $this->presenter->GenerateResponse();

        $this->assertTrue($this->presenter->loggedIn);

        $testLoginProvider = new TestLoginProvider();
        $this->assertTrue($testLoginProvider->IsLoggedIn());
    }
}

class LoginPresenterUnitTest extends LoginPresenter
{
    public $loggedIn = false;

    public function __construct($name = "")
    {
        parent::__construct(TestLoginProvider::class, "Username", $name);
    }

    protected function onSuccess()
    {
        $this->loggedIn = true;
    }
}