<?php
// A class to help work with Sessions
// In our case, primarily to manage logging users in and out
// Keep in mind when working with sessions that it is generally 
// inadvisable to store DB-related objects in sessions
namespace ACHD\Core\UserControl
class Session{
	private $loggedIn;
	public $userId;
	public $userType;
	public $message;
	public $confirmation;
	private static $validUserTypes=array('admin');
	function __construct(){
		session_start();
		$this->checkMessage();
		$this->checkConfirmation();
		$this->checkLogin();
		if ($this->loggedIn) {
			// actions to take right away if user is logged in
		} else {
			// actions to take right away if user is not logged in
		}
	}
	public static function isLoggedIn(){
		return $_SESSION['loggedIn'];
	}
	public function login($user){
		// database should find user based on username/password
		if ($user) {
			$this->userId   = $_SESSION['userId'] = $user->userId;
			$this->userType = $_SESSION['userType'] = $user->userType;
			$this->loggedIn = $_SESSION['loggedIn'] = true;
		}
		$this->confirmation("you have successfully logged in");
		self::afterSuccessfulLogin();
	}
	public function loginNewUser($user){
		// database should find user based on username/password
		if ($user) {
			$this->userId   = $_SESSION['userId'] = $user->userId;
			$this->userType = $_SESSION['userType'] = $user->userType;
			$this->loggedIn = $_SESSION['loggedIn'] = true;
		}
		$this->confirmation("You are now registered and you have successfully logged in");
		self::afterSuccessfulLogin();
	}
	public function logout(){
		unset($_SESSION['userId']);
		unset($this->userId);
		$_SESSION['confirmation'] = "You have successfully logged out";
		self::afterSuccessfulLogout();
	}
	public static function validateUserAccess($userType){
		global $session;
		global $validUserTypes;
		if (isset($userType)) {
			if (in_array($userType,$validUserTypes)) {
				if (isset($session->userType)) {
					if (!($session->userType == $userType)) {
						return false;
					} else if (($session->userType == $userType)) {
						return true;
					}
				} else {
					return false;
				}
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
	public static function validateUserAccessMultiAccess($userTypes){
		global $session;
		if (isset($userTypes)) {
			$isValidUserType = false;
			foreach ($userTypes as $userType){
				if ((in_array($userType,$validUserTypes)) {
					if (isset($session->userType)) {
						if (($session->userType == $userType)) {
							$isValidUserType = true;
							//return $isValidUserType;
						}
					} else {
						$isValidUserType = false;
						//return $isValidUserType;
					}
				} else {
					$isValidUserType = true;
					//return $isValidUserType;
				}
			}
			return $isValidUserType;
		} else {
			$isValidUserType = false;
			return $isValidUserType;
		}
		return $isValidUserType;
	}
	public function message($msg = ""){
		if (!empty($msg)) {
			// then this is "set message"
			// make sure you understand why $this->message=$msg wouldn't work
			$_SESSION['message'] = $msg;
		} else {
			// then this is "get message"
			return $this->message;
		}
	}
	public function confirmation($msg = ""){
		if (!empty($msg)) {
			// then this is "set message"
			// make sure you understand why $this->message=$msg wouldn't work
			$_SESSION['confirmation'] = $msg;
		} else {
			// then this is "get message"
			return $this->confirmation;
		}
	}
	public function debug($msg = ""){
		if (!empty($msg)) {
			// then this is "set message"
			// make sure you understand why $this->message=$msg wouldn't work
			$_SESSION['debug'] = $msg;
		} else {
			// then this is "get message"
			return $this->debug;
		}
	}
	private function checkLogin(){
		if (isset($_SESSION['userId'])) {
			$this->userId   = $_SESSION['userId'];
			$this->userType = $_SESSION['userType'];
			$this->loggedIn = true;
		} else {
			unset($this->userId);
			$this->loggedIn = false;
		}
	}
	private function checkMessage(){
		// Is there a message stored in the session?
		if (isset($_SESSION['message'])) {
			// Add it as an attribute and erase the stored version
			$this->message = $_SESSION['message'];
			unset($_SESSION['message']);
		} else {
			$this->message = "";
		}
	}
	private function checkConfirmation(){
		// Is there a message stored in the session?
		if (isset($_SESSION['confirmation'])) {
			// Add it as an attribute and erase the stored version
			$this->confirmation = $_SESSION['confirmation'];
			unset($_SESSION['confirmation']);
		} else {
			$this->confirmation = "";
		}
	}
	private function checkDebug(){
		// Is there a message stored in the session?
		if (isset($_SESSION['debug'])) {
			// Add it as an attribute and erase the stored version
			$this->debug = $_SESSION['debug'];
			unset($_SESSION['debug']);
		} else {
			$this->debug = "";
		}
	}
	public static function endSession(){
		// Use both for compatibility with all browsers
		// and all versions of PHP.
		if (isset($this)) {
			session_unset();
		}
		if (isset($this)) {
			session_destroy();
		}
		redirectTo(SITE_ROOT);
	}
	// Does the request IP match the stored value?
	private static function requestIpMatchesSession(){
		// return false if either value is not set
		if (!isset($_SESSION['ip']) || !isset($_SERVER['REMOTE_ADDR'])) {
			return false;
		}
		if ($_SESSION['ip'] === $_SERVER['REMOTE_ADDR']) {
			return true;
		} else {
			return false;
		}
	}
	// Does the request user agent match the stored value?
	private static function requestUserAgentMatchesSession(){
		// return false if either value is not set
		if (!isset($_SESSION['user_agent']) || !isset($_SERVER['HTTP_USER_AGENT'])) {
			return false;
		}
		if ($_SESSION['user_agent'] === $_SERVER['HTTP_USER_AGENT']) {
			return true;
		} else {
			return false;
		}
	}
	// Has too much time passed since the last login?
	private static function lastLoginIsRecent(){
		$maxElapsed = 60 * 60 * 24; // 1 day
		// return false if value is not set
		if (!isset($_SESSION['last_login'])) {
			return false;
		}
		if (($_SESSION['last_login'] + $maxElapsed) >= time()) {
			return true;
		} else {
			return false;
		}
	}
	// Should the session be considered valid?
	private static function isSessionValid(){
		$checkip        = true;
		$checkuseragent = true;
		$checklastlogin = true;
		if ($checkip && !self::requestIpMatchesSession()) {
			return false;
		}
		if ($checkuseragent && !self::requestUserAgentMatchessession()) {
			return false;
		}
		if ($checklastlogin && !self::lastLoginIsRecent()) {
			return false;
		}
		return true;
	}
	// If session is not valid, end and redirect to login page.
	private static function confirmSessionIsvalid(){
		if (!(self::isSessionValid())) {
			self::endSession();
			// Note that header redirection requires output buffering 
			// to be turned on or requires nothing has been output 
			// (not even whitespace).
			return false;
		} else {
			return true;
		}
	}
	// If user is not logged in, end and redirect to login page.
	private static function confirmUserLoggedin(){
		if (!self::isLoggedIn()) {
			self::endSession();
			// Note that header redirection requires output buffering 
			// to be turned on or requires nothing has been output 
			// (not even whitespace).
			return false;
		} else {
			return true;
		}
	}
	public function debugVars($varsToDebug){
		if (!(empty($varsToDebug))) {
			$debug = "Var Dump: \n";
			foreach ($varsToDebug as $var) {
				$debug .= "<div class=\"spacer1em\"></div>";
				$debug .= var_dump($var);
				$debug .= "<div class=\"spacer1em\"></div>";
			}
			$this->debug($debug);
		}
	}
	// Actions to preform after every successful login
	private static function afterSuccessfulLogin(){
		// Regenerate session ID to invalidate the old one.
		// Super important to prevent session hijacking/fixation.
		session_regenerate_id();
		// Save these values in the session, even when checks aren't enabled 
		$_SESSION['ip']         = $_SERVER['REMOTE_ADDR'];
		$_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$_SESSION['last_login'] = time();
	}
	// Actions to preform after every successful logout
	private static function afterSuccessfulLogout(){
		$_SESSION['loggedIn'] = false;
		self::endSession();
	}
	// Actions to preform before giving access to any 
	// access-restricted page.
	public static function beforeEveryProtectedPage($userType, $permitGuestsAccessToProtectedPage = false){
		$message = "Tried to access " . $userType . " Pages";
		if ($permitGuestsAccessToProtectedPage == true){
			return true;
		}else{
			
			if (self::confirmUserLoggedin()) {
				if (self::confirmSessionIsValid()) {
					if (self::validateUserAccess($userType)) {
						return true;
					} else {
						logAction("userAccessManagement", "Attempt to Access Protected Page", $message);
						return false;
					}
				} else {
					logAction("userAccessManagement", "Attempt to Access Protected Page", $message);
					return false;
				}
			} else {
				logAction("userAccessManagement", "Attempt to Access Protected Page", $message);
				return false;
			}
		}
	}
	
	public static function beforeEveryProtectedPageMultiAccess($userTypes, $permitGuestsAccessToProtectedPage = false){
		$message = '';
		/*foreach ($userTypes as $userType){
			$message .= "Tried to access " . $userType . " Pages\n";
			if (self::beforeEveryProtectedPage($userType)){
				$isValidUserType = true;
				
			}
		}
		if (($isValidUserType) && isset($isValidUserType)){
			return true;
		}
		*/
		if ($permitGuestsAccessToProtectedPage == true){
			return true;
		}else{
			if (self::confirmUserLoggedin()) {
				if (self::confirmSessionIsValid()) {
					if (self::validateUserAccessMultiAccess($userTypes)) {
						return true;
					} else {
						logAction("userAccessManagement", "Attempt to Access Protected Page", $message);
						return false;
					}
				} else {
					logAction("userAccessManagement", "Attempt to Access Protected Page", $message);
					return false;
				}
			} else {
				logAction("userAccessManagement", "Attempt to Access Protected Page", $message);
				return false;
			}
		}
	}
}
$session      = new Session();
$confirmation = $session->confirmation();
$debug        = $session->debug();
$message      = $session->message();
			
function outputPHPMessages(){
	global $message;
	if ($message != "") {
		$output = "<div id=\"phpMessages\">";
		$output .= $message;
		$output .= "</div>";
		unset($message);
		echo $output;
	}
}
function outputPHPConfirmations(){
	global $confirmation;
	if ($confirmation != "") {
		$output = "<div id=\"phpConfirm\">";
		$output .= $confirmation;
		$output .= "</div>";
		unset($confirmation);
		echo $output;
	}
}
function outputPHPDebugging(){
	global $inDebugMode;
	global $debug;
	if ($inDebugMode) {
		$output = "<div id=\"phpDebug\">";
		$output .= "<h2>Currently In Debug Mode</h2>";
		if ($debug != "") {
			$output .= $debug;
			$output .= "</div>";
			unset($debug);
		} else {
			$output .= "</div>";
		}
		echo $output;
	}
}
