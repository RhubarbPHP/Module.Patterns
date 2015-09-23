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

namespace Rhubarb\Patterns\Mvp\Crud\ModelForm;

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Crown\String\StringTools;
use Rhubarb\Leaf\Presenters\Controls\Buttons\Button;
use Rhubarb\Leaf\Presenters\Forms\MvpRestBoundForm;
use Rhubarb\Leaf\Presenters\Presenter;
use Rhubarb\Stem\Models\Validation\Validator;
use Rhubarb\Stem\Schema\SolutionSchema;

class ModelFormPresenter extends MvpRestBoundForm
{
    protected function getTitle()
    {
        if ($this->restModel !== null) {
            if ($this->restModel->isNewRecord()) {
                return "Adding a new " . strtolower(
                    StringTools::wordifyStringByUpperCase(
                        SolutionSchema::getModelNameFromClass(get_class($this->restModel))
                    )
                ) . " entry";
            } else {
                return ucfirst(
                    strtolower(
                        StringTools::wordifyStringByUpperCase(
                            SolutionSchema::getModelNameFromClass(get_class($this->restModel))
                        )
                    ) . " '" . $this->restModel->GetLabel() . "'"
                );
            }
        } else {
            if ($this->restCollection !== null) {
                return StringTools::wordifyStringByUpperCase(
                    StringTools::makePlural(
                        SolutionSchema::getModelNameFromClass($this->restCollection->getModelClassName())
                    )
                );
            } else {
                return "Untitled";
            }
        }
    }

    protected function applyModelToView()
    {
        parent::applyModelToView();

        $this->view->title = $this->getTitle();

        if ($this->restModel) {
            $this->view->newModel = $this->restModel->isNewRecord();
        }
    }

    protected function redirectAfterSave()
    {
        $this->redirectAfterCancel();
    }

    protected function redirectAfterCancel()
    {
        throw new ForceResponseException(new RedirectResponse("../"));
    }

    protected function saveRestModel()
    {
        $this->restModel->save();
        return $this->restModel;
    }

    protected final function save()
    {
        $validator = $this->getDefaultValidator();

        if ($validator && $validator instanceof Validator) {
            if (!$this->validate($validator)) {
                return;
            }
        }

        $this->saveRestModel();
        $this->redirectAfterSave();
    }

    protected function cancel()
    {
        $this->redirectAfterCancel();
    }

    protected function delete()
    {
        $this->restModel->delete();
        $this->redirectAfterSave();
    }

    protected function onPresenterAdded(Presenter $presenter)
    {
        if ($presenter->getName() == "Save") {
            if ($presenter instanceof Button) {
                $presenter->validator = $this->createDefaultClientSideValidator();
            }
        }

        parent::onPresenterAdded($presenter);
    }

    protected function configureView()
    {
        parent::configureView();

        $this->view->attachEventHandler(
            "GetRestCollection",
            function () {
                return $this->restCollection;
            }
        );

        $this->view->attachEventHandler(
            "GetRestModel",
            function () {
                return $this->getRestModel();
            }
        );

        $this->view->attachEventHandler(
            "SavePressed",
            function () {
                $this->save();
            }
        );

        $this->view->attachEventHandler(
            "CancelPressed",
            function () {
                $this->cancel();
            }
        );

        $this->view->attachEventHandler(
            "DeletePressed",
            function () {
                $this->delete();
            }
        );
    }
}
