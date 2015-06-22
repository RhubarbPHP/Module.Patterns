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

namespace Rhubarb\Patterns\Mvp\Crud;

use Rhubarb\Crown\Settings\HtmlPageSettings;
use Rhubarb\Crown\String\StringTools;
use Rhubarb\Leaf\Presenters\Controls\Buttons\Button;
use Rhubarb\Leaf\Views\HtmlView;
use Rhubarb\Patterns\Mvp\Controls\Buttons\PrimaryButton;
use Rhubarb\Patterns\Mvp\Controls\Buttons\WarningButton;
use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Schema\SolutionSchema;

class CrudView extends HtmlView
{
    public $newModel = true;

    public function createPresenters()
    {
        $this->addPresenters(
            $save = new PrimaryButton("Save", "Save", function () {
                $this->raiseEvent("SavePressed");
            }),
            new Button("Cancel", "Cancel", function () {
                $this->raiseEvent("CancelPressed");
            }),
            $delete = new WarningButton("Delete", "Delete", function () {
                $this->raiseEvent("DeletePressed");
            }),
            new Button("Add", "Add", function () {
                $this->raiseEvent("AddPressed");
            })
        );

        $validator = $this->raiseEvent("GetDefaultClientSideValidator");

        if ($validator) {
            $save->setValidator($validator);
        }

        $delete->setConfirmMessage("Are you sure you want to delete this item?");

        parent::createPresenters();
    }

    /**
     * Override this to provide a name for the real world entity being manipulated.
     * @return string
     */
    protected function getEntityName()
    {
        $restModel = $this->raiseEvent("GetRestModel");

        if ($restModel instanceof Model) {
            return StringTools::wordifyStringByUpperCase(
                SolutionSchema::getModelNameFromClass(get_class($restModel))
            );
        }

        if ($restModel instanceof Collection) {
            return StringTools::wordifyStringByUpperCase(
                SolutionSchema::getModelNameFromClass($restModel->getModelClassName())
            );
        }

        return "";
    }

    protected function onBeforePrintViewContent()
    {
        parent::onBeforePrintViewContent();

        $restModel = $this->raiseEvent("GetRestModel");

        if (is_object($restModel)) {
            $pageSettings = new HtmlPageSettings();

            if ($restModel instanceof Model) {
                if ($restModel->isNewRecord()) {
                    $pageSettings->PageTitle = "Add a " . $this->getEntityName();
                } else {
                    $pageSettings->PageTitle = "Editing " . $this->getEntityName() . " '" . $restModel->getLabel() . "'";
                }
            } elseif ($restModel instanceof Collection) {
                $pageSettings->PageTitle = StringTools::pluralise($this->getEntityName(), 2);
            }
        }
    }
}