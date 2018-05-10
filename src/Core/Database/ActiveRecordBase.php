<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
namespace ACHD\Core\Database
class ActiveRecordBase{
	protected static $idName = '';
	protected static $tableName = '';
	protected static $dbFields = array();
	protected static $numUpdates;
	protected static $dateModified;
	protected static $defaultObjAttr = array();
	

	public function getId(){
		$idNameAttribute = static::$idName;
		return $this->$idNameAttribute;
	}
	public function setId($id){
		$idNameAttribute = static::$idName;
		$this->$idNameAttribute = $id;
	}
	public static function getDefaultObj(){
		global $numObjUsedForPage;
		$className = get_called_class();
		$object = new $className;
		foreach(static::$defaultObjAttr as $attribute=>$value){
			if($object->hasAttribute($attribute)) {
				$object->$attribute = h($value);
			}
			$object->numUpdates = 0;
		}
		$numObjUsedForPage++;
		return $object;
	}
	function __construct(){
		foreach(static::$defaultObjAttr as $attribute=>$value){
			if($this->hasAttribute($attribute)) {
				$this->$attribute = h($value);
			}
		}
		$this->numUpdates = 0;
	}
	public static function findAll() {
		return static::findBySql("SELECT * FROM ".static::$tableName.";");
	}
	 
	public static function findById($id,$returnDefault=false) {
		global $database;
		$id = (int) $database->escapeValue($id);
		$resultArray = static::findBySql("SELECT * FROM ".static::$tableName." WHERE ".static::$idName." ='{$id}' LIMIT 1;");
		if($returnDefault){
			$valIfFalse = static::getDefaultObj();
		}else{
			$valIfFalse = false;
		}
		return !empty($resultArray) ? array_shift($resultArray) : $valIfFalse; //static::getDefaultObj();
	}
	public static function findBySql($sql="") {
		global $database;
		$resultSet = $database->query($sql);
		$objectArray = array();
		while ($row = $database->fetchArray($resultSet)) {
			$objectArray[] = static::instantiate($row);
		}
		$database->freeResult($resultSet);
		return $objectArray;
	}
	public static function findBySqlNoIterator($sql="") {
		global $database;
		$resultSet = $database->query($sql);
		$RI = new RecordIterator();
		$RI->resultSet = $resultSet;
		$RI->recordClass=get_called_class();
		$RI->recordTable = static::$tableName;
		$RI->initialize();
		return $RI;
	}
	
	public static function countAll() {
		global $database;
		$sql = "SELECT COUNT(*) FROM ".static::$tableName.";";
		$resultSet = $database->query($sql);
		$row = $database->fetchArray($resultSet);
		$database->freeResult($resultSet);
		return array_shift($row);
	}
		
	public static function countIf($where) {
		global $database;
		$sql = "SELECT COUNT(*) FROM ".static::$tableName." WHERE ".$where.";";
		$resultSet = $database->query($sql);
		$row = $database->fetchArray($resultSet);
		$database->freeResult($resultSet);
		return (int) array_shift($row);
	}
	
	protected static function instantiate($record) {
		// Could check that $record exists and is an array
		global $numObjUsedForPage;
		$className = get_called_class();
		$object = new $className;
		// Simple, long-form approach:
		// $object->id 				= $record['id'];
		// $object->username 	= $record['username'];
		// $object->password 	= $record['password'];
		// $object->first_name = $record['first_name'];
		// $object->last_name 	= $record['last_name'];
		
		// More dynamic, short-form approach:
		foreach($record as $attribute=>$value){
			if($object->hasAttribute($attribute)) {
				$value = str_replace("\'", "'", $value);
				$value = str_replace('\"', '"', $value);
				$value = str_replace("\\r","",$value);
				$value = str_replace("\\n","\n",$value);
				$object->$attribute = h($value);
			}
			$object->numUpdates = $record['numUpdates'];
			$object->dateModified = $record['dateModified'];
		}
		$numObjUsedForPage++;
		return $object;
	}
	
	protected function hasAttribute($attribute) {
	// We don't care about the value, we just want to know if the key exists
	// Will return true or false
		return array_key_exists($attribute, $this->attributes());
	}
	
	protected function attributes() { 
		// return an array of attribute names and their values
		$attributes = array();
		foreach(static::$dbFields as $field) {
			if(property_exists($this, $field)) {
				$attributes[$field] = $this->$field;
			}
		}
		return $attributes;
	}
	
	protected function sanitizedAttributes() {
		global $database;
		$cleanAttributes = array();
		// sanitize the values before submitting
		// Note: does not alter the actual value of each attribute
		foreach($this->attributes() as $key => $value){
			$cleanAttributes[$key] = $database->escapeValue(htmlspecialchars_decode($value));
		}
		$cleanAttributes['numUpdates'] = (int) $this->numUpdates;
		return $cleanAttributes;
	}
	
	public function save() {
	// A new record won't have an id yet.
		$idNameVal = static::$idName;
		return isset($this->$idNameVal) ? $this->update() : $this->create();
	}
	
	public function create() {
		global $database; 
		$attributes = $this->sanitizedAttributes();
		$sql = "INSERT INTO `".static::$tableName."` (`";
		$sql .= join("`, `", array_keys($attributes));
		$sql .= "`) VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "');";
		if($database->query($sql)) {
			$id = $database->insertId();
			$this->setId($id); 
			$logName = static::$tableName;
			$ObjIdName = static::$idName;
			$message       = $ObjIdName . ": " . $id;
			logAction($logName, "Created", $message);
			if($logName!=MAINTENANCE_CLASS){
				\ACHD\Core\SiteMaintenance::exportFullDatabase();
			}
			\ACHD\Core\CoreFunctions::logAction("backupDB","backed up - create");
			return true;
		} else {
			return false;
		}
	}

	public function update() {
		global $database;
		// Don't forget your SQL syntax and good habits:
		// - UPDATE table SET key='value', key='value' WHERE condition
		// - single-quotes around all values
		// - escape all values to prevent SQL injection
		$this->numUpdates +=1;
		$attributes = $this->sanitizedAttributes();
		$attributePairs = array();
		foreach($attributes as $key => $value) {
			$attributePairs[] = "`{$key}`='{$value}'";
		}
		$sql = "UPDATE ".static::$tableName." SET ";
		$sql .= join(", ", $attributePairs);
		$sql .= " WHERE ".static::$idName."='".$this->getId()."';";
		$database->query($sql);
		$logName = static::$tableName;
		$ObjIdName = static::$idName;
		$message       = $ObjIdName . ": " . $this->getId();
		logAction($logName, "Updated", $message);
		if($logName!=MAINTENANCE_CLASS){
			\ACHD\Core\SiteMaintenance::exportFullDatabase();
		}
		\ACHD\Core\CoreFunctions::logAction("backupDB","backed up - update");
		return ($database->affectedRows() == 1) ? true : false;
	}
	
	public function delete() {
		global $database;
		// Don't forget your SQL syntax and good habits:
		// - DELETE FROM table WHERE condition LIMIT 1
		// - escape all values to prevent SQL injection
		// - use LIMIT 1
		$sql = "DELETE FROM ".static::$tableName;
		$sql .= " WHERE ".static::$idName."='{$this->getId()}';";
		$sql .= " LIMIT 1;";
		$database->query($sql);
		$logName = static::$tableName;
		$ObjIdName = static::$idName;
		$message       = $ObjIdName . ": " . $this->getId();
		logAction($logName, "Deleted", $message);	
		if($logName!=MAINTENANCE_CLASS){
			\ACHD\Core\SiteMaintenance::exportFullDatabase();
		}
		\ACHD\Core\CoreFunctions::logAction("backupDB","backed up - delete");
		return ($database->affectedRows() == 1) ? true : false;
		
		// NB: After deleting, the instance of User still 
		// exists, even though the database entry does not.
		// This can be useful, as in:
		//   echo $user->first_name . " was deleted";
		// but, for example, we can't call $user->update() 
		// after calling $user->delete().
	}	
	public function saveAsArray($valsToExclude = array()){
		$attributes = array();
		foreach(static::$dbFields as $field) {
			if(property_exists($this, $field)) {
				if(!\ACHD\Core\Validation::hasInclusionIn($field,$valsToExclude)){
					$attributes[$field] = $this->$field;
				}
			}
		}
		return $attributes;
	}
	public function getImage($imgField){
		//echo $imgField." ".$this->$imgField;
		if ($this->hasAttribute($imgField)){
			if(empty($this->$imgField )|| $this->$imgField == ""){
				$this->$imgField = "default.jpg";
				$this->update();
				\ACHD\Core\CoreFunctions::logAction("defaultImg", "ImageUpdated", "updatedDefaultImage");	
				return "default.jpg";
			} else{
				return $this->$imgField;
			}
		}
	}
	public static function getRandomEntry($method="basic"){
		$now = time();
		$className = get_called_class();
		$count = $className::countAll();
		$randId;
		switch (strtolower($method)){
			case "basic":
				$randId = random_int(1,$count);
				break;
			default:
				$randId = random_int(1,$count);
				break;
		}
		$result = $className::findById($randId);
	//	print_r ($result);
		return $result;
		
	}
	public static function getLatest(){
		$resultArray = static::findBySql("SELECT * FROM ".static::$tableName." ORDER BY ".static::$idName." DESC LIMIT 1;");
		return !empty($resultArray) ? array_shift($resultArray) : false; //st
	}
	public function __set($attribute,$value){
		//echo "setting val";
		$this->$attribute=$value;
	}	
	public function __get($attribute){
		//echo "getting val";
		return $this->$attribute;
	}
	public function __toString(){
		$attributes = $this->saveAsArray();
		$className = get_called_class();
		$output = "Instance of class: ".$className." | Attributes: \n";
		foreach($attributes as $key=>$value){
			$output.="[".$key."] => ".$value."\n";
		} 
		return $output;
	}
}
