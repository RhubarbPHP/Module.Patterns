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

    private $onTabSelectedJs = "";

    protected function printViewContent()
    {
        $tabs = $this->getTabs();

        $handler = ResourceDeploymentProvider::getProvider();

        $url = $handler->deployResource(__DIR__ . "/simple-tabs.js");

        $urlsRequired[] = $url;

        self::$useCount++;

        $tabsId = (self::$useCount > 1) ? uniqid() : "";

        ResourceLoader::addScriptCode(
            "$( function()
			{
				$( '#tabs{$tabsId}' ).simpleTabs(
				{
					setInputFocus: true,
					appendTabToLocation: true,
					$this->onTabSelectedJs
				} );
			} );",
            $urlsRequired
        );

        ?>
        <div class="tabs-wrapper">

            <ul id="tabs<?= $tabsId; ?>" class="simple-tab-container simple-tabs">
                <?php

                $first = ' class="first simple-tab"';

                foreach ($tabs as $tab => $label) {
                    print '<li' . $first . '><a href="#' . $tabsId . preg_replace('/\W/', '', $tab) . '">' . $label . '</a></li>';
                    $first = ' class="simple-tab"';
                }

                ?>
            </ul>

            <div class="tabs-content">
                <?php

                foreach ($tabs as $tab => $label) {
                    print <<<HTML
                        <div id="$tabsId$tab" class="standard-form simple-tab-panel">
HTML;

                    $function = "print" . preg_replace('/\W/', '', $tab);

                    if (method_exists($this, $function)) {
                        call_user_func([$this, $function]);
                    }

                    print "</div>";
                }

                ?>
            </div>

            <div class="clear-floats"></div>
        </div>
        <?php
    }

    /**
     * Provides a snippet of JavaScript which will be executed when a tab is selected.
     * A JavaScript variable named panelName will be available to this code.
     *
     * @param string $onTabSelectedJs
     */
    public function setOnTabSelectedJs($onTabSelectedJs)
    {
        $this->onTabSelectedJs = "
            onTabSelected: function(panelName) {
                $onTabSelectedJs;
            }";
    }
}
