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

use Rhubarb\Crown\Settings\HtmlPageSettings;
use Rhubarb\Crown\Html\ResourceLoader;
use Rhubarb\Crown\Layout\Layout;
use Rhubarb\Crown\Layout\LayoutModule;

class BaseLayout extends Layout
{
    protected function getTitle()
    {
        $pageSettings = new HtmlPageSettings();
        return $pageSettings->PageTitle;
    }

    protected function printHead()
    {

    }

    protected function printTop()
    {

    }

    protected function printTail()
    {

    }

    protected function printContent($content)
    {
        print $content;
    }

    /**
     * An opportunity to print a page heading.
     *
     * Defaults to printing the result of GetTitle in an <h1> tag.
     */
    protected function printPageHeading()
    {
        $title = $this->getTitle();

        if ($title != "") {
            print "<h1>" . $title . "</h1>";
        }
    }

    protected function printLayout($content)
    {
        ?>
<html>
<head>
    <title><?= $this->getTitle(); ?></title>
    <?= LayoutModule::getHeadItemsAsHtml(); ?>
    <?= ResourceLoader::getResourceInjectionHtml(); ?>
    <?php $this->printHead(); ?>
</head>
<body>
<?php $this->printTop(); ?>
<?= LayoutModule::getBodyItemsAsHtml(); ?>
<?php $this->printPageHeading(); ?>
<?php $this->printContent($content); ?>
<?php $this->printTail(); ?>
</body>
</html>
    <?php

    }
}