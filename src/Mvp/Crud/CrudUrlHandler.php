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

namespace Rhubarb\Patterns\Mvp\Crud;

use Rhubarb\Crown\Exceptions\CollectionUrlException;
use Rhubarb\Crown\Request\Request;
use Rhubarb\Leaf\UrlHandlers\MvpRestHandler;
use Rhubarb\Stem\Schema\SolutionSchema;
use Rhubarb\Stem\UrlHandlers\ModelCollectionHandler;

class CrudUrlHandler extends MvpRestHandler
{
    private $namespaceBase;

    private $presenterClassStub;

    public function __construct($modelName, $namespaceBase, $additionalPresenterClassNameMap = [], $childUrlHandlers = [])
    {
        $namespaceBase = rtrim($namespaceBase, "\\");
        $this->namespaceBase = $namespaceBase;

        // Get the parent folder which will become our collection presenter
        $parts = explode("/", str_replace("\\", "/", $namespaceBase));

        $this->presenterClassStub = $parts[sizeof($parts) - 1];

        parent::__construct(
            $modelName,
            $namespaceBase . "\\" . $this->presenterClassStub . "CollectionPresenter",
            $namespaceBase . "\\" . $this->presenterClassStub . "ItemPresenter",
            $additionalPresenterClassNameMap,
            $childUrlHandlers
        );
    }

    public function getModelObject()
    {
        try {
            return parent::getModelObject();
        } catch (CollectionUrlException $er) {
            // Normally you can't get a model with no identifier in the rest handler.
            // However we want to allow for retrieval of a fresh model ready to receive our post back
            // data. So we restore that behaviour here.
            if (!$this->isCollection) {
                $newModel = SolutionSchema::getModel($this->modelName);

                // If we have a parent handler - see if it can populate our model with some foreign keys.
                $parentHandler = $this->getParentHandler();

                if ($parentHandler !== null && ($parentHandler instanceof ModelCollectionHandler)) {
                    $parentHandler->populateNewModelWithRelationshipValues($newModel);
                }

                return $newModel;
            }

            throw $er;
        }
    }

    private function makeActionClassFriendly($action)
    {
        return str_replace(" ", "", ucwords(strtolower(str_replace("-", " ", $action))));
    }

    protected function getPresenterClassName()
    {
        if ($this->urlAction != "") {
            // If the url action is not a number we can only have arrived here if the GetMatchingUrlFragment has already proved
            // that a presenter class is waiting for us...

            $mvpClass = $this->namespaceBase . "\\" . $this->presenterClassStub .
                $this->makeActionClassFriendly($this->urlAction) . "Presenter";

            if (class_exists($mvpClass)) {
                return $mvpClass;
            }
        }

        return parent::getPresenterClassName();
    }

    private function checkForPotentialAction($actionName)
    {
        $potentialClassName = $this->namespaceBase . "\\" . $this->presenterClassStub .
            $this->makeActionClassFriendly($actionName) . "Presenter";

        return class_exists($potentialClassName);
    }

    protected function getMatchingUrlFragment(Request $request, $currentUrlFragment = "")
    {
        $uri = $currentUrlFragment;

        $parentResponse = parent::getMatchingUrlFragment($request, $currentUrlFragment);

        $this->isCollection = true;

        if (preg_match("|^" . $this->url . "([^/]+)/([a-zA-Z0-9\-]+)|", $uri, $matches)) {
            if ($this->checkForPotentialAction($matches[2])) {
                $this->urlAction = $matches[2];
                $this->isCollection = false;

                return $matches[0];
            }
        }

        if (preg_match("|^" . $this->url . "([^/]+)/|", $uri, $match)) {
            $found = false;

            if ($match[1] == "add") {
                // Make sure that when adding we get a model object not a collection object.
                $this->isCollection = false;
            }

            if ($this->checkForPotentialAction($match[1])) {
                $found = true;
            } elseif ($match[1] == "add") {
                if ($this->checkForPotentialAction("Item")) {
                    $found = true;
                }
            }

            if ($found) {
                $this->urlAction = $match[1];
                return $match[0];
            }

            $this->resourceIdentifier = $this->urlAction = $match[1];
            $this->isCollection = false;
            return $match[0];
        }

        return $parentResponse;
    }
}