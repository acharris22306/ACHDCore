<?php
namespace ACHD\Core\Database;
$DB_USER = 'collmgr_pmscp';
$DB_PASS = 'collMgrPharmacyMuseumSCP';
session_start();
$userType = $_SESSION['userType'];

if ($userType == "collectionManager") {
	$DB_USER = 'collmgr_pmscp';
	$DB_PASS = 'collMgrPharmacyMuseumSCP';
} else if ($userType == "admin") {
	$DB_USER = 'admin_pmscp';
	$DB_PASS = 'adminPharmacyMuseumSCP';
} else if ($userType == "curator") {
	$DB_USER = 'curator_pmscp';
	$DB_PASS = 'curatorPharmacyMuseumSCP';
} else if ($userType == "any" || $userType == "all") {
	$DB_USER = 'collmgr_pmscp';
	$DB_PASS = 'collMgrPharmacyMuseumSCP';
} else if ($userType == "registration") {
	$DB_USER = 'curator_pmscp';
	$DB_PASS = 'curatorPharmacyMuseumSCP';
}else{
	$DB_USER = 'collmgr_pmscp';
	$DB_PASS = 'collMgrPharmacyMuseumSCP';
}
defined('DB_SERVER') ? null : define("DB_SERVER", 'mysql.pharmacymuseumscp.org');
defined('DB_USER') ? null : define("DB_USER", $DB_USER);
defined('DB_PASS') ? null : define("DB_PASS", $DB_PASS);
defined('DB_NAME') ? null : define("DB_NAME", 'pharmacymuseumscp_mysql');

class MySQLiDatabase{
	private $connection;
	private $stmt;
	public $lastQuery;
	public $lastQueryTime;
	private $lastResult;
	private $magicQuotesActive;
	private $realEscapeStringExists;
	function __construct(){
		$this->openConnection();
		$this->magicQuotesActive      = get_magic_quotes_gpc();
		$this->realEscapeStringExists = function_exists("mysqli_real_escape_string");
	}
	public function openConnection(){
		$this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
		if (!$this->connection) {
			die("Database connection failed: " . mysqli_error($this->connection));
		}
	}
	public function closeConnection(){
		if (isset($this->connection)) {
			mysqli_close($this->connection);
			unset($this->connection);
		}
	}
	public function query($sql){
		global  $numSQLUsedForPage;
		$numSQLUsedForPage++;
		$this->lastQuery = $sql;
	
		$queryStart	     = microtime(true);
		$result          = mysqli_query($this->connection, $sql);
		$queryEnd	     = microtime(true);
		$this->lastQueryTime = (float) ($queryEnd - $queryStart);
		$this->lastResult = $result;
		$this->confirmQuery($result);
		$timeInMS = round($this->lastQueryTime*1000,5);
		$sqlType = substr($sql, 0, 6);
		$message = array($sqlType,$sql,$timeInMS);
		\ACHD\Core\CoreFunctions::logActionCSV("sql", $message);
		return $result;
	}
	public function escapeValue($value){
		$value = (string) $value;
		if ($this->realEscapeStringExists) { // PHP v4.3.0 or higher
			// undo any magic quote effects so mysql_real_escape_string can do the work
			if ($this->magicQuotesActive) {
				$value = stripslashes($value);
			}
			$value = mysqli_real_escape_string($this->connection, $value);
		} else { // before PHP v4.3.0
			// if magic quotes aren't already on then add slashes manually
			if (!$this->magicQuotesActive) {
				$value = addslashes($value);
			}
			// if magic quotes are active, then the slashes already exist
		}
		return $value;
	}
	
	// for prepared statements
	//1)prepare the statement, inserting ? for vars
	//2)bind vars, using s for string, i for int and d for double/float
	//3)execute statement
	//4)bind result variables
	//5)get results, calling fecchStmtResults for each row
	//6)close the statements to free memory
	public function prepStatement($sql){
		$stmtInit = mysqli_stmt_init($this->connection);
		$result = mysqli_stmt_prepare($stmtInit, $sql);
		$this->stmt = $stmtInit;
		return $result;
	}
	
	public function bindVars($varTypes, $vars){
		return mysqli_stmt_bind_param($this->stmt, $varTypes, $vars);
	}
	
	public function execStatement($vars){
		mysqli_stmt_execute($this->stmt);
		return mysqli_stmt_bind_result($this->stmt, $vars);
	}
	public function bindResults($vars){
		return mysqli_stmt_bind_result($this->stmt, $vars);
	}
	public function fetchStmtResults(){
		return mysqli_stmt_fetch($this->stmt);
	}
	public function closePrepedStmt(){
		return mysqli_close($this->stmt);	
	}
	public function queryPreparedStatement($varTypesInput, $inputVars, $outputVars){
		global  $numSQLUsedForPage;
		$numSQLUsedForPage++;
		$this->bindVars($varTypesInput, $inputVars);
		$this->execStatement();
		$stmt = $this->bindResults($outputVars);
	}
	// "database-neutral" methods
	public function fetchArray($resultSet){
		global  $numSQLUsedForPage;
		$numSQLUsedForPage++;
		return mysqli_fetch_array($resultSet);
	}
	public function numRows($resultSet){
		return mysqli_num_rows($resultSet);
	}
	public function insertId(){
		// get the last id inserted over the current db connection
		return mysqli_insert_id($this->connection);
	}
	public function affectedRows(){
		return mysqli_affected_rows($this->connection);
	}
	private function confirmQuery($result){
		if (!$result) {
			$output = "Database query failed: " . mysqli_error($this->connection) . "<br /><br />";
			$output .= "Last SQL query: " . $this->lastQuery;
			die($output);
		}
	}
	public function freeResult($result){
		if ($result){ 
			mysqli_free_result($result);
		}
	}
	public function rollBack(){
		$result = mysqli_rollback($this->connection);
		mysqli_autocommit($this->connection,true);
		return $result;
	}
	public function beginTransaction(){
		return mysqli_autocommit($this->connection,false);
	}
	public function commit(){
		$result=mysqli_commit($this->connection);
		mysqli_autocommit($this->connection,true);
		return $result;
	}
		
}/*
*/
/**/
class PDODatabaseHandler{
	public $lastQuery;
	public $lastQueryTime;
	private $lastResult;
	private $pdoHandler;
	public $stmt;

	function __construct($DSN){
		try{
			$this->pdoHandler = new PDO($DSN, DB_USER, DB_PASS);
			$this->pdoHandler->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
		}catch (Exception $e){
			$_SESSION['message'] = $e->getMessage();	
		}
	}

	public function closeConnection(){
		$this->pdoHandler = null;
	}
	public function query($sql){
		global  $numSQLUsedForPage;
		$numSQLUsedForPage++;
		$this->lastQuery = $sql;
	//	die($sql);
		$sqlType = substr($sql, 0, 6);
		$queryStart	     = microtime(true);
		$result          = $this->pdoHandler->query($sql);
		$queryEnd	     = microtime(true);
		$this->confirmQuery($result);
		$this->lastQueryTime = (float) ($queryEnd - $queryStart);
		$timeInMS = round($this->lastQueryTime*1000,5);
		$message = array($sqlType,$sql,$timeInMS);
		\ACHD\Core\CoreFunctions::logActionCSV("sql", $message);

		$this->lastResult = $result;	
		return $result;
	}
	
	public function escapeValue($value){
		$value = (string) $value;
		$value = $this->pdoHandler->quote($value);
		$value = preg_replace("/^'/", "", $value);
		$value = preg_replace("/'$/", "", $value);/**/
		return $value;
	}
	// "database-neutral" methods
	public function fetchArray($resultSet){
		global  $numSQLUsedForPage;
		$numSQLUsedForPage++;
		return $resultSet->fetch(PDO::FETCH_ASSOC);
	}
	public function numRows($resultSet){
		return $resultSet->rowCount();
	}
	public function insertId(){
		// get the last id inserted over the current db connection
		return $this->pdoHandler->lastInsertId();
	}
	public function affectedRows(){
		return /*1*/$this->lastResult->rowCount();
	}
	private function confirmQuery($result){
		if (!$result) {
			$output = "Database query failed: " . $this->pdoHandler->errorInfo() . "<br /><br />";
			$output .= "Last SQL query: " . $this->lastQuery;
			die($output);
		}
	}
		// for prepared statements
	//1)prepare the statement, inserting ? for vars
	//2)bind vars, using s for string, i for int and d for double/float
	//3)execute statement
	//4)bind result variables
	//5)get results, calling fecchStmtResults for each row
	//6)close the statements to free memory
	public function prepStatement($sql){		
		$this->lastQuery = $sql;
		$this->stmt = $this->pdoHandler->prepare($sql);
		return ($this->stmt->errorCode() == 00000)?true:false;
	}
	
	public function bindVars($varTypes, $vars){
		$i = 1;
		while ($var = array_shift($vars)){
			$this->stmt->bindParam($i,$var);
			$i++;
		}
		return ($this->stmt->errorCode() == 00000)?true:false;
	}
	
	public function execStatement($vars){
		return $this->stmt->execute($vars);
	}
	public function bindResults($vars){
		$i = 1;
		while ($var = array_shift($vars)){
			$this->stmt->bindColumn($i,$var);
			$i++;
		}
		return ($this->stmt->errorCode() == 00000)?true:false;
	}
	public function fetchStmtResults($fetchMode=PDO::FETCH_BOTH){
		return $this->stmt->fetch($fetchMode);
	}
	public function closePrepedStmt(){
		return $this->stmt->closeCursor();	
	}
	public function queryPreparedStatement($varTypesInput, $inputVars, $outputVars){
		global  $numSQLUsedForPage;
		$numSQLUsedForPage++;
		$this->bindVars($varTypesInput, $inputVars);
		$this->execStatement();
		$this->bindResults($outputVars);
	}
	public function freeResult($result){
		$result->closeCursor();
		unset($result);
	}
	public function rollBack(){
		return $this->pdoHandler->rollBack();
	}
	public function beginTransaction(){
		return $this->pdoHandler->beginTransaction();
	}
	public function commit(){
		return $this->pdoHandler->commit();
	}
}


$mySqlDSN = 'mysql:host='.DB_SERVER.';dbname='.DB_NAME;
//$database = new MySQLiDatabase();
$database = new PDODatabaseHandler($mySqlDSN);

//$db =& $database;

