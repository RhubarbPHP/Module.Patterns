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

namespace Rhubarb\Patterns\Mvp\BoilerPlates\Login;

use Rhubarb\Leaf\Presenters\Controls\Buttons\Button;
use Rhubarb\Leaf\Presenters\Controls\Text\Password\Password;
use Rhubarb\Leaf\Presenters\Controls\Text\TextBox\TextBox;
use Rhubarb\Leaf\Views\HtmlView;

class LoginView extends HtmlView
{
    public $failed = false;

    public $usernameColumnName = "";

    public function createPresenters()
    {
        parent::createPresenters();

        $this->addPresenters(
            new TextBox($this->usernameColumnName),
            new Password("Password"),
            new Button("Login", "Login", function () {
                $this->raiseEvent("AttemptLogin");
            })
        );
    }

    public function printViewContent()
    {
        ?>


        <?php

        if ($this->failed) {
            print "<div class='alert--error'>Sorry, this username and password combination could not be found, please check and try again.</div>";
        }

        ?>
        <fieldset class="form -horizontal">

            <?php

            $this->printAdditionalBeforeForm();

            ?>

            <div class="field-group">
                <div class="form__inputs"><?= $this->presenters[$this->usernameColumnName]; ?></div>
                <label class="form__label"><?= $this->usernameColumnName; ?>: </label>
            </div>
            <div class="field-group">
                <div class="form__inputs"><?= $this->presenters["Password"]; ?></div>
                <label class="form__label">Password:</label>
            </div>

            <?php
            $this->printAdditionalBeforeButton();
            ?>

            <div class="form-actions">
                <?= $this->presenters["Login"]; ?>
            </div>

            <?php
            $this->printAdditionalAfterButton();
            ?>
        </fieldset>

    <?php
    }

    protected function printAdditionalBeforeForm()
    {

    }

    protected function printAdditionalBeforeButton()
    {

    }

    protected function printAdditionalAfterButton()
    {

    }
}