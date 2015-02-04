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

use Rhubarb\Leaf\Presenters\Controls\Selection\SelectionControlPresenter;

class ItemTogglePresenter extends SelectionControlPresenter
{
    protected function createView()
    {
        return new ItemToggleView();
    }

	protected function parseRequestForCommand()
	{
		// ItemTogglePresenters use the model to store their value so don't present an
		// actual HTML input. In this case we should always indicate we've 'changed' to make sure
		// model bindings kick in.

		$this->setBoundData();

		parent::parseRequestForCommand();
	}
}