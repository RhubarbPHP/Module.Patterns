<?php

/*
 *	Copyright 2015 RhubarbPHP
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

namespace Rhubarb\Patterns\Mvp\Controls\Search;

use Rhubarb\Leaf\Presenters\Controls\Selection\DropDown\DropDown;

/**
 * A control for selecting multiple values from a drop down.
 *
 * To select multiple values additional drop downs are presented on demand.
 */
class RepeatingDropDown extends DropDown
{
    public function __construct($name = "")
    {
        parent::__construct($name);

        $this->attachClientSidePresenterBridge = true;
    }

    protected function createView()
    {
        return new RepeatingDropDownView();
    }

    protected function supportsMultipleSelection()
    {
        return true;
    }

    protected function extractBoundData()
    {
        $items = parent::extractBoundData();
        $savedItems = [];

        foreach ($items as $item) {
            if ($item != "" && $item != "0") {
                $savedItems[] = $item;
            }
        }

        return $savedItems;
    }
}