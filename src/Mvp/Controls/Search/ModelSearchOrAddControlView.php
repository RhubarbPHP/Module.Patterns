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

use Rhubarb\Leaf\Controls\Common\SelectionControls\SearchControl\SearchControlView;
use Rhubarb\Leaf\Leaves\Leaf;
use Rhubarb\Leaf\Leaves\LeafDeploymentPackage;

class ModelSearchOrAddControlView extends SearchControlView
{
    private $addPresenter;

    public function __construct(Leaf $addPresenter = null)
    {
        $this->addPresenter = $addPresenter;
    }

    protected function createSubLeaves()
    {
        parent::createSubLeaves();

        $this->registerSubLeaf($this->addPresenter);
    }

    public function printViewContent()
    {
        parent::printViewContent();

        if ($this->addPresenter != null) {
            print $this->addPresenter;
        }
    }

    protected function getViewBridgeName()
    {
        return "ModelSearchOrAddControlViewBridge";
    }

    public function getDeploymentPackage()
    {
        $package = parent::getDeploymentPackage();
        $package->resourcesToDeploy[] = __DIR__ . "/ModelSearchOrAddControlViewBridge.js";

        return $package;
    }
}