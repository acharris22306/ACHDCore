<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
namespace ACHD\Core
class SiteMaintenance extends \ACHD\Core\Database\ActiveRecordBase{
	protected static $tableName = "siteMaintenance";
	protected static $idName = "siteMaintenanceId";
	protected static $dbFields = array('siteMaintenanceId', 'type', 'execTime', 'logMessage');
	public $siteMaintenanceId;
	public $type;
	public $execTime;
	public $logMessage;

	
	public static function findAllBySiteMaintenanceId(){
		return self::findBySql("SELECT * FROM siteMaintenance ORDER BY siteMaintenanceId ASC;");
	}
	public static function findAllByExecTimeDesc(){
		return self::findBySql("SELECT * FROM siteMaintenance ORDER BY execTime DESC,siteMaintenanceId DESC;");
	}
	public static function findSetNumberByExecTimeDesc($limit = MAX_OBJ_ADMIN_DB){
		return self::findBySql("SELECT * FROM siteMaintenance ORDER BY execTime DESC,siteMaintenanceId DESC LIMIT ".$limit.";");
	}
	public static function findMostRecentOfType($type){
		$resultArray = self::findBySql("SELECT * FROM siteMaintenance WHERE type='{$type}' ORDER BY execTime DESC LIMIT 1;");
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}
	public static function determineIfNextMaintenance($type, $maxTimeInDays = 1){
		$maxTimeInSecs = $maxTimeInDays*(60*60*24);
	//	$maxTimeInSecs = 240;
		$mostRecent = self::findMostRecentOfType($type);
		$timeThreshold = time()-$maxTimeInSecs;
		if($timeThreshold>=$mostRecent->execTime){
			return true;
		}else{
			return false;
		}	
	}
	public static function backupDatabaseAsStringMySQL(){
		$mysqli      = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
		$mysqli->select_db(DB_NAME);
		$mysqli->query("SET NAMES 'utf8'");
		$queryTables = $mysqli->query('SHOW TABLES');
		while ($row         = $queryTables->fetch_row()) {
			$targetTables[] = $row[0];
		}
		$magicQuotesActive      = get_magic_quotes_gpc();
		$realEscapeStringExists = function_exists("mysqli_real_escape_string");
		$content = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\r\nSET time_zone = \"+00:00\";\r\n\r\n\r\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\r\n/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\r\n/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\r\n/*!40101 SET NAMES utf8 */;\r\n--Database: `" . $name . "`\r\n\r\n\r\n";
		foreach ($targetTables as $table)  {
			$result        = $mysqli->query('SELECT * FROM ' . $table);
			$$fieldsAmount = $result->field_count;
			$rows_num      = $mysqli->affected_rows;
			$res           = $mysqli->query('SHOW CREATE TABLE ' . $table);
			$TableMLine    = $res->fetch_row();
			$content .= "\n\n" . $TableMLine[1] . ";\n\n";
			for ($i = 0, $stCounter = 0; $i < $$fieldsAmount; $i++, $stCounter = 0) {
				while ($row = $result->fetch_row()){ //when started (and every after 100 command cycle):
					if ($stCounter % 100 == 0 || $stCounter == 0) {
						$content .= "\nINSERT INTO " . $table . " VALUES";
					}
					$content .= "\n(";
					for ($j = 0; $j < $$fieldsAmount; $j++) {
						if (isset($row[$j])) {
							$content .= '"' . $row[$j] . '"';
						} else {
							$content .= '""';
						} 
						if ($j < ($$fieldsAmount - 1)){
							$content.= ',';
						}
					}
					$content .=")";
					if ((($stCounter + 1) % 100 == 0 && $stCounter != 0) || $stCounter + 1 == $rows_num)  {
						$content .= ";";
					} else {
						$content .= ",";
					} 
					$stCounter = $stCounter + 1;
				}
			} 
			$content .="\n\n\n";
		}
		$content .= "\r\n\r\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\r\n/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\r\n/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
		return $content;
	}
	public static function backupDatabaseAsString(){
		global $database;
		$database->query("SET NAMES 'utf8'");
		$queryTables = $database->query('SHOW TABLES');
		while ($row         = $queryTables->fetch(PDO::FETCH_NUM)) {
			$targetTables[] = $row[0];
		}
		$content = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\r\nSET time_zone = \"+00:00\";\r\n\r\n\r\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\r\n/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\r\n/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\r\n/*!40101 SET NAMES utf8 */;\r\n--Database: `" . $name . "`\r\n\r\n\r\n";
		foreach ($targetTables as $table)  {
			$result        = $database->query('SELECT * FROM ' . $table);
			$$fieldsAmount = $result->columnCount();
			$rowNum      = $result->rowCount();
			$res           = $database->query('SHOW CREATE TABLE ' . $table);
			$TableMLine    = $res->fetch(PDO::FETCH_NUM);
			$content .= "\n\n" . $TableMLine[1] . ";\n\n";
			for ($i = 0, $stCounter = 0; $i < $$fieldsAmount; $i++, $stCounter = 0) {
				while ($row = $result->fetch(PDO::FETCH_NUM)){ //when started (and every after 100 command cycle):
					if ($stCounter % 100 == 0 || $stCounter == 0) {
						$content .= "\nINSERT INTO " . $table . " VALUES";
					}
					$content .= "\n(";
					for ($j = 0; $j < $$fieldsAmount; $j++) {
						if (isset($row[$j])) {
							$content .= '"' . $row[$j] . '"';
						} else {
							$content .= '""';
						} 
						if ($j < ($$fieldsAmount - 1)){
							$content.= ',';
						}
					}
					$content .=")";
					if ((($stCounter + 1) % 100 == 0 && $stCounter != 0) || $stCounter + 1 == $rowNum)  {
						$content .= ";";
					} else {
						$content .= ",";
					} 
					$stCounter = $stCounter + 1;
				}
			} 
			$content .="\n\n\n";
		}
		$content .= "\r\n\r\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\r\n/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\r\n/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
		return $content;
	}
	public static function exportFullDatabase($backupName = false){
		$content = self::backupDatabaseAsString();
		$dbName = "scpMuseumDB";
		$backupName = $backupName ? $backupName : $dbName . "_" .time()."_". rand(1, 11111111) . ".sql";
		$locationToSave = FILE_ROOT . 'files/databaseBackups/'.$backupName;
		$new     = file_exists($locationToSave) ? false : true;
		$fileMode = 'w'; 
		if ($handle = fopen($locationToSave, $fileMode)) {
			fwrite($handle, html_entity_decode($content));
			fclose($handle);
			if ($new) {
				chmod($locationToSave, 0755);
			}
			return $backupName;
		}else{
			return false;
		}

	}

	public static function cleanUpFilesInDir($directory, $daysToKeepFiles = 7, $cleanupExcess = true, $maxFiles = MAX_FILES_BEFORE_CLEANING){
		$dir = FILE_ROOT.$directory;
	//	echo var_dump($directory." ". $daysToKeepFiles." ".  $cleanupExcess ." ".  $maxFiles);
		$earliestTimeToKeep = time() - (60*60*24*$daysToKeepFiles);
	//	echo time()." ++ ".$earliestTimeToKeep;
		$filesInDir = array();
		$filesInDir = scandir($dir);
		$logMsg = "Cleaning Up FIles In Directory";
		foreach($filesInDir as $file){
			if (($file != "index.php")){
				$fullPathToFile = $dir."/".$file;
				if(is_file($fullPathToFile)){
					$fileLastModified = filemtime($fullPathToFile);
					if($fileLastModified <= $earliestTimeToKeep){
					//	$logMsg. "deleting: ". $fullPathToFile . " modified on: ".$fileLastModified."\n";
						unlink($fullPathToFile);	

					}else{
						//$logMsg.= $file . " was not deleted\n";
					}
				}else{
				//	$logMsg.= $fullPathToFile." is not a file\n";	
				}
			}
		}
		if($cleanupExcess){
			self::cleanUpFilesInDirMaxFiles($directory,$maxFiles);
		}
		$siteMaintenance = new self;
		$siteMaintenance->type = "fileCleanup_".$directory;
		$siteMaintenance->logMessage = $logMsg;
		$siteMaintenance->execTime=time();
		$siteMaintenance->create();
	}
	
	public static function cleanUpFilesInDirMaxFiles($directory, $maxFiles = MAX_FILES_BEFORE_CLEANING){
		$dir = FILE_ROOT.$directory;
		$filesInDir = array();
		$filesInDir = dirToArray($dir);
		$numFiles = count($filesInDir);
		$numFilesToDelete = $numFiles - $maxFiles;
		if ($numFilesToDelete <=0){
			$logMsg = "No files to delete in ".$directory;
			return false;
		}else{
			$logMsg = "Cleaning Up ".$numFilesToDelete." FIles In Directory";
			usort($filesInDir, function ($a, $b){
				$fullPathToFileA = $dir."/".$a;
				$fullPathToFileB = $dir."/".$b;
				if(file_exists($fullPathToFileA) && file_exists($fullPathToFileB)){
					if (filemtime($a) === filemtime($b)) return 0;
					return filemtime($a) < filemtime($b) ? -1 : 1; 
				}else{
					return 0;
				}

			});
			$i=0;
			$j=0;
			while($i<$numFilesToDelete){
				$file = $filesInDir[$j];
				if (($file != "index.php")){
					$fullPathToFile=($dir."/".$file);
					if(is_file($fullPathToFile)){
						unlink($fullPathToFile);	
						$j++;	
						$i++;
						//$logMsg.= $fullPathToFile." file deleted\n";
					}else{
						$j++;
					//	$logMsg.= $fullPathToFile." not a file\n";	
					}
				}else{
					$j++;
				}
			}
		}
		$siteMaintenance = new self;
		$siteMaintenance->type = "fileCleanup_max_".$directory;
		$siteMaintenance->logMessage = $logMsg;
		$siteMaintenance->execTime=time();
		$siteMaintenance->create();
	}
	public static function backupDirAsZip($directory, $dirName,$recursive = false){
		$zipName = createZipFromDirectory($directory, $dirName,$recursive);
		$siteMaintenance = new self;
		$siteMaintenance->type = "fileBackup_".$dirName;
		$siteMaintenance->logMessage = "backup made of directory: ".$dirName;
		$siteMaintenance->execTime=time();
		$siteMaintenance->create();
		return $zipName;
	}
	public static function pruneCollectionManagers($monthsMax = 6, $ignoreObjsAdded = false){
		global $database;
		$colMgrs= CollectionManager::getInactiveCollectionManagers($monthsMax,$ignoreObjsAdded);
		$numDeleted = 0;
		foreach($colMgrs as $cm){
			$entryToDelete = User::findById($cm->userId);
			try{
				$database->beginTransaction();
				if ($cm->delete()&& $entryToDelete->delete()) {
					$database->commit();
					$numDeleted++;
				} else {
					$database->rollBack();
				}
			}catch(Exception $e){
				$database->rollBack();
				CoreFunctions::logAction("errors", "deleteUser", $e->getMessage());
			}
		}
		$siteMaintenance = new self;
		$siteMaintenance->type = "pruneColMgrs";
		$siteMaintenance->logMessage = "Removed ".$numDeleted." inactive collection managers";
		$siteMaintenance->execTime=time();
		$siteMaintenance->create();
		return $numDeleted;
	}
	public static function cleanUpCollectionManagers($monthsMax = 6, $ignoreObjsAdded = false,$daysBeforeMaintenance = 30){
		if(self::determineIfNextMaintenance("pruneColMgrs",$daysBeforeMaintenance)/* || true*/){
			return self::pruneCollectionManagers($monthsMax, $ignoreObjsAdded );
		}
	}
	public static function cleanupFiles($directoriesAndOptions,$daysBeforeMaintenance = 1){
		foreach ($directoriesAndOptions as $dir=>$opts){
			if(self::determineIfNextMaintenance("fileCleanup_".$dir,$daysBeforeMaintenance)/* || true*/){
				self::cleanUpFilesInDir($dir,$directoriesAndOptions[$dir]["daysToKeepFiles"],$directoriesAndOptions[$dir]["cleanupExcess"],$directoriesAndOptions[$dir]["maxFiles"]);
				
			}
		}
	}
	public static function backupDatabase($daysBeforeMaintenance = 1){
		if(self::determineIfNextMaintenance("databaseBackup",$daysBeforeMaintenance)){
			self::exportFullDatabase();
		}
	}
	public static function archiveObjImgs($daysBeforeMaintenance = 1){
		if(self::determineIfNextMaintenance("fileBackup_objectImages",$daysBeforeMaintenance)){
			self::backupDirAsZip(FILE_ROOT."images/objectImages/","objectImages");
		}
	}
	public static function archiveFolders($foldersToArchive,$daysBeforeMaintenance = 1,$recursive = false){
		foreach($foldersToArchive as $folder=>$folderName){
			if(self::determineIfNextMaintenance("fileBackup_".$folderName,$daysBeforeMaintenance)){
				self::backupDirAsZip(FILE_ROOT.$folder,$folderName,$recursive);
			}
		}
		
	}
}
