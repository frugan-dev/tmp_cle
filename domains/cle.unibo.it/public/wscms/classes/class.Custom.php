<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @copyright 2020 Websync
 * classes/class.Custom.php v.1.0.0. 08/07/2021
*/
class Custom extends Core {

	public function __construct() 	{
		parent::__construct();
	}

	public static function checkIfCompaniesCodeExists($companies_code) 
	{
		//Config::$debugMode = 1;
		$res = false;
		$count = Sql::countRecordQry( Config::$dbTablePrefix.'companies','id','code = ?',[$companies_code]);
		if (Config::$resultOp->error > 0) die('Errore db lettura code companies');
		if ($count > 0) {
			$res = true;
		}
		return $res;
	}

	public static function createNewCode()
	{
		set_time_limit (50);
		$x = 0;
		$code = '';
		while(true) {
			$code = ToolsStrings::setNewPassword('ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',8);
			if (self::checkIfCompaniesCodeExists($code) == false) {
				break;
			}
		};
		return $code;
	}

}
?>