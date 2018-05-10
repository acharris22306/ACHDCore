<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
namespace ACHD\Core\UserControl
class User extends \ACHD\Core\Database\ActiveRecordBase{
	protected static $tableName = "users";
	protected static $dbFields = array('userId', 'username', 'hashedPassword', 'userType', 'firstName','lastName','email');
	protected static $idName = "userId";
	public $userId;
	public $username;
	public $hashedPassword;
	public $userType;
	public $firstName;
	public $lastName;
	public $email;
	public static function authenticate($username = "", $password = ""){
		global $database;
		global $passwordHashOptions;
		$username = $database->escapeValue($username);
		$password = $database->escapeValue($password);
		$user     = self::findByUsername($username);
		if (!empty($user)) {
			$existingHashedPassword = $user->hashedPassword;
			if (password_verify($password,$existingHashedPassword)) {
				$passwordNeedsRehash = password_needs_rehash($existingHashedPassword,PASSWORD_HASH_METHOD,$passwordHashOptions);
				if($passwordNeedsRehash == true){
					$rehashedPw= CoreFunctions::passwordEncrypt($password);
					$user->hashedPassword = $rehashedPw;
					$user->update();
				}
				return $user;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	public static function loginNewUser($newUsername, $password){
		global $session;
		global $validUserTypes;
		// Check database to see if username/password exist.
		$foundUser = self::authenticate($newUsername, $password);
		if ($foundUser) {
			$session->loginNewUser($foundUser);
			//	$_SESSION['message'] = var_dump($foundUser);
			if (!isset($foundUser->userId)) {
				$_SESSION['message'] .= " Error getting userId";
			}
			if (!isset($foundUser->username)) {
				$_SESSION['message'] .= " Error getting username";
			}
			if (!isset($foundUser->userType)) {
				$_SESSION['message'] .= " Error getting userType";
			}
			$_SESSION['userId']   = $foundUser->userId;
			$_SESSION['userType'] = $foundUser->userType;
			if (isset($target)) {
				CoreFunctions::redirectTo($target);
			} elseif (in_array($foundUser->userType,$validUserTypes)) {
				CoreFunctions::redirectTo(SITE_ROOT . $foundUser->userType."s/".$foundUser->userType."Dashboard.php");
			} else{
				CoreFunctions::returectTo(SITE_ROOT);
			}
		} else {
			// username/password combo was not found in the database
			$message = "Username/password combination not found.";
		}
	}
	public static function redirectionSignin($userType){
		global $validUserTypes;
		if (in_array($userType,$validUserTypes)) {
			CoreFunctions::redirectTo(SITE_ROOT . $userType."s/".$userType."Signin.php");
		} else{
			CoreFunctions::returectTo(SITE_ROOT."login.php");
		}
	}
	public static function redirectionDashboard($userType){
		global $validUserTypes;
		if (in_array($userType,$validUserTypes)) {
			CoreFunctions::redirectTo(SITE_ROOT . $userType."s/".$userType."Dashboard.php");
		} else{
			CoreFunctions::returectTo(SITE_ROOT);
		}
	}
	
	public static function findAllByLastFirst(){
		return self::findBySql("SELECT * FROM users ORDER BY lastName, firstName ASC;");
	}
	public static function findAllByLastFirstForType($userType){
		return self::findBySql("SELECT * FROM users WHERE userType='{$userType}' ORDER BY lastName, firstName ASC;");
	}
	public static function findAllByLastFirstForTypeAddedBetween($userType, $start, $end){
		global $database;
		$usertypeTable = $userType."s";
		$usertypeId = $userType."Id";
		$sql       = "SELECT userId FROM {$usertypeTable} WHERE {$usertypeId}>='{$start}' AND {$usertypeId}<'{$end}'";
		return self::findBySql("SELECT * FROM users WHERE userType='{$userType}' AND userId=ANY({$sql}) ORDER BY lastName, firstName ASC;");
	}
	public static function findAllByUserId(){
		return self::findBySql("SELECT * FROM users ORDER BY userId ASC;");
	}

	public static function findByUsername($username){
		$resultArray = self::findBySql("SELECT * FROM users WHERE username = '{$username}' LIMIT 1;");
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

	public static function findIdByUsername($username){
		$resultArray = self::findBySql("SELECT userId FROM users WHERE username = '{$username}' LIMIT 1;");
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}
	public static function findIdByFirstLastNames($firstName, $lastName){
		global $database;
		$sql       = "SELECT userId FROM users WHERE firstName = '{$firstName}' AND lastName = '{$lastName}' LIMIT 1;";
		$resultSet = $database->query($sql);
		$row       = $database->fetchArray($resultSet);
		$database->freeResult($resultSet);
		return array_shift($row);
	}
	public function validateUniqueAccount(){
		$where       = "email = '{$this->email}' OR username='{$this->username}'";
		$numAccount = self::countIf($where);
		$isUnique;
		if(isset($this->userId)){
			$isUnique = ($numAccount>1)?false:true;
		}else{
			$isUnique = ($numAccount>0)?false:true;
		}
		return $isUnique;
	}
	public function create(){
		if($this->validateUniqueAccount()){
			return parent::create();
		}else{
			return false;
		}
	}
	public function deleteUser(){
		global $database;
		$userType=$this->userType;
		$className = ucwords($userType);
		$entryToDelete = $className::findByUserId($this->userId);
		try{
			$database->beginTransaction();
			if ($entryToDelete->delete()&& $this->delete()) {
				$database->commit();
				return true;
			} else {
				$database->rollBack();
				return false;
			}
		}catch(Exception $e){
			$database->rollBack();
			logAction("errors", "deleteUser", $e->getMessage());
			return false;
		}
	}
	
	public function createUser($usr){
		global $database;
		try{
			$database->beginTransaction();
			if($this->create()){
				$entryId = $this->getId();
				$usr->userId=$entryId;
				if($usr->create()){
					$database->commit();
					return true;
				} else {
					$database->rollBack();
					return false;
				}
			} else {
				$database->rollBack();
				return false;
			}
			
		}catch(Exception $e){
			$database->rollBack();
			CoreFunctions::logAction("errors", "createUser", $e->getMessage());
			return false;
		}
	}

	public static function countAllAdmins(){
		$where       = "userType='admin'";
		return self::countIf($where);
	}

	public static function isValidUserForType($id,$type){
		$where = "userType='{$type}' AND userId='{$id}'";
		$numUsers =  self::countIf($where);
		return ($numUsers>0)?true:false;
		
	}
	
}
