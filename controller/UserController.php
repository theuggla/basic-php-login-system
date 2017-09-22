<?php

namespace controller;

    class UserController {
        private $loginController = 'UserController::LoginController';
        private $registerController = 'UserController::RegisterController';

        private $user  = 'UserController::User';

        private $displayLoginForm = true;
        private $displayRegisterForm = false;

        public function __construct($user, $loginController, $registerController) {
            $this->loginController = $loginController;
            $this->registerController = $registerController;

            $this->user = $user;

        }

        public function greetUserCorrectly() {
            $isLoggedIn = $this->user->isUserLoggedIn(); 
            $sessionIsNotHijacked = isset($_SESSION["userAgent"]) && $_SESSION["userAgent"] == $_SERVER["HTTP_USER_AGENT"];
            $wantsToRegister = isset($_GET["register"]);
            if ($isLoggedIn && $sessionIsNotHijacked) {
                $this->loginController->handleLoggedInUser();
                if (!$this->user->isLoggedIn()) {
                    $this->displayLogout = false;
                }
            } else if ($wantsToRegister) {
                $this->displayLogout = false;
                $this->displayRegister = true;
            } else {
                $this->loginController->handleLoggedOutUser();
                if (!$this->loginController->loginSucceeded()) {
                    $this->displayLogout = false;
                }
                
            }
            if ($this->displayLogout) {
                $this->loginController->showLogoutForm();
            } else if ($this->displayRegister) {
                $this->registerController->showRegisterForm();
            } else {
                $this->loginController->showLoginForm();
            }
        }

        private function delegateControlDependingOnUseCase() {
            if ($this->user->isLoggedIn() && $this->user->hasNotBeenHijacked()) {                
                
                $this->loginController->handleLoggedInUser();

                if ($this->user->isLoggedIn()) {
                    $this->displayLoginForm = false;
                }

            } else if ($this->registerController->userWantsToRegister()) {
                
                $this->displayRegisterForm = true;

                $this->registerController->handleUser();

            } else {
                $this->loginController->handleLoggedOutUser();

                if ($this->user->isLoggedIn()) {
                    $this->displayLoginForm = false;
                }
                
            }

        }

        private function displayCorrectView() {
            if ($this->displayLoginForm) {

                $this->loginController->showLoginForm();
                
            } else if ($this->displayRegisterForm) {

                $this->registerController->showRegisterForm();

            } else {

                $this->loginController->showLogoutForm();
                
            }
        }
    }
?>