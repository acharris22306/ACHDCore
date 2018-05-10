<?php
namespace ACHD\Core
class Validation{

	public static $blacklistedEmails = array(
		"/^s\.?e\.?r\.?v\.?i\.?c\.?d\.?1\.?5\.?7\.?6\.?@gmail\.com$/",
		"/^u\.?l\.?t\.?i\.?m\.?a\.?t\.?e\.?l\.?y\.?y\.?n\.?e\.?f@gmail\.com$/",
		"/^[a-zA-Z0-9_\.]+@yandex\.com$/",
		"/^m\.?o\.?n\.?c\.?l\.?e\.?r\.?j\.?a\.?s\.?5\.?8\.?8\.?@gmail\.com$/"
	);

	public static $errors = array();
	public static $instance;
	function __construct(){
		if(empty(self::$instance)){
			self::$instance = new self;
		}
		return self::$instance;
	}
	public static function fieldnameAsText($fieldname){
		$fields = array(
			"firstName" => "First Name",
			"lastName" => "Last Name",
			"email" => "Email",
			"contactPurpose" =>"I am contacting to...",

			"confirmUsername" => "Confirm Username",

			"firstLastNames" => "First and Last Names",

			"usernameAuthorizer" => "Authorizing Admins Username",
			"passwordAuthorizer" => "Authorizing Admins Password",
	
			"termsAgreement" => "Terms of Use Agreement",
	
			"dateOfBirth" => "Date of Birth",

			"message" => "Message",
			"profilePic"=> "Please Upload your profile picture",
			"profilePicture"=> "Please Upload your profile picture",
			"likeAboutUnfused" => "What I Like About the site",
			 "areasOfImprovement" => "What Needs Improvement",
			 "rating" => "Rating For the site",
			 "recommendUnfused" => "Would Recommend the site",
		//	 "amountHelped" => "Amount Helped By the site",
	
		);
		if ($fields[$fieldname]) {
			$fieldNameAsText = $fields[$fieldname];
		} else {
			$fieldNameAsText = ucwords(CoreFunctions::parseCamelCase($fieldname));
		}
		return $fieldNameAsText;
	}
	// * presence
	// use trim() so empty spaces don't count
	// use === to avoid false positives
	// empty() would consider "0" to be empty
	public static function hasPresence($value){
		return isset($value) && $value !== "";
	}
	public static function validatePresences($requiredFields){
		global $errors;
		foreach ($requiredFields as $field) {
			$value     = trim($_POST[$field]);
			$fieldName = self::fieldnameAsText($field);
			if (!self::hasPresence($value)) {
				$errors[$field] = "{$fieldName} is required and can't be blank";
			}
		}
	}
	// * string length
	// max length
	public static function hasMaxLength($value, $max){
		return strlen($value) <= $max;
	}

	public static function validateSelectVals($selectFieldsAndVals){
		global $errors;
		foreach ($selectFieldsAndVals as $field=>$allowed) {
			$value     = trim($_POST[$field]);
			$fieldName = self::fieldnameAsText($field);
			$allowOthers = $allowed['allowed'];
			$allowedValues=$allowed['vals'];
			if($allowOthers[$field]=="specified"){
				if((!self::hasInclusionIn($value,$allowedValues))){
					$errors[$field] = "{$fieldName} is not a valid entry.";
				}
			}else if ($allowOthers[$field]=="number"){
				if(!self::isNumeric($value)){
					$errors[$field] = "{$fieldName} is not a valid entry.";
				}
			}
		}
	
	}
	public static function validateRadioCheckboxVals($radioCheckboxFieldsAndVals,$fieldTypes){
		global $errors;
		foreach ($radioCheckboxFieldsAndVals as $field=>$allowed) {
			$value     = trim($_POST[$field]);
			$fieldName = self::fieldnameAsText($field);
			if($fieldTypes[$field]=="radio"){
				if((!self::hasInclusionIn($value,$allowed))){
					$errors[$field] = "{$fieldName} is not a valid entry.";
				}
			}elseif($fieldTypes[$field]=="checkbox"){
				foreach($value as $val){
					if((!self::hasInclusionIn($val,$allowed))){
						$errors[$field] = "{$fieldName} does not include all valid entrys.";
					}
				}
			}
		
		}
	
	}
	public static function validateMaxLengths($fieldsWithMaxLengths){
		global $errors;
		// Expects an assoc. array
		foreach ($fieldsWithMaxLengths as $field => $max) {
			$value     = trim($_POST[$field]);
			$fieldName = fieldnameAsText($field);
			if((!self::hasInclusionIn($field,$requiredFields))&&(!self::hasPresence($value))){
				CoreFunctions::logAction("formDataTest","notIncludedInTest",$fieldName);
			}else if (!self::hasMaxLength($value, $max)) {
				$errors[$field] = "{$fieldName} is too long. {$fieldName} must be less than {$max} characters long.";
			}
		}
	}
	public static function validatePhoneNumbers($phones,$requiredFields){
		global $errors;
		// Expects an assoc. array
		foreach ($phones as $field) {
			$value     = trim($_POST[$field]);
			$fieldName = fieldnameAsText($field);
			if((!self::hasInclusionIn($field,$requiredFields))&&(!self::hasPresence($value))){
				CoreFunctions::logAction("formDataTest","notIncludedInTest",$fieldName);
			}else if (!self::isValidPhone($value)) {
				$errors[$field] = "{$fieldName} must be either in 'xxx-xxx-xxxx' or '(xxx)-xxx-xxxx' or 'xxxxxxxxxx' or 'xxx xxx xxxx' format.";
			}
		}
	}
	public static function validatePasswordSecurity($passwords,$requiredFields){
		global $errors;
		//global $requiredFields;
		// Expects an assoc. array
		foreach ($passwords as $field) {
			$value     = trim($_POST[$field]);
			$fieldName = fieldnameAsText($field);
			if((!self::hasInclusionIn($field,$requiredFields))&&(!self::hasPresence($value))){
				CoreFunctions::logAction("formDataTest","notIncludedInTest",$fieldName);
			}else if (!self::isValidPassword($value)) {
				$errors[$field] = "{$fieldName} must be at least 6 letters long.";
			}
		}
	}
	public static function hasMinLength($value, $min){
		return (strlen($value) >= $min);
	}
	public static function isNumeric($value){
		return (is_numeric($value));
	}
	public static function isValidEmail($value){
		global $blacklistedEmails;
		$regex = "/^[a-zA-Z0-9_\.]+@[a-zA-Z0-9_\.]+\.[a-zA-Z0-9_\.]{2,}$/";
		if (preg_match($regex, $value)||(SpamEmail::isSpam($value))){
		//if (filter_var($value, FILTER_VALIDATE_EMAIL)){
			foreach($blacklistedEmails as $email){
				if (preg_match($email, $value)){
					$message       = "Blocked: " . $value;
					CoreFunctions::logAction("security", "Blacklisted Emails", $message);
					return false;
				}else{
					return true;
				}
			}
		}else{
			return false;
		}
		return true;
	}
	public static function isValidPhone($value){
		$regex = "/^((\(\d{3}\))|(\d{3}))([- ]?)\d{3}([- ]?)\d{4}$/";
		if (preg_match($regex, $value)) {
			return true;
		} else {
			return false;
		}
	}
	public static function isValidPassword($value){
		$regex = "/^\S{8,}$/";
		if (preg_match($regex, $value)) {
			return true;
		} else {
			return false;
		}
	}
	public static function verifyUsernameUnique($username){
		if (!User::findByUsername($username)) {
			return true;
		} else {
			return false;
		}
	}
	public static function validateMinLengths($fieldsWithMinLengths,$requiredFields){
		global $errors;
		//global $requiredFields;
		// Expects an assoc. array
		foreach ($fieldsWithMinLengths as $field => $min) {
			$value     = trim($_POST[$field]);
			$fieldName = fieldnameAsText($field);
			if((!self::hasInclusionIn($field,$requiredFields))&&(!self::hasPresence($value))){
				CoreFunctions::logAction("formDataTest","notIncludedInTest",$fieldName);
			}else if (!hasMinLength($value, $min)) {
					$errors[$field] = "{$fieldName} is too short. {$fieldName} must be more than {$min} characters long.";
			}
		}
	}
	public static function validateNumerics($numericInputs,$requiredFields){
		global $errors;
		// Expects an assoc. array
		foreach ($numericInputs as $field) {
			$value     = trim($_POST[$field]);
			$fieldName = self::fieldnameAsText($field);
			if((!self::hasInclusionIn($field,$requiredFields))&&(!self::hasPresence($value))){
				CoreFunctions::logAction("formDataTest","notIncludedInTest",$fieldName);
			}else if (!self::isNumeric($value)) {
				$errors[$field] = "{$fieldName} must be a number.";
			}
		}
	}
	public static function validateNumericsAssoc($numericInputs,$requiredFields){
		global $errors;
		// Expects an assoc. array
		foreach ($numericInputs as $field=>$other) {
			$value     = trim($_POST[$field]);
			$fieldName = self::fieldnameAsText($field);
			if((!self::hasInclusionIn($field,$requiredFields))&&(!self::hasPresence($value))){
				CoreFunctions::logAction("formDataTest","notIncludedInTest",$fieldName);
			}else if (!self::isNumeric($value)) {
				$errors[$field] = "{$fieldName} must be a number.";
			}
		}
	}
	public static function validateEmails($emailInputs,$requiredFields){
		global $errors;
		// Expects an assoc. array
		foreach ($emailInputs as $field) {
			$value     = trim($_POST[$field]);
			$fieldName = self::fieldnameAsText($field);
			if (!self::isValidEmail($value)) {
				$errors[$field] = "{$fieldName} must be a valid email address.";
			}
		}
	}
	// * inclusion in a set
	public static function hasInclusionIn($value, $set){
		if (is_array($set)) {
			return in_array($value, $set);
		} else {
			return false;
		}
	}
	public static function confirmMatches($field, $confirm){
		global $errors;
		$fieldVal = trim($_POST[$field]);
		$confirmVal = trim($_POST[$confirm]);
		if ($fieldVal != $confirmVal) {
			$fieldTitle = self::fieldnameAsText($field);
			$confirmTitle = self::fieldnameAsText($confirm);
			$errors[$field] = $fieldTitle." and ". $confirmTitle." must match";
		}
	}
	public static function validateConfirmMatches($confirmFields){
		foreach($confirmFields as $field =>$confirm){
			self::confirmMatches($field, $confirm);
		}
	
	}

	public static function formErrors(){
		$output = "";
		if (!empty($errors)) {
			$output .= "<div>";
			$output .= "<h2>Please fix the following errors:</h2>";
			foreach ($errors as $key => $error) {
				//	$fieldName = fieldnameAsText($key);
				$output .= "<p>";
				//	$output .= $fieldName;
				//$output .= ": ";
				$output .= htmlentities($error, ENT_COMPAT, 'utf-8');
				$output .= "</p>";
			}
			$output .= "</div>";
		}
		return $output;
	}
	public static function customFormError($field, $msg){
		$fieldName = self::fieldnameAsText($field);
		$errors[$field] = $fieldName." ".$msg;
	}
}
