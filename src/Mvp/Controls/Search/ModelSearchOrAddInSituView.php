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

use Rhubarb\Leaf\Presenters\Controls\Selection\SearchControl\SearchControlView;
use Rhubarb\Leaf\Presenters\Presenter;

class ModelSearchOrAddInSituView extends SearchControlView
{
    private $addPresenter;

    public function __construct(Presenter $addPresenter = null)
    {
        $this->addPresenter = $addPresenter;
    }

    public function createPresenters()
    {
        parent::createPresenters();

        $this->addPresenters($this->addPresenter);
    }

    public function printViewContent()
    {
        ?>
        <div>
            <?php parent::printViewContent(); ?>
        </div>
        <div style="display:none">
            <?php
            if ($this->addPresenter != null) {
                print $this->addPresenter;
            }
            ?>
        </div>
        <?php
    }

    protected function getClientSideViewBridgeName()
    {
        return "ModelSearchOrAddInSituViewBridge";
    }

    public function getDeploymentPackage()
    {
        $package = parent::getDeploymentPackage();
        $package->resourcesToDeploy[] = __DIR__ . "/ModelSearchOrAddInSituViewBridge.js";

        return $package;
    }
}