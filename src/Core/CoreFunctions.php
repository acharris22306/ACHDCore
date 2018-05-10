<?php
namespace ACHD\Core
class CoreFunctions{
	private static $instance;
	function __construct(){
		if(empty(self::$instance)){
			self::$instance = new self;
		}
		return self::$instance;
	}
	public static function stripZerosFromDate($markedString = ""){
		// first remove the marked zeros
		$noZeros       = str_replace('*0', '', $markedString);
		// then remove any remaining marks
		$cleanedString = str_replace('*', '', $noZeros);
		return $cleanedString;
	}

	public static function logAction($logFile, $action, $message = ""){
		$archivedLogFileName = FILE_ROOT . 'logs/archives/' .$logFile."_Archive_".time() ."_". rand(1, 11111111). '.txt';
		$logFile = FILE_ROOT . 'logs/' . $logFile . '.txt';
		$new     = file_exists($logFile) ? false : true;
		if(filesize($logFile) > MAX_LOG_SIZE){
			$renameResult = rename($logFile,$archivedLogFileName);
		//	echo $renameResult.$archivedLogFileName;
			$fileMode = 'w';
		}else{
			$fileMode = 'a';
		}
		if ($handle = fopen($logFile, $fileMode)) { // append
			$timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
			$content   = "{$timestamp} | {$action}: {$message}\n";
			fwrite($handle, $content);
			fclose($handle);
			if ($new) {
				chmod($logFile, 0755);
			}
		} else {
			echo "Could not open log file for writing.";
		}
	}
	public static function logActionCSV($logFile, $contentArray){
		$archivedLogFileName = FILE_ROOT . 'logs/archives/' .$logFile."_Archive_".time() ."_". rand(1, 11111111). '.csv';
		$logFile = FILE_ROOT . 'logs/' . $logFile . '.csv';
		$new     = file_exists($logFile) ? false : true;
		if(filesize($logFile) > MAX_LOG_SIZE){
			$renameResult = rename($logFile,$archivedLogFileName);
		//	echo $renameResult.$archivedLogFileName;
			$fileMode = 'w'; 
		}else{
			$fileMode = 'a';
		}
		if ($handle = fopen($logFile, $fileMode)) { 
			$message = implode(",",$contentArray);
			$timestamp = strftime("%Y-%m-%d %H:%M:%S", time());
			$unixTime = time();
			$content   = "{$timestamp},{$unixTime},{$message}\n";
			fwrite($handle, $content);
			fclose($handle);
			if ($new) {
				chmod($logFile, 0755);
			}
		} else {
			echo "Could not open log file for writing.";
		}
	}
	public static function datetimeToText($datetime = ""){
		$unixdatetime = strtotime($datetime);
		return strftime("%B %d, %Y at %I:%M %p", $unixdatetime);
	}
	public static function confirmQuery($resultSet){
		if (!$resultSet) {
			die("Database query failed.");
		}
	}
	public static function passwordEncrypt($password){
		global $passwordHashOptions;
	//	$hashFormat    = "$2y$10$"; // Tells PHP to use Blowfish with a "cost" of 10
	//	$saltLength    = 22; // Blowfish salts should be 22-characters or more
	//	$salt          = generateSalt($saltLength);
	//	$formatAndSalt = $hashFormat . $salt;
		$hash          = password_hash($password, PASSWORD_HASH_METHOD,$passwordHashOptions);
		return $hash;
	}
	public static function generateSalt($length){
		// Not 100% unique, not 100% random, but good enough for a salt
		// MD5 returns 32 characters
		$uniqueRandomString   = md5(uniqid(mt_rand(), true));
		// Valid characters for a salt are [a-zA-Z0-9./]
		$base64String         = base64_encode($uniqueRandomString);
		// But not '+' which is valid in base64 encoding
		$modifiedBase64String = str_replace('+', '.', $base64String);
		// Truncate string to the correct length
		$salt                 = substr($modifiedBase64String, 0, $length);
		return $salt;
	}
	public static function redirectTo($newLocation){
		global $inStylingMode;
		if ($inStylingMode == false){
			header('Location: ' . $newLocation);
		}else if ($inStylingMode == true){
			self::logAction("stylingDev", "No Redirect",  $newLocation);
		}
		//exit;
	}
	// Sanitize for HTML output 
	public static function h($string){
		return htmlspecialchars($string);
	}
	// Sanitize for JavaScript output
	public static function j($string){
		return json_encode($string);
	}
	// Sanitize for use in a URL
	function u($string){
		return urlencode($string);
	}
	public static function p($string){
		return str_replace('<?php','_PHP_',$string);	
	}
	public static function hju($string){
		return self::h(self::j(self::u($string)));	
	}
	public static function hjup($string){
		return self::h(self::j(self::u(self::p($string))));	
	}


	public static function debug($var, $outputAsHtml = true){ 
	  if ( is_null($var) ) {
		  return '<span class="null-value">[NULL]</span>'; 
		}
	  $out = '';
	  switch ($var) { 
		case empty($var):
		  $out = '[empty value]';
		  break;
	
		case is_array($var):
		  $out = var_export($var, true);
		  break;
	
		case is_object($var):
		  $out = var_export($var, true);
		  break;
	  
		case is_string($var):
		  $out = $var;
		  break;
	
		default:
		  $out = var_export($var, true);
		  break;
	  }
	  if ($outputAsHtml) { 
		  $out = "<pre>\n" . h($out) ."</pre>"; 
	  }
	  return $out;
	}



	public static function mimeType($ext = null){
	  $types = array(
		'ai'      => 'application/postscript',
		'aif'     => 'audio/x-aiff',
		'aifc'    => 'audio/x-aiff',
		'aiff'    => 'audio/x-aiff',
		'asc'     => 'text/plain',
		'atom'    => 'application/atom+xml',
		'atom'    => 'application/atom+xml',
		'au'      => 'audio/basic',
		'avi'     => 'video/x-msvideo',
		'bcpio'   => 'application/x-bcpio',
		'bin'     => 'application/octet-stream',
		'bmp'     => 'image/bmp',
		'cdf'     => 'application/x-netcdf',
		'cgm'     => 'image/cgm',
		'class'   => 'application/octet-stream',
		'cpio'    => 'application/x-cpio',
		'cpt'     => 'application/mac-compactpro',
		'csh'     => 'application/x-csh',
		'css'     => 'text/css',
		'csv'     => 'text/csv',
		'dcr'     => 'application/x-director',
		'dir'     => 'application/x-director',
		'djv'     => 'image/vnd.djvu',
		'djvu'    => 'image/vnd.djvu',
		'dll'     => 'application/octet-stream',
		'dmg'     => 'application/octet-stream',
		'dms'     => 'application/octet-stream',
		'doc'     => 'application/msword',
		'dtd'     => 'application/xml-dtd',
		'dvi'     => 'application/x-dvi',
		'dxr'     => 'application/x-director',
		'eps'     => 'application/postscript',
		'etx'     => 'text/x-setext',
		'exe'     => 'application/octet-stream',
		'ez'      => 'application/andrew-inset',
		'gif'     => 'image/gif',
		'gram'    => 'application/srgs',
		'grxml'   => 'application/srgs+xml',
		'gtar'    => 'application/x-gtar',
		'hdf'     => 'application/x-hdf',
		'hqx'     => 'application/mac-binhex40',
		'htm'     => 'text/html',
		'html'    => 'text/html',
		'ice'     => 'x-conference/x-cooltalk',
		'ico'     => 'image/x-icon',
		'ics'     => 'text/calendar',
		'ief'     => 'image/ief',
		'ifb'     => 'text/calendar',
		'iges'    => 'model/iges',
		'igs'     => 'model/iges',
		'jpe'     => 'image/jpeg',
		'jpeg'    => 'image/jpeg',
		'jpg'     => 'image/jpeg',
		'js'      => 'application/x-javascript',
		'json'    => 'application/json',
		'kar'     => 'audio/midi',
		'latex'   => 'application/x-latex',
		'lha'     => 'application/octet-stream',
		'lzh'     => 'application/octet-stream',
		'm3u'     => 'audio/x-mpegurl',
		'man'     => 'application/x-troff-man',
		'mathml'  => 'application/mathml+xml',
		'me'      => 'application/x-troff-me',
		'mesh'    => 'model/mesh',
		'mid'     => 'audio/midi',
		'midi'    => 'audio/midi',
		'mif'     => 'application/vnd.mif',
		'mov'     => 'video/quicktime',
		'movie'   => 'video/x-sgi-movie',
		'mp2'     => 'audio/mpeg',
		'mp3'     => 'audio/mpeg',
		'mpe'     => 'video/mpeg',
		'mpeg'    => 'video/mpeg',
		'mpg'     => 'video/mpeg',
		'mpga'    => 'audio/mpeg',
		'ms'      => 'application/x-troff-ms',
		'msh'     => 'model/mesh',
		'mxu'     => 'video/vnd.mpegurl',
		'nc'      => 'application/x-netcdf',
		'oda'     => 'application/oda',
		'ogg'     => 'application/ogg',
		'pbm'     => 'image/x-portable-bitmap',
		'pdb'     => 'chemical/x-pdb',
		'pdf'     => 'application/pdf',
		'pgm'     => 'image/x-portable-graymap',
		'pgn'     => 'application/x-chess-pgn',
		'png'     => 'image/png',
		'pnm'     => 'image/x-portable-anymap',
		'ppm'     => 'image/x-portable-pixmap',
		'ppt'     => 'application/vnd.ms-powerpoint',
		'ps'      => 'application/postscript',
		'qt'      => 'video/quicktime',
		'ra'      => 'audio/x-pn-realaudio',
		'ram'     => 'audio/x-pn-realaudio',
		'ras'     => 'image/x-cmu-raster',
		'rdf'     => 'application/rdf+xml',
		'rgb'     => 'image/x-rgb',
		'rm'      => 'application/vnd.rn-realmedia',
		'roff'    => 'application/x-troff',
		'rss'     => 'application/rss+xml',
		'rtf'     => 'text/rtf',
		'rtx'     => 'text/richtext',
		'sgm'     => 'text/sgml',
		'sgml'    => 'text/sgml',
		'sh'      => 'application/x-sh',
		'shar'    => 'application/x-shar',
		'silo'    => 'model/mesh',
		'sit'     => 'application/x-stuffit',
		'skd'     => 'application/x-koan',
		'skm'     => 'application/x-koan',
		'skp'     => 'application/x-koan',
		'skt'     => 'application/x-koan',
		'smi'     => 'application/smil',
		'smil'    => 'application/smil',
		'snd'     => 'audio/basic',
		'so'      => 'application/octet-stream',
		'spl'     => 'application/x-futuresplash',
		'src'     => 'application/x-wais-source',
		'sv4cpio' => 'application/x-sv4cpio',
		'sv4crc'  => 'application/x-sv4crc',
		'svg'     => 'image/svg+xml',
		'svgz'    => 'image/svg+xml',
		'swf'     => 'application/x-shockwave-flash',
		't'       => 'application/x-troff',
		'tar'     => 'application/x-tar',
		'tcl'     => 'application/x-tcl',
		'tex'     => 'application/x-tex',
		'texi'    => 'application/x-texinfo',
		'texinfo' => 'application/x-texinfo',
		'tif'     => 'image/tiff',
		'tiff'    => 'image/tiff',
		'tr'      => 'application/x-troff',
		'tsv'     => 'text/tab-separated-values',
		'txt'     => 'text/plain',
		'ustar'   => 'application/x-ustar',
		'vcd'     => 'application/x-cdlink',
		'vrml'    => 'model/vrml',
		'vxml'    => 'application/voicexml+xml',
		'wav'     => 'audio/x-wav',
		'wbmp'    => 'image/vnd.wap.wbmp',
		'wbxml'   => 'application/vnd.wap.wbxml',
		'wml'     => 'text/vnd.wap.wml',
		'wmlc'    => 'application/vnd.wap.wmlc',
		'wmls'    => 'text/vnd.wap.wmlscript',
		'wmlsc'   => 'application/vnd.wap.wmlscriptc',
		'wrl'     => 'model/vrml',
		'xbm'     => 'image/x-xbitmap',
		'xht'     => 'application/xhtml+xml',
		'xhtml'   => 'application/xhtml+xml',
		'xls'     => 'application/vnd.ms-excel',
		'xml'     => 'application/xml',
		'xpm'     => 'image/x-xpixmap',
		'xsl'     => 'application/xml',
		'xslt'    => 'application/xslt+xml',
		'xul'     => 'application/vnd.mozilla.xul+xml',
		'xwd'     => 'image/x-xwindowdump',
		'xyz'     => 'chemical/x-xyz',
		'zip'     => 'application/zip'
	  );

	  if (is_null($ext)){
		  return $types;
	  }

	  $lowerExt = strtolower($ext);

	  return isset($types[$lowerExt]) ? $types[$lowerExt] : null;
	}

	/**
	* simply puts "<pre>" tags around the print_r call so the formatting looks good in a browser.
	*
	* @param mixed $mixed
	*/
	public static function echoR($mixed, $returnResult = false){
	// 	if(app_status == "live")
	// 		return;
		if (!$returnResult){
			echo "<pre>";
			print_r($mixed);
			echo "</pre>";
		}else{
			$result =  "<pre>" . print_r($mixed, true) . "</pre>";
			return $result;
		}
	}

	/**
	* die() doesn't do a good job of dumping objects and arrays. this one does what die should...
	*
	* @param mixed $mixed
	*/
	public static function dieR($mixed, $returnResult = false){
		self::echoR($mixed, $returnResult);
		die();
	}
	/**
	* simply puts "<pre>" tags around the var_dump call so the formatting looks good in a browser.
	*
	* @param mixed $mixed
	*/
	public static function dumpR($mixed, $returnResult = false){	
		global $displayVarDumpOnPage;
		if (!$returnResult){
			echo("<code class=\"dumpR\"><pre ");
			if (!$displayVarDumpOnPage){
				echo "style=\"display:none;\"";	
			}else{
				echo "style=\"display:block;\"";	
			}
			echo ">";
			print_r($mixed);
			echo("</pre></code>");
		}else{
			$result = "<code class=\"dumpR\"><pre ";
			if (!$displayVarDumpOnPage){
				$result .= "style=\"display:none;\"";	
			}else{
				$result .= "style=\"display:block;\"";	
			}
			$result .= ">";
			$result .= print_r($mixed, true);
			$result .= "</pre></code>";
			return $result;
		}
	}
	/**
	* draws a div with a colored border around an echo_r call. Very useful to keep track of multiple echo_r calls.
	*
	* @param mixed $mixed
	* @param string $color any acceptable color string that works with css
	*/
	public static function showR($mixed, $color = "blue", $returnResult = false){
		if (!$returnResult){
			echo "<div align=\"left\" style=\"border: 1px solid ".$color.";\">";
			self::echoR($mixed);
			echo "</div>";
		}else{
			$result =  "<div align=\"left\" style=\"border: 1px solid ".$color.";\">";
			$result .= self::echoR($mixed, true);
			$result .=  "</div>";
			return $result;
		}
	}



	public static function parseCamelCase($str){
		return preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]|[0-9]{1,}/', ' $0', $str);
	}
	public static function parseCamelCaseUC($str){
		return ucwords(self::parseCamelCase($str));
	}
	public static function breadcrumbs($separator = ' &raquo; ', $home = 'Home') {
		// This gets the REQUEST_URI (/path/to/file.php), splits the string (using '/') into an array, and then filters out any empty values
		$path = array_filter(explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)));
		$base = SITE_ROOT;

		// Initialize a temporary array with our breadcrumbs. (starting with our home page, which I'm assuming will be the base URL)
		$breadcrumbs = Array("<a href=\"".$base."\">".$home."</a>");

		// Find out the index for the last value in our path array
		$last = end(array_keys($path));

		// Build the rest of the breadcrumbs 
		foreach ($path AS $index => $crumb) {
			// Our "title" is the text that will be displayed (strip out .php and turn '_' into a space)
			$titleToParse = str_replace(Array('.php', '_'), Array('', ' '), $crumb);
			$title = ucwords(self::parseCamelCase($titleToParse));
			// If we are not on the last index, then display an <a> tag
			if ($index != $last){
				$newBreadcrumb = "<a href=\"".$base.$crumb."\">".$title."</a>";
				$breadcrumbs[] = $newBreadcrumb;
			// Otherwise, just display the title (minus)
			}else{
				$breadcrumbs[] = $title;
			}
		}

		// Build our temporary array (pieces of bread) into one big string
		return implode($separator, $breadcrumbs);
	}



	public static function generateThumbnail($img, $width = 150, $quality = 90, $newLocation = FILE_ROOT){
		if (is_file($img)) {
		
			$imgSizes = getimagesize($img);
		
			$resizeFactor = $imgSizes[0]/$width; 
			$newHeight = (int)floor($imgSizes[1]/$resizeFactor);
			$imagick = new \Imagick(realpath($img));
			$imagick->readImage(realpath($img));
			$imagick->setImageFormat('jpeg');
			$imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
			$imagick->setImageCompressionQuality($quality);
			$imagick->thumbnailImage($width, $newHeight, false, false);
			$imgNameParts = explode("/",$img);
			$imgNameLength = count($imgNameParts);
			$imgNameOnly = $imgNameParts[$imgNameLength-1];
			$filenameNoExt = reset(explode('.', ($imgNameOnly)));
			$newFileName = (realpath($newLocation)."/".$filenameNoExt . '_thumb' . '.jpg');
		
			if(fopen($newFileName,'w')){
				if (file_put_contents($newFileName,$imagick->getImageBlob()) == false) {
			//	die("no file put contents");
					return false;
				}
			}
			//die("file created named".$filenameNoExt . '_thumb' . '.jpg');
			return true;
		} else {
			//die("not file");
			return false;
		}
	}
	public static function getThumbNameFromImgName($imgName){
		if($imgName == ""){
			$imgName = "default.jpg";
		}
		$filenameNoExt = reset(explode('.', $imgName));
		return ($filenameNoExt . '_thumb' . '.jpg');
	}
	public static function debugger(){
		echo "<!-- begin debug mode -->";
		global $displayVarDumpOnPage;
		echo "<div id=\"varDump\"";
		if (!$displayVarDumpOnPage){
			echo "style=\"display:none;\"";	
		}else{
			echo "style=\"display:block;\"";	
		}
		echo "><p>Variable Dump (DEBUGGING ONLY, will be removed prior to release):</p>";
		echo "database (global): <br/>";
		self::dumpR($database);
		echo "<br/>";
		echo "<br/>";
		echo "cookie (global): <br/>";
		self::dumpR($_COOKIE);
		echo "<br/>";
		echo "<br/>";
		echo "session (global): <br/>";
		self::dumpR($_SESSION);
		echo "<br/>";
		echo "<br/>";
		echo "server (global): <br/>";
		self::dumpR($_SERVER);
		echo "<br/>";
		echo "<br/>";
		echo "post (global): <br/>";
		self::dumpR($_POST);
		echo "<br/>";
		echo "<br/>";
		echo "get (global): <br/>";
		self::dumpR($_GET);
		echo "<br/>";
		echo "<br/>";
		echo "files (global): <br/>";
		self::dumpR($_FILES);
		echo "<br/>";
		echo "<br/>";
		echo "user: <br/>";
		self::dumpR($user);
		echo "<br/>";
		echo "<br/>";
		echo "session: <br/>";
		self::dumpR($session);
		echo "<br/>";
		echo "<br/>";
		echo "curator: <br/>";
		self::dumpR($curator);
		echo "<br/>";
		echo "<br/>";
		echo "collectionManager: <br/>";
		self::dumpR($collectionManager);
		echo "<br/>";
		echo "<br/>";
		echo "admin: <br/>";
		self::dumpR($admin);
		echo "<br/>";
		echo "<br/>";
		echo "</div>";
		echo "<!-- end debug mode -->";
	}

	public static function dirToArray($dir,$recursive=false) { 
		global $dirsToSkip;
		set_time_limit(0);
	   $result = array(); 
	   $cdir = scandir($dir); 
	   foreach ($cdir as $key => $value)   { 
		  if (!in_array($value,array(".","..")))   { 
			 if (is_dir($dir . "/" . $value))  { 
				 if($recursive == true && !Validation::($value,$dirsToSkip)){
					 $subfolderResult = self::dirToArray($dir . "/" . $value,$recursive); 
					 foreach($subfolderResult as $resultKey=>$resultValue){
						 $subfolderResult[$resultKey] = $value."/".$resultValue;
					 }
					 $result = array_merge($result,$subfolderResult);
				 }else{
				 
				 }
		   
			 }  else { 
				$result[] = $value; 
			 } 
		  } 
	   }  
	   return $result; 
	} 
	public static function dirToArray2($dir){
		global $dirsToSkip;
		set_time_limit(0);
	   $result = array(); 
	   $cdir = scandir($dir); 
	   foreach ($cdir as $key => $value)   { 
		  if (!in_array($value,array(".","..")))   { 
			 if (is_dir($dir . "/" . $value) && !Validation::hasInclusionIn($value,$dirsToSkip))  { 
					 $subfolderResult = self::dirToArray2($dir . "/" . $value); 
					 $result[$value] = $subfolderResult;
			 }  else { 
				$result[] = $value; 
			 } 
		  } 
	   } 
		return $result;
	}
	public static function createZipFromDirectory($directory, $dirName,$recursive=false){
		set_time_limit(0);
		$files = self::dirToArray($directory,$recursive);
		$fileStructure = self::dirToArray2($directory);
		$baseName = 'files/zips/'.$dirName."/".$dirName.time()."_". rand(1, 11111111) .".zip";
		$structureName = 'zipStructure_'.$dirName.time()."_". rand(1, 11111111) .".txt";
		$zipname = FILE_ROOT.$baseName;
		$zipFile = SITE_ROOT.$baseName;
		$zip = new ZipArchive();
		$zip->open($zipname, ZipArchive::CREATE);
		foreach ($files as $file) {
		  $zip->addFile($directory.$file,$file);
		}
		$zip->addFromString($structureName,html_entity_decode(print_r($fileStructure,true)));
		$zip->close();
		return $zipFile;
	}


	public static function parseSize($sizeToParse, $precision = 2){
	
		$parsedSize="";
		if($sizeToParse >=10000.00){
			$sizeInKb = round(($sizeToParse/1024),$precision);
			if($sizeInKb>=1000.00){
				$sizeInMb = round(($sizeInKb/1024),$precision);
				if($sizeInMb>=1000.00){
					$sizeInGB = round(($sizeInMb/1024),$precision);
					if($sizeInGB>=1000.00){
						$sizeInTB = round(($sizeInGB/1024),$precision);
						$parsedSize = $sizeInTB." TB";
					}else{
						$parsedSize = $sizeInGB." GB";
					}
				}else{
					$parsedSize=$sizeInMb." MB";
				}
			}else{
				$parsedSize=$sizeInKb." KB";
			}
		}else{
			$parsedSize = $sizeToParse." Bytes";
		}
		return $parsedSize;
	}

	public static function createXMLTag($tag, $content){
		$tagstring = '<'.$tag.'>'."\n";
		$tagstring .=($content)."\n";
		$tagstring.= '</'.$tag.'>'."\n";
		return $tagstring;
	}
	public static function createXMLTagWithAttributes($tag, $content,$attributes){
		$tagstring = '<'.$tag.' ';
		foreach($attributes as $attr=>$val){
			$tagstring.=''.$attr.'="'.$val.'" ';
		}
		$tagstring .='>'."\n".($content)."\n";
		$tagstring.= '</'.$tag.'>'."\n";
		return $tagstring;
	}
	public static function createSelfClosingXMLTag($tag,$attributes){
		$tagstring = '<'.$tag.' ';
		foreach($attributes as $attr=>$val){
			$tagstring.=''.$attr.'="'.$val.'" ';
		}
		$tagstring .='/>'."\n";
		return $tagstring;
	}
	public static function createJSONEntry($key,$value,$type="text"){
		$entryType = strtolower($type);
		if($entryType == "object"){
			return '"'.$key.'":{'.$value.'},'."\n";
		}else if($entryType == "array"){
			$val = implode(",",$value);
			return '"'.$key.'":['.$val.'],'."\n";
		}else{
			return '"'.$key.'":"'.$value.'",'."\n";
		}
	}
	public static function getLastLines($file,$lines){
		$fa = file($file);
		$linesForOutput = array();
		$length = count($fa);
	
		$minLine = $length-$lines;
		$i = 0;
		$idx=$length-1;
		while ($i<$lines && $idx>=0){
			$linesForOutput[$i]=$fa[$idx];
			$i++;
			$idx--;
		}
		//var_dump ($fa);
		//var_dump ($linesForOutput);
		return $linesForOutput;
	}
	public static function outputLastLines($file,$lines,$start="",$separator=",",$end=""){
		$fa = self::getLastLines($file,$lines);
		$output = $start;
	//echo($output);
		$output.=implode($separator,$fa);
		//echo($output);
		$output.=$end;
		//echo $end;
		//echo($output);
		return $output;
	}
}