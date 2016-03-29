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

namespace Rhubarb\Patterns\Mvp\BoilerPlates\Login;

use Rhubarb\Crown\Exceptions\ForceResponseException;
use Rhubarb\Crown\LoginProviders\Exceptions\LoginDisabledException;
use Rhubarb\Crown\LoginProviders\Exceptions\LoginFailedException;
use Rhubarb\Crown\LoginProviders\LoginProvider;
use Rhubarb\Crown\Response\RedirectResponse;
use Rhubarb\Leaf\Presenters\Forms\Form;

abstract class LoginPresenter extends Form
{
    private $loginProviderClassName = "";
    private $usernameColumnName = "Username";

    /**
     * @param null $loginProviderClassName If not supplied, the default login provider will be used.
     * @param string $usernameColumnName
     */
    public function __construct($loginProviderClassName = null, $usernameColumnName = "Username")
    {
        parent::__construct();

        if ($loginProviderClassName == null) {
            $loginProviderClassName = LoginProvider::getDefaultLoginProviderClassName();
        }

        $this->loginProviderClassName = $loginProviderClassName;
        $this->usernameColumnName = $usernameColumnName;
    }

    protected function initialiseModel()
    {
        parent::initialiseModel();

        if (isset($_GET["rd"])) {
            $this->model->RedirectUrl = $_GET["rd"];
        }
    }

    protected function getPublicModelPropertyList()
    {
        $list = parent::getPublicModelPropertyList();
        $list[] = "RedirectUrl";

        return $list;
    }

    protected function createView()
    {
        return new LoginView();
    }

    /**
     * Returns the login provider for this presenter.
     *
     * @return \Rhubarb\Stem\LoginProviders\ModelLoginProvider
     */
    private function getLoginProvider()
    {
        $provider = $this->loginProviderClassName;

        return new $provider();
    }

    protected function onSuccess()
    {
        if (isset($this->model->RedirectUrl)) {
            $url = base64_decode($this->model->RedirectUrl);

            if ($url) {
                throw new ForceResponseException(new RedirectResponse($url));
            }
        }

        throw new ForceResponseException(new RedirectResponse($this->getDefaultSuccessUrl()));
    }

    protected function getDefaultSuccessUrl()
    {
        return "/";
    }

    /**
     * Called just before the view is rendered.
     *
     * Guaranteed to only be called once during a normal page execution.
     */
    protected function beforeRenderView()
    {
        $login = $this->getLoginProvider();

        if ($login->isLoggedIn()) {
            $this->onSuccess();
        }
    }

    protected function configureView()
    {
        parent::configureView();

        $this->view->usernameColumnName = $this->usernameColumnName;

        $this->view->attachEventHandler(
            "AttemptLogin",
            function () {
                $login = $this->getLoginProvider();

                try {
                    $usernameColumn = $this->usernameColumnName;

                    if ($login->login($this->$usernameColumn, $this->Password)) {

                        if ($this->model["RememberMe"]) {
                            $login = $this->getLoginProvider();
                            $login->rememberLogin();
                        }

                        $this->onSuccess();
                    }
                } catch (LoginDisabledException $er) {
                    $this->Disabled = true;
                    $this->Failed = true;
                } catch (LoginFailedException $er) {
                    $this->Failed = true;
                }
            }
        );
    }

    protected function applyModelToView()
    {
        parent::applyModelToView();

        if ($this->Failed) {
            $this->view->failed = true;
        }

        if ($this->Disabled) {
            $this->view->disabled = true;
        }
    }
}
