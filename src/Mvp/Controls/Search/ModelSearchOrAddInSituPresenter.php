<?php

namespace Rhubarb\Patterns\Mvp\Controls\Search;

class ModelSearchOrAddInSituPresenter extends ModelSearchOrAddPresenter
{
    protected function createView()
    {
        return new ModelSearchOrAddInSituView($this->addPresenter);
    }
}
