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

use Rhubarb\Leaf\Leaves\Leaf;

class ModelSearchOrAddControl extends ModelSearchControl
{
    protected $addPresenter;

    /**
     * @var ModelSearchOrAddControlModel
     */
    protected $model;

    public function __construct($name, $modelClassName, Leaf $addPresenter)
    {
        $this->addPresenter = $addPresenter;

        parent::__construct($name, $modelClassName);
    }

    protected function onModelCreated()
    {
        // Rename the presenter to make sure we can simplify how we access it.
        if ($this->addPresenter != null) {
            $this->addPresenter->setName("Add");
            $this->model->addLeaf = $this->addPresenter;
            $this->model->hasAddPresenter = true;
        } else {
            $this->model->hasAddPresenter = false;
        }

        parent::onModelCreated();
    }

    protected function createModel()
    {
        return new ModelSearchOrAddControlModel();
    }

    protected function getViewClass()
    {
        return ModelSearchOrAddControlView::class;
    }
}