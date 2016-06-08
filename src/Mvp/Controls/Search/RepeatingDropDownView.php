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

use Rhubarb\Leaf\Presenters\Controls\Selection\DropDown\DropDownView;

class RepeatingDropDownView extends DropDownView
{
    public function __construct()
    {
        parent::__construct();

        $this->requiresContainer = true;
    }

    public function getDeploymentPackage()
    {
        $package = parent::getDeploymentPackage();
        $package->resourcesToDeploy[] = __DIR__ . "/RepeatingDropDown.js";

        return $package;
    }

    protected function getViewBridgeName()
    {
        return "RepeatingDropDown";
    }
}