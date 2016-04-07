<?php

namespace Rhubarb\Patterns\Tests\Mvp\Controls\Search;

use Rhubarb\Crown\Request\Request;
use Rhubarb\Crown\Tests\Fixtures\TestCases\RhubarbTestCase;
use Rhubarb\Patterns\Mvp\Controls\Search\RepeatingDropDown;

class RepeatingDropDownTest extends RhubarbTestCase
{
    public function testEmptyValuesAreRemoved()
    {
        $request = Request::current();
        $request->post("Test", [0, 1, 2, 3, 0]);

        $result = false;

        $dropDown = new RepeatingDropDown("Test");
        $dropDown->AttachEventHandler("SetBoundData", function ($presenter, $data) use (&$result) {
            $result = $data;
        });

        $dropDown->GenerateResponse($request);

        $this->assertEquals([1, 2, 3], $result);
    }
}