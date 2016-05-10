<?php

namespace Rhubarb\Patterns\Mvp\Controls\Search;

class ModelSearchOrAddInSituControl extends ModelSearchOrAddControl
{
    protected function createView()
    {
        return new ModelSearchOrAddInSituView($this->addPresenter);
    }

}