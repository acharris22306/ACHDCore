<?php
namespace ACHD\Core
class Email{
	public static $instance;
	private static $host;
	private static $SMTPAuth = false;
	private static $port = 587;
	private static $wordWrap = 70;
	private static $SMTPSecure = "tls";
	private static $username;
	private static $password;
	private static $companyEmails=array(
		"Support"=>"support@replaceMe.org",
		"Registration"=>"registration@replaceMe.org",
	);
	function __construct(){
		if(empty(self::$instance)){
			self::$instance = new self;
		}
		return self::$instance;
	}
	function tryToSendEmailReset($entry, $email, $token){
		$mail     = new \PHPMailer\PHPMailer\PHPMailer();
		$url      = SITE_ROOT . "changePassword.php?token=" . $token;
		$fullName = $entry->firstName . " " . $entry->lastName;
		//$mail->IsSMTP();
		$mail->Host       = self::$host;
		$mail->Port       = self::$port;
		$mail->SMTPAuth   = self::$SMTPAuth;
		$mail->Username   = self::$username;
		$mail->WordWrap   = self::$wordWrap;
		$mail->Password   = self::$password;
		$mail->SMTPSecure = self::$SMTPSecure;
		$mail->FromName   = "Support";
		$mail->From       = self::$companyEmails["Support"];
		$mail->addAddress($email, $fullName);
		$mail->Subject = "Password Reset";
		$mail->Body    = "We have received a password change request. You can use the link below to change your password.
							\n\n" . $url . "\n\nFor your security, this link will expire one (1) week from now.\n\nIf you did not make this request, you do not need to take any action. Your password will not be changed without clicking the above link.";
	
		$result        = $mail->Send();
		$message       = $email. " ";
		if(!$result) {
		$message .=  'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
		} 
		CoreFunctions::logAction("emails", "Password Reset Request", $message);
		return $result;
	}
	function sendRegistrationConfirmation($user, $email){
		$mail     = new \PHPMailer\PHPMailer\PHPMailer();
		$username = $user->username;
		$userType = $user->userType;

		$fullName = $user->firstName . " " . $user->lastName;
		//$mail->IsSMTP();
		$mail->Host       = self::$host;
		$mail->Port       = self::$port;
		$mail->SMTPAuth   = self::$SMTPAuth;
		$mail->Username  = self::$username;
		$mail->WordWrap   = self::$wordWrap;
		$mail->Password   = self::$password;
		$mail->SMTPSecure = self::$SMTPSecure;
		$mail->FromName   = "Registration";
		$mail->From       = self::$companyEmails["Registration"];
		$mail->addAddress($email, $fullName);
		$mail->Subject = "Account Creation";
		$mail->Body    = "Thank you for registering! You have been registered with the username {$username}. Please Keep this email for your records.";
		$result        = $mail->Send();
		$message       = $email. " ";
		if(!$result) {
		$message .=  'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
		} 
		CoreFunctions::logAction("emails", "Registration Confirmation Sent", $message);
		return $result;
	}

	public static function emailResetToken($username){
		global $database;
		$user  = User::findByUsername($username);
		$email = $database->query("SELECT email FROM passwordResetRequests WHERE username='{$username}' ORDER BY passwordResetId DESC LIMIT 1;");
		$email = $database->fetchArray($email);
		$email = array_shift($email);
		$token = $database->query("SELECT resetToken FROM passwordResetRequests WHERE username='{$username}' ORDER BY passwordResetId DESC LIMIT 1 ;");
		$token = $database->fetchArray($token);
		$token = array_shift($token);
		if ($user) {
		
			self::tryToSendEmailReset($user, $email, $token);
			// and send an email with a URL that includes the token.
			return true;
		} else {
			return false;
		}
	}
}
