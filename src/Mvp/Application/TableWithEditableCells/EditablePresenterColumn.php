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

use Rhubarb\Leaf\Presenters\Application\Table\Columns\PresenterColumn;
use Rhubarb\Leaf\Presenters\Application\Table\Columns\SortableColumn;
use Rhubarb\Leaf\Presenters\Application\Table\Columns\TableColumn;
use Rhubarb\Leaf\Presenters\Presenter;
use Rhubarb\Stem\Models\Model;

class EditablePresenterColumn extends PresenterColumn
{
    /** @var SortableColumn|TableColumn */
    private $shadowColumn = null;

    public function __construct(Presenter $presenter, $label = "")
    {
        parent::__construct($presenter, $label);
    }

    public function getSortableColumnName()
    {
        return $this->shadowColumn->getSortableColumnName();
    }

    /**
     * @param SortableColumn $shadowColumn
     */
    public function setShadowColumn($shadowColumn)
    {
        $this->shadowColumn = $shadowColumn;
    }

    /**
     * @return SortableColumn
     */
    public function getShadowColumn()
    {
        return $this->shadowColumn;
    }

    public function getCustomCellAttributes(Model $row)
    {
        $attributes = parent::getCustomCellAttributes($row);
        $attributes["data-value"] = json_encode($this->presenter->fetchBoundData());

        return $attributes;
    }

    protected function getCellValue(Model $row, $decorator)
    {
        return $this->shadowColumn->getCellValue($row, $decorator);
    }
}
