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

use Rhubarb\Leaf\Presenters\Controls\Selection\SelectionControlView;

class ItemToggleView extends SelectionControlView
{
    public function __construct()
    {
        $this->requiresStateInputs = true;
    }

    protected function printViewContent()
    {
        ?>
    <span class="item-toggles" id="<?= \htmlentities($this->getIndexedPresenterPath()); ?>"
          presenter-name="<?= \htmlentities($this->presenterName); ?>">
        <?php

        foreach ($this->availableItems as $item) {
            $itemList = [$item];

            // Note: No Support for option groups....

            foreach ($itemList as $subItem) {
                $value = $subItem->value;
                $text = $subItem->label;

                $data = json_encode($subItem);

                $selected = $this->isValueSelected($value) ? " -is-selected" : "";
                print "<a href=\"#\" data-item=\"" . \htmlentities(
                        $data
                    ) . "\" class=\"item " . $selected . "\">" . \htmlentities($text) . "</a>
";
            }
        }
        ?>
        </span><?php
    }

    protected function getClientSideViewBridgeName()
    {
        return "ItemToggleViewBridge";
    }

    public function getDeploymentPackage()
    {
        $package = parent::getDeploymentPackage();
        $package->resourcesToDeploy[] = __DIR__ . "/" . $this->getClientSideViewBridgeName() . ".js";

        return $package;
    }
}