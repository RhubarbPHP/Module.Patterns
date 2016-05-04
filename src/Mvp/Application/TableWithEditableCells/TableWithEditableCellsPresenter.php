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

namespace Rhubarb\Patterns\Mvp\Application\TableWithEditableCells;

use Rhubarb\Leaf\Table\Leaves\Columns;
use Rhubarb\Leaf\Table\Leaves\Table;
use Rhubarb\Leaf\Presenters\SpawnableByViewBridgePresenter;

class TableWithEditableCellsPresenter extends Table
{
    protected function createView()
    {
        return new TableWithEditableCellsView();
    }

    protected function configureView()
    {
        parent::configureView();

        $this->view->attachEventHandler(
            "RowSaved",
            function ($rowId, $rowData) {
                $result = $this->raiseEvent("RowSaved", $rowId, $rowData);

                if ($result !== null) {
                    return $result;
                }

                return false;
            }
        );
    }

    protected function createColumnFromObject($object, $label)
    {
        if ($object instanceof SpawnableByViewBridgePresenter) {
            return new EditableLeafColumn($object, $label);
        }

        return parent::createColumnFromObject($object, $label);
    }

    protected function inflateColumns($columns)
    {
        $columns = parent::inflateColumns($columns);
        $columns[] = new RowControlsColumn();

        foreach ($columns as $column) {
            if ($column instanceof EditableLeafColumn) {
                $column->setShadowColumn($this->createColumnFromString($column->getLeaf()->getName(), ""));
            }
        }

        return $columns;
    }

    private function compileSpawnableSettings()
    {
        $spawnSettings = [];
        $columns = $this->inflateColumns($this->Columns);

        $i = -1;

        foreach ($columns as $column) {
            $i++;

            if ($column instanceof EditableLeafColumn) {
                $spawnSettings[$i] = $column->getLeaf()->getSpawnStructure();
            }
        }

        $this->model->SpawnSettings = $spawnSettings;
    }

    protected function applyModelToView()
    {
        $this->compileSpawnableSettings();

        parent::applyModelToView();
    }

    protected function getPublicModelPropertyList()
    {
        $properties = parent::getPublicModelPropertyList();
        $properties[] = "SpawnSettings";

        return $properties;
    }
}