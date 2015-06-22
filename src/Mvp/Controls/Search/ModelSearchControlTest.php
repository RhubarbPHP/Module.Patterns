<?php

namespace Rhubarb\Patterns\Mvp\Controls\Search;

use Rhubarb\Crown\UnitTesting\CoreTestCase;
use Rhubarb\Leaf\Views\UnitTestView;
use Rhubarb\Stem\UnitTesting\User;

class ModelSearchControlTest extends CoreTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $user = new User();
        $user->Forename = "Alice";
        $user->Surname = "Smith";
        $user->Save();

        $user = new User();
        $user->Forename = "Bob";
        $user->Surname = "Diamond";
        $user->Save();
    }

    public function testSearchHasItemsOnlyWhenHasPhrase()
    {
        $search = new UnitTestModelSearchControl();
        $items = $search->PublicGetCurrentAvailableSelectionItems();

        $this->assertCount(0, $items);

        $search->Phrase = "Bob";
        $items = $search->PublicGetCurrentAvailableSelectionItems();

        $this->assertCount(1, $items);
        $this->assertEquals("Bob Diamond", $items[0]->label);
    }

    public function testSearchReturnsItems()
    {
        $view = new UnitTestView();
        $search = new UnitTestModelSearchControl();
        $search->AttachMockView($view);

        $items = $view->SimulateEvent("SearchPressed", "alice");
        $this->assertCount(1, $items);
        $this->assertEquals("Alice Smith", $items[0]->label);
    }
}

class UnitTestModelSearchControl extends ModelSearchControl
{
    public function __construct($name = "")
    {
        parent::__construct($name, "Rhubarb\Stem\UnitTesting\User");
    }

    public function PublicRaiseEvent()
    {
        $args = func_get_args();

        return call_user_func_array([$this, "RaiseEvent"], $args);
    }

    public function PublicGetCurrentAvailableSelectionItems()
    {
        return $this->getCurrentlyAvailableSelectionItems();
    }
}
