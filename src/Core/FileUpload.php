<?php
// Form upload functions
namespace ACHD\Core
class FileUpload{
// Configuration
// Use MAX_FILE_SIZE in your form but don't trust it.
// Check it again in your application
	public static $instance;
	function __construct(){
		if(empty(self::$instance)){
			self::$instance = new self;
		}
		return self::$instance;
	}
	public static $maxFileSize     = 2048576; // 1 MB expressed in bytes
	// Where to store uploaded files?
	// Choose a directory outside of the public path, unless the file 
	// should be publicly visible/accessible.
	// Examples:
	//   job application => private
	//   website profile photo => public
	// Of course, when outside the public path, you need PHP code that can
	// access those files. The browser can't access them directly.
	// Define allowed filetypes to check against during validations
	public static $imageTypes      = array(
		'image/png',
		'image/gif',
		'image/jpg',
		'image/jpeg'
	);
	public static $imageExtensions = array(
		'png',
		'gif',
		'jpg',
		'jpeg'
	);
	public static $docTypes        = array(
		'application/pdf',
		'application/msword',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/vnd.oasis.opendocument.text', 
		'application/rtf',
		'text/plain',
		'image/png',
		'image/gif',
		'image/jpg',
		'image/jpeg'
	);
	public static $docExtensions   = array(
		'png',
		'gif',
		'jpg',
		'jpeg',
		'pdf',
		'doc',
		'docx',
		'odt',
		'rtf',
		'txt'
	);
	public static $projectFileType = array(
		'application/pdf',
		'application/msword',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/vnd.oasis.opendocument.text', 
		'application/rtf',
		'text/plain',
		'image/png',
		'image/gif',
		'image/jpg',
		'image/jpeg',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'application/zip',
		'application/vnd.ms-powerpoint',
		'application/vnd.ms-excel',
		'text/rtf',
		'audio/mpeg3', 
		'audio/wav',
		'video/quicktime',
		'video/avi',
		'image/psd',
		'video/mpeg',
		'application/postscript',
		'application/x-zip-compressed',
		'multipart/x-zip',
		'application/x-compressed'
	
	);
	public static $projectFileExtensions = array(
		'png',
		'gif',
		'jpg',
		'jpeg',
		'pdf',
		'doc',
		'docx',
		'odt',
		'rtf',
		'txt',
		'mov',
		'avi',
		'mp4',
		'mp3',
		'psd',
		'ppt',
		'pptx',
		'wav',
		'xls',
		'xlsx',
		'zip',
		'mpg',
		'mpeg',
		'ai',
		'eps'
	);
	// Provides plain-text error messages for file upload errors.
	public static function fileUploadError($errorInteger){
		$uploadErrors = array(
			// http://php.net/manual/en/features.file-upload.errors.php
			UPLOAD_ERR_OK => "No errors.",
			UPLOAD_ERR_INI_SIZE => "Larger than upload_max_filesize.",
			UPLOAD_ERR_FORM_SIZE => "Larger than form MAX_FILE_SIZE.",
			UPLOAD_ERR_PARTIAL => "Partial upload.",
			UPLOAD_ERR_NO_FILE => "No file.",
			UPLOAD_ERR_NO_TMP_DIR => "No temporary directory.",
			UPLOAD_ERR_CANT_WRITE => "Can't write to disk.",
			UPLOAD_ERR_EXTENSION => "File upload stopped by extension."
		);
		return $uploadErrors[$errorInteger];
	}
	// Sanitizes a file name to ensure it is harmless
	public static function sanitizeFileName($filename){
		// Remove characters that could alter file path.
		// I disallowed spaces because they cause other headaches.
		// "." is allowed (e.g. "photo.jpg") but ".." is not.
		$filename = preg_replace("/([^A-Za-z0-9_\-\.]|[\.]{2})/", "", $filename);
		// basename() ensures a file name and not a path
		$filename = basename($filename);
		return $filename;
	}
	// Returns the file permissions in octal format.
	public static function filePermissions($file){
		// fileperms returns a numeric value
		$numericPerms = fileperms($file);
		// but we are used to seeing the octal value
		$octalPerms   = sprintf('%o', $numericPerms);
		return substr($octalPerms, -4);
	}
	// Returns the file extension of a file
	public static function fileExtension($file){
		$pathParts = pathinfo($file);
		return $pathParts['extension'];
	}
	// Searches the contents of a file for a PHP embed tag
	// The problem with this check is that file_get_contents() reads 
	// the entire file into memory and then searches it (large, slow).
	// Using fopen/fread might have better performance on large files.
	public static function fileContainsPhp($file){
		$contents = file_get_contents($file);
		$position = strpos($contents, '<?php');
		return $position !== false;
	}
	// Runs file being uploaded through a series of validations.
	// If file passes, it is moved to a permanent upload directory
	// and its execute permissions are removed.
	public static function uploadFile($fieldName, $uploadPath, $maxFileSize, $allowedMimeTypes, $allowedExtensions, $CustomFileName, $checkIsImage){
		if (isset($_FILES[$fieldName])) {
			// Sanitize the provided file name.
			$fileExtension = fileExtension($fileName);
			$fileName      = "{$CustomFileName}.{$fileExtension}";
			// Even more secure to assign a new name of your choosing.
			// Example: 'file_536d88d9021cb.png'
			// $unique_id = uniqid('file_', true); 
			// $new_name = "{$unique_id}.{$file_extension}";
			$fileType      = $_FILES[$fieldName]['type'];
			$tmpFile       = $_FILES[$fieldName]['tmp_name'];
			$error         = $_FILES[$fieldName]['error'];
			$fileSize      = $_FILES[$fieldName]['size'];
			// Prepend the base upload path to prevent hacking the path
			// Example: $file_name = '/etc/passwd' becomes harmless
			$filePath      = $uploadPath . $fileName;
			if ($error > 0) {
				// Display errors caught by PHP
				Validation::$errors[$fieldName] = "Error: " . self::fileUploadError($error);
			} elseif (!is_uploaded_file($tmpFile)) {
				Validation::$errors[$fieldName] = "Error: Does not reference a recently uploaded file.";
			} elseif ($fileSize > $maxFileSize) {
				// PHP already first checks php.ini upload_max_filesize, and 
				// then form MAX_FILE_SIZE if sent.
				// But MAX_FILE_SIZE can be spoofed; check it again yourself.
				Validation::$errors[$fieldName] = "Error: File size is too big.";
			} elseif (!in_array($fileType, $allowedMimeTypes)) {
				Validation::$errors[$fieldName] = "Error: Not an allowed mime type.";
			} elseif (!in_array($fileExtension, $allowedExtensions)) {
				// Checking file extension prevents files like 'evil.jpg.php' 
				Validation::$errors[$fieldName] = "Error: Not an allowed file extension";
			} elseif ($checkIsImage && (getimagesize($tmpFile) === false)) {
				// getimagesize() returns image size details, but more importantly,
				// returns false if the file is not actually an image file.
				// You obviously would only run this check if expecting an image.
				Validation::$errors[$fieldName] = "Error: Not a valid image file";
			} elseif (self::fileContainsPhp($tmpFile)) {
				// A valid image can still contain embedded PHP.
				Validation::$errors[$fieldName] = "Error: File contains PHP code.";
			} elseif (file_exists($filePath)) {
				// if destination file exists it will be over-written
				// by move_uploaded_file()
				Validation::$errors[$fieldName] = "Error: A file with that name already exists in target location.";
				// Could rename or force user to rename file.
				// Even better to store in uniquely-named subdirectories to
				// prevent conflicts.
				// For example, if the database record ID for an image is 1045: 
				// "/uploads/profile_photos/1045/uploaded_image.png"
				// Because no other profile_photo has that ID, the path is unique.
			} else {
				// move_uploaded_file has is_uploaded_file() built-in
				if (move_uploaded_file($tmpTile, $filePath)) {
					// remove execute file permissions from the file
					if (chmod($filePath, 0644)) {
						$filePermissions = filePermissions($filePath);
						$message         = $filePath;
						CoreFunctions::logAction("files", "File Uploaded", $message);
					} else {
						Validation::$errors[$fieldName] = "Error: Execute permissions could not be removed.";
					}
				}
			}
		}
	}
	public static function validateFile($fieldName, $maxFileSize, $allowedMimeTypes, $allowedExtensions, $checkIsImage){
		if (isset($_FILES[$fieldName])) {
			// Sanitize the provided file name.
			$fileExtension = self::fileExtension($fileName);
			// Even more secure to assign a new name of your choosing.
			// Example: 'file_536d88d9021cb.png'
			// $unique_id = uniqid('file_', true); 
			// $new_name = "{$unique_id}.{$file_extension}";
			$fileType      = $_FILES[$fieldName]['type'];
			$tmpFile       = $_FILES[$fieldName]['tmp_name'];
			$error         = $_FILES[$fieldName]['error'];
			$fileSize      = $_FILES[$fieldName]['size'];
			// Prepend the base upload path to prevent hacking the path
			// Example: $file_name = '/etc/passwd' becomes harmless
			if ($error > 0) {
				// Display errors caught by PHP
				Validation::$errors[$fieldName] = "Error: " . self::fileUploadError($error);
				return false;
			} elseif (!is_uploaded_file($tmpFile)) {
				Validation::$errors[$fieldName] = "Error: Does not reference a recently uploaded file.";
				return false;
			} elseif ($fileSize > $maxFileSize) {
				// PHP already first checks php.ini upload_max_filesize, and 
				// then form MAX_FILE_SIZE if sent.
				// But MAX_FILE_SIZE can be spoofed; check it again yourself.
				Validation::$errors[$fieldName] = "Error: File size is too big.";
				return false;
			} elseif (!in_array($fileType, $allowedMimeTypes)) {
				Validation::$errors[$fieldName] = "Error: Not an allowed mime type.";
				return false;
				/*
				} elseif(!in_array($fileExtension, $allowedExtensions)) {
				// Checking file extension prevents files like 'evil.jpg.php' 
				$errors[$fieldName]= "Error: Not an allowed file extension.";
				return false;
				*/
			} elseif ($checkIsImage && (getimagesize($tmpFile) === false)) {
				// getimagesize() returns image size details, but more importantly,
				// returns false if the file is not actually an image file.
				// You obviously would only run this check if expecting an image.
				Validation::$errors[$fieldName] = "Error: Not a valid image file";
				return false;
			} elseif (self::fileContainsPhp($tmpFile)) {
				// A valid image can still contain embedded PHP.
				Validation::$errors[$fieldName] = "Error: File contains PHP code.";
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
	public static function saveFileFromString($content, $filePath){
		$file = fopen($filePath, 'w');
		fwrite($file, $content);
		fclose($file);
	}
}
