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

namespace Rhubarb\Patterns\Mvp\Application\TableWithEditableRows;

use Rhubarb\Leaf\Table\Leaves\Columns\ISortableColumn;
use Rhubarb\Leaf\Table\Leaves\Table;
use Rhubarb\Stem\Models\Model;

abstract class TableWithEditableRowsPresenter extends Table
{
    protected function createView()
    {
        return new TableWithEditableRowsView();
    }

    protected function configureView()
    {
        parent::configureView();

        $this->view->attachEventHandler(
            "GetAdditionalClientSideRowData",
            function ($model) {
                return $this->getClientSideDataForModel($model);
            }
        );
    }

    /**
     * Implement and return an array of data to attach to the table row.
     *
     * @param Model $model
     * @return string[]
     */
    protected abstract function getClientSideDataForModel(Model $model);

}