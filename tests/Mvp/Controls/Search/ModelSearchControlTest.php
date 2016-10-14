<?php

namespace Rhubarb\Patterns\Tests\Mvp\Controls\Search;

use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Leaf\Tests\Fixtures\Presenters\UnitTestView;
use Rhubarb\Patterns\Mvp\Controls\Search\ModelSearchControl;
use Rhubarb\Stem\Tests\unit\Fixtures\User;

class ModelSearchControlTest extends RhubarbTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $user = new User();
        $user->Forename = "Alice";
        $user->Surname = "Smith";
        $user->save();

        $user = new User();
        $user->Forename = "Bob";
        $user->Surname = "Diamond";
        $user->save();
    }

    public function testSearchHasItemsOnlyWhenHasPhrase()
    {
        $search = new UnitTestModelSearchControl();
        $items = $search->publicGetCurrentAvailableSelectionItems();

        $this->assertCount(0, $items);

        $search->Phrase = "Bob";
        $items = $search->publicGetCurrentAvailableSelectionItems();

        $this->assertCount(1, $items);
        $this->assertEquals("Bob Diamond", $items[0]->label);
    }

    public function testSearchReturnsItems()
    {
        $view = new UnitTestView();
        $search = new UnitTestModelSearchControl();
        $search->attachMockView($view);

        $items = $view->simulateEvent("SearchPressed", "alice");
        $this->assertCount(1, $items);
        $this->assertEquals("Alice Smith", $items[0]->label);
    }
}

class UnitTestModelSearchControl extends ModelSearchControl
{
    public function __construct($name = "")
    {
        parent::__construct($name, User::class);
    }

    public function publicRaiseEvent()
    {
        $args = func_get_args();

        return call_user_func_array([$this, "RaiseEvent"], $args);
    }

    public function publicGetCurrentAvailableSelectionItems()
    {
        return $this->getCurrentlyAvailableSelectionItems();
    }
}
