<?php

namespace Rhubarb\Patterns\Mvp\Application\TableWithEditableCells;

use Rhubarb\Stem\UnitTesting\Example;
use Rhubarb\Leaf\Presenters\Application\Table\Columns\PresenterColumn;
use Rhubarb\Leaf\Presenters\Application\Table\Columns\Template;
use Rhubarb\Leaf\Presenters\Controls\Text\TextBox\TextBox;
use Rhubarb\Leaf\Presenters\Forms\Form;
use Rhubarb\Leaf\Views\View;
use Rhubarb\Crown\UnitTesting\CoreTestCase;
use Gcd\Tests\ViewTest;

class TableWithEditableCellsPresenterTest extends CoreTestCase
{
	public function testEditablePresenterColumnCreatedWhenGivenAPresenter()
	{
		$table = new MyEditableTable( Example::Find() );
		$table->Columns =
			[
				new TextBox( "Forename" )
			];

		$columns = $table->PublicInflateColumns();

		$this->assertInstanceOf( "Rhubarb\Patterns\Mvp\Application\TableWithEditableCells\EditablePresenterColumn", $columns[0] );
	}

	public function testTableGetsRowControlsColumn()
	{
		$example = new Example();
		$example->Forename = "Andrew";
		$example->Save();

		$table = new MyEditableTable( Example::Find() );
		$table->Columns =
		[
			new Template( "Empty Column" )
		];

		$response = $table->GenerateResponse();

		$this->assertContains( "class=\"row-controls\"", $response );
	}

	public function testTableGetsControlCreationData()
	{
		$example = new Example();
		$example->Forename = "Andrew";
		$example->Save();

		$host = new MyEditableForm();
		$response = $host->GenerateResponse();

		$this->assertContains( "data-value=\"&quot;Andrew&quot;\"", $response );
		$this->assertContains( ">Andrew</td>", $response );

		$table = MyEditableView::$table;
		$model = $table->model;

		$this->assertInternalType( "array", $model->SpawnSettings );
		$this->assertEquals( "Forename", $model->SpawnSettings[0][ "PresenterName" ] );
	}

	public function testTableColumnGetsShadowColumn()
	{
		$example = new Example();
		$example->Forename = "Andrew";
		$example->Save();

		$host = new MyEditableForm();
		$host->GenerateResponse();

		$this->assertInstanceOf( "Rhubarb\Leaf\Presenters\Application\Table\Columns\ModelColumn", MyEditableView::$column->GetShadowColumn() );
	}
}

class MyEditableTable extends TableWithEditableCellsPresenter
{
	public function PublicGetModelState()
	{
		return $this->GetModelState();
	}

	public function PublicInflateColumns()
	{
		return $this->InflateColumns( $this->Columns );
	}
}

class MyEditableForm extends Form
{
	protected function CreateView()
	{
		return new MyEditableView();
	}
}

class MyEditableView extends View
{
	public static $table;
	public static $textbox;
	public static $column;

	public function CreatePresenters()
	{
		self::$table = new TableWithEditableCellsPresenter( Example::Find() );
		self::$textbox = new TextBox( "Forename" );

		self::$table->Columns =
		[
			self::$column = new EditablePresenterColumn( self::$textbox )
		];

		$this->AddPresenters(
			self::$table,
			self::$textbox
		);
	}

	protected function PrintViewContent()
	{
		print self::$table;
	}
}