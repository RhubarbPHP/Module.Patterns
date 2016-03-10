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

namespace Rhubarb\Patterns\Mvp\Views;

use Rhubarb\Crown\Deployment\ResourceDeploymentProvider;
use Rhubarb\Crown\Html\ResourceLoader;

trait TabbedView
{
    protected function getTabs()
    {
        return [];
    }

    private static $useCount = 0;

    protected function printViewContent()
    {
        $tabs = $this->getTabs();

        $handler = ResourceDeploymentProvider::getResourceDeploymentHandler();

        $url = $handler->deployResource(__DIR__ . "/simple-tabs.js");

        $urlsRequired[] = $url;

        self::$useCount++;

        $tabsId = (self::$useCount > 1) ? uniqid() : "";

        ResourceLoader::addScriptCodeOnReady(
            "$( function()
			{
				$( '#tabs{$tabsId}' ).simpleTabs(
				{
					setInputFocus: true,
					appendTabToLocation: true
				} );
			} );",
            $urlsRequired
        );

        ?>
        <div class="tabs-wrapper">

            <ul id='tabs<?= $tabsId; ?>' class="simple-tab-container simple-tabs">
                <?php

                $first = " class='first simple-tab'";

                foreach ($tabs as $tab => $label) {
                    print '<li' . $first . '><a href="#' . $tabsId . preg_replace(
                            "/\W/",
                            "",
                            $tab
                        ) . '">' . $label . '</a></li>';
                    $first = " class='simple-tab'";
                }

                ?>
            </ul>

            <div class="tabs-content">
                <?php

                foreach ($tabs as $tab => $label) {
                    print "<div id='$tabsId$tab' class='standard-form simple-tab-panel'>";

                    $function = "print" . preg_replace("/\W/", "", $tab);

                    if (method_exists($this, $function)) {
                        call_user_func(array($this, $function));
                    }

                    print "</div>";
                }

                ?>
            </div>

            <div class="clear-floats"></div>
        </div>
    <?php
    }
}