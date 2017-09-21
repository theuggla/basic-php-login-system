<?php

namespace model;

class User {

    private $username;
    private $password;

    public function __construct() {
        $this->username = new \model\Username();
        $this->password = new \model\Password();
    }

    public function doesUserExist(string $username, string $password) {
        try {
            $this->findUser($username, $password);
            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function verifyUserByCookie(string $username, string $password) {
        $this->username->validateUsername($username);
        $this->password->validatePassword($password);

        //TODO: Fix so that saving a cookie updates the password in the database, then compare here properly

        $query='SELECT * FROM User WHERE BINARY username="' . $username . '"';
        $dbconnection = \model\DBConnector::getConnection('UserRegistry');
        $result = $dbconnection->query($query);
        
        if ($result->num_rows <= 0) {
            throw new \model\WrongInfoInCookieException('Wrong information in cookies');
        } else {
            return true;
        }
    }

    private function findUser(string $username, string $password) {
        $this->username->validateUsername($username);
        $this->password->validatePassword($password);

        $query='SELECT * FROM User WHERE BINARY username="' . $username . '" AND BINARY password="' . $password . '"';
        $dbconnection = \model\DBConnector::getConnection('UserRegistry');
        $result = $dbconnection->query($query);
        
        if ($result->num_rows <= 0) {
            throw new \model\WrongCredentialsException('Wrong name or password');
        }
    }

    public function logout() {
        $_SESSION["isLoggedIn"] = false;
    }

    public function login() {
        $_SESSION["isLoggedIn"] = true;
    }

    public function isUserLoggedIn() {
        return isset($_SESSION["isLoggedIn"]) && $_SESSION["isLoggedIn"];
    }

    public function hashPassword($password) {
        $hashedPassword = $this->password->hashPassword($password);
        return ($hashedPassword);
    }
}

?>