<?php

namespace Rhubarb\Patterns\Tests\Mvp\Controls\Search;

use Rhubarb\Crown\Context;
use Rhubarb\Crown\Tests\RhubarbTestCase;
use Rhubarb\Patterns\Mvp\Controls\Search\RepeatingDropDown;

class RepeatingDropDownTest extends RhubarbTestCase
{
    public function testEmptyValuesAreRemoved()
    {
        $request = Context::CurrentRequest();
        $request->Post("Test", [0, 1, 2, 3, 0]);

        $result = false;

        $dropDown = new RepeatingDropDown("Test");
        $dropDown->AttachEventHandler("SetBoundData", function ($presenter, $data) use (&$result) {
            $result = $data;
        });

        $dropDown->GenerateResponse($request);

        $this->assertEquals([1, 2, 3], $result);
    }
}