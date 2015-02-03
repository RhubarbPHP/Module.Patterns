<?php

namespace Rhubarb\Patterns\Mvp\Crud\ModelForm;

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Stem\Models\Validation\HasValue;
use Rhubarb\Stem\Models\Validation\Validator;
use Rhubarb\Stem\UnitTesting\User;
use Rhubarb\Leaf\Views\UnitTestView;
use Rhubarb\Crown\UnitTesting\CoreTestCase;

class ModelFormPresenterTest extends CoreTestCase
{
	/**
	 * @var ModelFormPresenter
	 */
	private $_presenter;

	/**
	 * @var UnitTestView
	 */
	private $_view;

	protected function setUp()
	{
		parent::setUp();

		$this->_presenter = new TestModelPresenter();
		$this->_view = new MockModelPresenterView();

		$this->_presenter->AttachMockView( $this->_view );
	}

	public function testSaveButton()
	{
		$user = new User();

		$user->Username = "abc";

		// This next line is just to make sure validation doesn't fail. Validation is tested below
		// and operates on the presenter model, not the rest model.
		$this->_presenter->Username = "abc";

		$this->_presenter->SetRestModel( $user );

		try
		{
			$this->_view->SimulateEvent( "SavePressed" );

			$this->fail( "SavePressed should have thrown a ForceResponseException to redirect us" );
		}
		catch( ForceResponseException $ex )
		{
		}

		$this->assertFalse( $user->IsNewRecord() );
	}

	public function testCancelButton()
	{
		$user = new User();
		$user->Username = "abc";

		// This next line is just to make sure validation doesn't fail. Validation is tested below
		// and operates on the presenter model, not the rest model.
		$this->_presenter->Username = "abc";

		$this->_presenter->SetRestModel( $user );

		try
		{
			$this->_view->SimulateEvent( "CancelPressed" );

			$this->fail( "SavePressed should have thrown a ForceResponseException to redirect us" );
		}
		catch( ForceResponseException $ex )
		{
		}

		$this->assertTrue( $user->IsNewRecord() );
	}

	public function testInvalidModelDoesntSave()
	{
		$user = new User();
		$user->Username = "";

		$this->_presenter->SetRestModel( $user );

		$this->_view->SimulateEvent( "SavePressed" );

		$this->assertTrue( $user->IsNewRecord(), "This test should have failed validation so the user should not be saved." );
	}
}

class TestModelPresenter extends ModelFormPresenter
{
	protected function CreateDefaultValidator()
	{
		$validator = new Validator();
		$validator->validations[] = new HasValue( "Username" );

		return $validator;
	}
}

class MockModelPresenterView extends UnitTestView
{

}