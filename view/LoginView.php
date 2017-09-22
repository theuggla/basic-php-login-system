<?php

namespace view;

	class LoginView implements IUseCaseView {
    	private static $login = 'LoginView::Login';
    	private static $logout = 'LoginView::Logout';
    	private static $name = 'LoginView::UserName';
    	private static $password = 'LoginView::Password';
    	private static $cookieName = 'LoginView::CookieName';
    	private static $cookiePassword = 'LoginView::CookiePassword';
    	private static $keep = 'LoginView::KeepMeLoggedIn';
    	private static $messageId = 'LoginView::Message';

		public function getBodyWithMessage(string $message = '', bool $isLoggedIn) {
			if ($isLoggedIn) {
				$response = $this->generateLogoutButtonHTML($message);
			} else {
				 $response = $this->generateLoginFormHTML($message);
			}
        	
        	return $response;
    	}
    
    	private function generateLoginFormHTML($message)
    	{
			$lastUsernameTried = $this->getRequestUsername();

        	return '
				<form method="post" > 
					<fieldset>
						<legend>Login - enter Username and password</legend>
						<p id="' . self::$messageId . '">' . $message . '</p>
					
						<label for="' . self::$name . '">Username :</label>
						<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $lastUsernameTried . '"/>

						<label for="' . self::$password . '">Password :</label>
						<input type="password" id="' . self::$password . '" name="' . self::$password . '" />

						<label for="' . self::$keep . '">Keep me logged in  :</label>
						<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
					
						<input type="submit" name="' . self::$login . '" value="login" />
					</fieldset>	
				</form>
			';
    	}

		private function generateLogoutButtonHTML($message)
    	{
        	return '
				<form  method="post" >
					<p id="' . self::$messageId . '">' . $message .'</p>
					<input type="submit" name="' . self::$logout . '" value="logout"/>
				</form>
			';
    	}

		public function userWantsToKeepCredentials() {
    		return isset($_POST[self::$keep]);
		}

		public function getUserCredentials() {
			$username = $this->getRequestUserName();
			$password = $this->getRequestPassword();
    		return array('username'=>$username, 'password'=>$password);
		}

		public function setCookieCredentials($credentials, $expiry) {
			setcookie(self::$cookieName, $credentials["username"], $expiry, "/");
			setcookie(self::$cookiePassword, $credentials["cookiePassword"], $expiry, "/");
		}

		public function getCookieCredentials() {
			$result;

			if(isset($_COOKIE[self::$cookieName]) && isset($_COOKIE[self::$cookiePassword])) {
				$credentials;
				$credentials["username"] = $_COOKIE[self::$cookieName];
				$credentials["cookiePassword"] = $_COOKIE[self::$cookiePassword];
				$result = $credentials;
			} else {
				$result = false;
			}

			return $result;
		}	

		public function removeCookieCredentials() {
			setcookie(self::$cookieName, "", time()-3600);
			setcookie(self::$cookiePassword, "", time()-3600);
		}

		public function cookieCredentialsArePresent() {
			return isset($_COOKIE[self::$cookieName]) && isset($_COOKIE[self::$cookiePassword]);
		}	
    
    	private function getRequestUserName()
    	{
        	return isset($_POST[self::$name]) ? $_POST[self::$name] : '' ;
    	}

		private function getRequestPassword()
    	{
        	return isset($_POST[self::$password]) ? $_POST[self::$password]: '' ;
    	}


  		public function userWantsToLogin() {
    		return isset($_POST[self::$login]);
		}

		public function userWantsToLogout() {
  			return isset($_POST[self::$logout]);
  		}

	}
?>