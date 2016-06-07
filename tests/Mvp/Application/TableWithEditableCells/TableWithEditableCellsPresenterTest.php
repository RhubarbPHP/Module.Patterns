<?php

namespace Rhubarb\Patterns\Tests\Mvp\Application\TableWithEditableCells;

use Rhubarb\Crown\Tests\RhubarbTestCase;
use Rhubarb\Leaf\Presenters\Application\Table\Columns\ModelColumn;
use Rhubarb\Leaf\Presenters\Application\Table\Columns\Template;
use Rhubarb\Leaf\Presenters\Controls\Text\TextBox\TextBox;
use Rhubarb\Leaf\Presenters\Forms\Form;
use Rhubarb\Leaf\Views\View;
use Rhubarb\Patterns\Mvp\Application\TableWithEditableCells\EditablePresenterColumn;
use Rhubarb\Patterns\Mvp\Application\TableWithEditableCells\TableWithEditableCellsPresenter;
use Rhubarb\Stem\Tests\Fixtures\Example;

class TableWithEditableCellsPresenterTest extends RhubarbTestCase
{
    public function testEditablePresenterColumnCreatedWhenGivenAPresenter()
    {
        $table = new MyEditableTable(Example::find());
        $table->Columns =
            [
                new TextBox("Forename")
            ];

        $columns = $table->publicInflateColumns();

        $this->assertInstanceOf(EditablePresenterColumn::class, $columns[0]);
    }

    public function testTableGetsRowControlsColumn()
    {
        $example = new Example();
        $example->Forename = "Andrew";
        $example->save();

        $table = new MyEditableTable(Example::find());
        $table->Columns =
            [
                new Template("Empty Column")
            ];

        $response = $table->generateResponse();

        $this->assertContains("class=\"row-controls\"", $response);
    }

    public function testTableGetsControlCreationData()
    {
        $example = new Example();
        $example->Forename = "Andrew";
        $example->save();

        $host = new MyEditableForm();
        $response = $host->generateResponse();

        $this->assertContains("data-value=\"&quot;Andrew&quot;\"", $response);
        $this->assertContains(">Andrew</td>", $response);

        $table = MyEditableView::$table;
        $model = $table->model;

        $this->assertInternalType("array", $model->SpawnSettings);
        $this->assertEquals("Forename", $model->SpawnSettings[0]["PresenterName"]);
    }

    public function testTableColumnGetsShadowColumn()
    {
        $example = new Example();
        $example->Forename = "Andrew";
        $example->save();

        $host = new MyEditableForm();
        $host->generateResponse();

        $this->assertInstanceOf(ModelColumn::class, MyEditableView::$column->getShadowColumn());
    }
}

class MyEditableTable extends TableWithEditableCellsPresenter
{
    public function publicGetModelState()
    {
        return $this->getModelState();
    }

    public function publicInflateColumns()
    {
        return $this->inflateColumns($this->Columns);
    }
}

class MyEditableForm extends Form
{
    protected function createView()
    {
        return new MyEditableView();
    }
}

class MyEditableView extends View
{
    public static $table;
    public static $textbox;
    public static $column;

    public function createPresenters()
    {
        self::$table = new TableWithEditableCellsPresenter(Example::find());
        self::$textbox = new TextBox("Forename");

        self::$table->Columns =
            [
                self::$column = new EditablePresenterColumn(self::$textbox)
            ];

        $this->addPresenters(
            self::$table,
            self::$textbox
        );
    }

    protected function printViewContent()
    {
        print self::$table;
    }
}
