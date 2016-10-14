<?php

namespace Rhubarb\Patterns\Mvp\Crud\ModelForm;

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Leaf\Tests\Fixtures\Presenters\UnitTestView;
use Rhubarb\Stem\Models\Validation\HasValue;
use Rhubarb\Stem\Models\Validation\Validator;
use Rhubarb\Stem\Tests\unit\Fixtures\User;

class ModelFormPresenterTest extends RhubarbTestCase
{
    /**
     * @var ModelFormPresenter
     */
    private $presenter;

    /**
     * @var UnitTestView
     */
    private $view;

    protected function setUp()
    {
        parent::setUp();

        $this->presenter = new TestModelPresenter();
        $this->view = new MockModelPresenterView();

        $this->presenter->attachMockView($this->view);
    }

    public function testSaveButton()
    {
        $user = new User();

        $user->Username = "abc";

        // This next line is just to make sure validation doesn't fail. Validation is tested below
        // and operates on the presenter model, not the rest model.
        $this->presenter->Username = "abc";

        $this->presenter->setRestModel($user);

        try {
            $this->view->simulateEvent("SavePressed");

            $this->fail("SavePressed should have thrown a ForceResponseException to redirect us");
        } catch (ForceResponseException $ex) {
        }

        $this->assertFalse($user->isNewRecord());
    }

    public function testCancelButton()
    {
        $user = new User();
        $user->Username = "abc";

        // This next line is just to make sure validation doesn't fail. Validation is tested below
        // and operates on the presenter model, not the rest model.
        $this->presenter->Username = "abc";

        $this->presenter->setRestModel($user);

        try {
            $this->view->simulateEvent("CancelPressed");

            $this->fail("SavePressed should have thrown a ForceResponseException to redirect us");
        } catch (ForceResponseException $ex) {
        }

        $this->assertTrue($user->isNewRecord());
    }

    public function testInvalidModelDoesntSave()
    {
        $user = new User();
        $user->Username = "";

        $this->presenter->setRestModel($user);

        $this->view->simulateEvent("SavePressed");

        $this->assertTrue($user->isNewRecord(), "This test should have failed validation so the user should not be saved.");
    }
}

class TestModelPresenter extends ModelFormPresenter
{
    protected function createDefaultValidator()
    {
        $validator = new Validator();
        $validator->validations[] = new HasValue("Username");

        return $validator;
    }
}

class MockModelPresenterView extends UnitTestView
{

}
