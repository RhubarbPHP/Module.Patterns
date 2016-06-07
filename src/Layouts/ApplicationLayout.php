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

namespace Rhubarb\Patterns\Layouts;

require_once __DIR__ . '/BaseLayout.php';

class ApplicationLayout extends BaseLayout
{
    protected function printLoginStatus()
    {

    }

    protected function printTop()
    {
        ?>
        <div id="top">

        <div class="ajax-progress"><p>Processing... please wait...</p></div>

        <div id="header">
            <p class="system-title"><strong><?php $this->printSystemTitle(); ?></strong></p>
            <?php $this->printLoginStatus(); ?>
        </div>

        <div id="nav">
            <?php $this->printMenu(); ?>
            <div class="logo"><?php $this->printLogo(); ?></div>
        </div>

        <div id="content">
        <div class="shell">
        <?php
    }

    protected function printSystemTitle()
    {

    }

    protected function printLogo()
    {

    }

    protected function printMenu()
    {

    }

    protected function printTail()
    {
        ?>
        </div>
        </div>
        </div>
        <?php
    }
}
