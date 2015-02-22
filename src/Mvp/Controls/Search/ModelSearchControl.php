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

use Rhubarb\Leaf\Presenters\Controls\Selection\SearchControl\SearchControl;
use Rhubarb\Stem\Collections\Collection;
use Rhubarb\Stem\Filters\Contains;
use Rhubarb\Stem\Models\Model;
use Rhubarb\Stem\Schema\SolutionSchema;

class ModelSearchControl extends SearchControl
{
    protected $modelClassName;

    public function __construct($name = "", $modelClassName)
    {
        parent::__construct($name);

        $this->modelClassName = $modelClassName;
    }

    protected function getResultColumns()
    {
        $schema = SolutionSchema::getModelSchema($this->modelClassName);

        return [$schema->labelColumnName];
    }

    protected function getLabelForItem($item)
    {
        if (!$item) {
            return "";
        }

        if (!$item instanceof Model) {
            $item = $this->convertValueToModel($item);
        }

        return $item->getLabel();
    }

    protected function convertValueToModel($value)
    {
        if ($value) {
            $object = SolutionSchema::getModel($this->modelClassName, $value);

            return $object;
        }

        return parent::convertValueToModel($value);
    }

    protected function getCurrentlyAvailableSelectionItems()
    {
        if ($this->Phrase == "") {
            return [];
        }

        $class = $this->modelClassName;

        $list = new Collection($class);
        $filter = $this->getCollectionFilter($this->Phrase);

        $list->filter($filter);

        $results = [];

        foreach ($list as $item) {
            $result = $this->makeItem($item->getUniqueIdentifier(), $item->getLabel(), $this->getDataForItem($item));
            $results[] = $result;
        }

        return $results;
    }

    protected function getCollectionFilter($phrase)
    {
        $model = SolutionSchema::getModel($this->modelClassName);

        $filter = new Contains($model->getLabelColumnName(), $phrase);
        return $filter;
    }

    protected function createView()
    {
        return new ModelSearchView();
    }
}