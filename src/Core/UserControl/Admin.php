<?php
namespace ACHD\Core\UserControl
class Admin extends ActiveRecordBase{
	protected static $tableName = "admins";
	protected static $idName = "adminId";
	protected static $dbFields = array('userId', 'adminId');
	public $userId;
	public $adminId;


	public static function findAllByAdminId(){
		return self::findBySql("SELECT * FROM admins ORDER BY adminId ASC;");
	}
	public static function findByUserId($userId = 1){
		$resultArray = self::findBySql("SELECT * FROM admins WHERE userId = '{$userId}' LIMIT 1;");
		return !empty($resultArray) ? array_shift($resultArray) : false;
	}

}
