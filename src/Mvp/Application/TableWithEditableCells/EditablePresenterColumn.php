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

use Rhubarb\Leaf\Table\Leaves\Columns\LeafColumn;
use Rhubarb\Leaf\Table\Leaves\Columns\TableColumn;
use Rhubarb\Leaf\Presenters\Leaf;
use Rhubarb\Stem\Models\Model;

class EditableLeafColumn extends LeafColumn
{
    private $shadowColumn = false;

    public function __construct(Leaf $leaf, $label = "")
    {
        parent::__construct($leaf, $label);
    }

    public function GetSortableColumnName()
    {
        return $this->shadowColumn->getSortableColumnName();
    }

    /**
     * @param TableColumn $shadowColumn
     */
    public function setShadowColumn($shadowColumn)
    {
        $this->shadowColumn = $shadowColumn;
    }

    /**
     * @return TableColumn
     */
    public function getShadowColumn()
    {
        return $this->shadowColumn;
    }

    public function getCustomCellAttributes(Model $row)
    {
        $attributes = parent::getCustomCellAttributes($row);
        $attributes["data-value"] = json_encode($this->leaf->FetchBoundData());

        return $attributes;
    }

    protected function getCellValue(Model $row, $decorator)
    {
        return $this->shadowColumn->getCellValue($row, $decorator);
    }
}