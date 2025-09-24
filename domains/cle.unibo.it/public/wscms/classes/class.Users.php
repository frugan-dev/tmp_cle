<?php
/*	classes/class.Users.php v.1.0.0. 11/02/2021 */

class Users extends Core {

	public static $dbTable;

	public static $whereDbClause = array();
	public static $fieldsDbSelect = array('users.*');
	public static $fieldsDbValues = array();
	public static $clauseQueryDb = '';
	public static $details;

	public static $queryParams = array();

	public static $qryFields = array('*');
	public static $qryFieldsValues = array();
	public static $qryClause = '';
	public static $qryAndClause = '';
	public static $qryOrder = '';
	public static $qryLimit = '';

	public static $HideLevelsIdAliasInQuery = '';
	public static $HideRootInQuery = true;

	public static $ViewLevelsIdAliasInQuery = '';

	public static $UserCompaniesCode = '';

	public static $langUser = 'it';


	public function __construct() 
	{
		parent::__construct();	
	}

	public static function initsetQueryParams() {
		self::$queryParams = array(
			'tables'			=> self::$dbTable.' AS users',
			'fields'			=> array('users.*'),
			'fieldsValues'		=> array(),
			'whereClause'		=> '',
			'whereClauseAnd'	=> '',
			'order'				=> 'users.surname ASC, users.name ASC',
			'tablePrefix'		=> ''
		);
	}



	public static function checkIfUsersExist() {
		return Sql::checkIfRecordExists();
	}

	public static function getUsersFromCompaniesCode() 
	{
		//echo 'code'.self::$UserCompaniesCode;
		$foo = array();
		//Core::setDebugMode(1);
		self::$queryParams['tables'] = 	Sql::getTablePrefix().'ass_companies_code_users AS ass INNER JOIN '.self::$dbTable.' AS users ON (ass.users_id = users.id)';
		self::$queryParams['fieldsValues'] = array(
			'ass.*',
			'users.*',
			'(SELECT comuni.nome FROM '.Sql::getTablePrefix().'location_comuni AS comuni WHERE comuni.id = users.location_comuni_id) AS comune',
			'(SELECT province.nome FROM '.Sql::getTablePrefix().'location_province AS province WHERE province.id = users.location_province_id) AS provincia',
			'(SELECT nations.title_'.self::$langUser.' FROM '.Sql::getTablePrefix().'location_nations AS nations WHERE nations.id = users.location_nations_id) AS nations'
		);
		self::$queryParams['fieldsValues'] = array(self::$UserCompaniesCode);
  		self::$queryParams['whereClause'] = 'ass.companies_code = ?';

		Sql::initQueryBasic(
			self::$queryParams['tables'],
			self::$queryParams['fields'],
			self::$queryParams['fieldsValues'],
			self::$queryParams['whereClause'],
			self::$queryParams['order']
		);

		$pdoObject = Sql::getPdoObjRecords();
		if (Core::$resultOp->error > 0) { die('errore db get records users');	ToolsStrings::redirect(URL_SITE.'error/db'); }
		while ($row = $pdoObject->fetch()) {
			$foo[] = $row;
		}
		return $foo;
	}

	public static function getUsersList() 
	{
		//Core::setDebugMode(1);
		$obj = array();

		// nascondi root
		if (self::$HideRootInQuery == true) {
			self::$queryParams['whereClause'] .= self::$queryParams['whereClauseAnd'] .self::$queryParams['tablePrefix'].'is_root = 0';
			self::$queryParams['whereClauseAnd'] = ' AND ';
		}

 		// nascondi levels_id_alias_ids
		if (is_array(self::$HideLevelsIdAliasInQuery) && count(self::$HideLevelsIdAliasInQuery) > 0) {
			$subwhere = array();
			foreach (self::$HideLevelsIdAliasInQuery As $value) {
				$subwhere[] = self::$queryParams['tablePrefix'].'levels_id_alias <> ?';
				self::$queryParams['fieldsValues'][] = $value;
			}
			if (count($subwhere) > 0) {
 				self::$queryParams['whereClause'] .= self::$queryParams['whereClauseAnd'] .'('.implode(' AND ',$subwhere).')';
				self::$queryParams['whereClauseAnd'] = ' AND ';
			}
		}

		// visualizza solo  levels_id_alias_ids
		if (is_array(self::$ViewLevelsIdAliasInQuery) && count(self::$ViewLevelsIdAliasInQuery) > 0) {
			$subwhere = array();
			foreach (self::$ViewLevelsIdAliasInQuery As $value) {
				$subwhere[] = self::$queryParams['tablePrefix'].'levels_id_alias = ?';
				self::$queryParams['fieldsValues'][] = $value;
			}
			if (count($subwhere) > 0) {
 				self::$queryParams['whereClause'] .= self::$queryParams['whereClauseAnd'] .'('.implode(' AND ',$subwhere).')';
				self::$queryParams['whereClauseAnd'] = ' AND ';
			}
		}

		//ToolsStrings::dump(self::$queryParams['fieldsValues']);
		Sql::initQueryBasic(
			self::$queryParams['tables'],
			self::$queryParams['fields'],
			self::$queryParams['fieldsValues'],
			self::$queryParams['whereClause'],
			self::$queryParams['order']
		);

		$pdoObject = Sql::getPdoObjRecords();
		if (Core::$resultOp->error > 0) { die('errore db get records users');	ToolsStrings::redirect(URL_SITE.'error/db'); }
		while ($row = $pdoObject->fetch()) {
			$obj[] = $row;
		}
		return $obj;
	}

	public static function oldGetUsersList() 
	{
		//Core::setDebugMode(1);
		$obj = array();
		$table = self::$dbTable.' AS users';
		Sql::initQueryBasic($table,self::$fieldsDbSelect,self::$fieldsDbValues,self::$clauseQueryDb,'','',false);
		$pdoObject = Sql::getPdoObjRecords();
		if (Core::$resultOp->error > 0) { die('errore db get records users');	ToolsStrings::redirect(URL_SITE.'error/db'); }
		while ($row = $pdoObject->fetch()) {
			$obj[] = $row;
		}
		return $obj;
	}
	
	public static function getUserDetails($id) 
	{
		//Core::setDebugMode(1);
		$obj = new stdClass();
		Sql::initQuery(
			self::$dbTable.' AS users',
			array(
				'users.*',
				'(SELECT comuni.nome FROM '.Sql::getTablePrefix().'location_comuni AS comuni WHERE comuni.id = users.location_comuni_id) AS comune',
				'(SELECT province.nome FROM '.Sql::getTablePrefix().'location_province AS province WHERE province.id = users.location_province_id) AS provincia',
				'(SELECT nations.title_'.self::$langUser.' FROM '.Sql::getTablePrefix().'location_nations AS nations WHERE nations.id = users.location_nations_id) AS nations'
			),
			array($id),
			'users.id = ?','','',false);		
		$obj = Sql::getRecord();
		//ToolsStrings::dump($obj);die();
		if (Core::$resultOp->error > 0) { die('errore db get record user'); ToolsStrings::redirect(URL_SITE.'error/db'); }	
		return $obj;	
	}

	public static function getUserDetailsFromCompaniesCode($code) 
	{
		//Core::setDebugMode(1);
		$obj = new stdClass();
		Sql::initQuery(
			Sql::getTablePrefix().'ass_companies_code_users AS ass
			INNER JOIN '.self::$dbTable.' AS users ON (ass.users_id = users.id)',
			array(
				'ass.*',
				'users.*',
				'(SELECT comuni.nome FROM '.Sql::getTablePrefix().'location_comuni AS comuni WHERE comuni.id = users.location_comuni_id) AS comune',
				'(SELECT province.nome FROM '.Sql::getTablePrefix().'location_province AS province WHERE province.id = users.location_province_id) AS provincia',
				'(SELECT nations.title_'.self::$langUser.' FROM '.Sql::getTablePrefix().'location_nations AS nations WHERE nations.id = users.location_nations_id) AS nations'
			),
			array($code),
			'ass.companies_code = ? and levels_id_alias = 0',
			'',
			'',
			false
		);			
		$obj = Sql::getRecord();
		//ToolsStrings::dump($obj);die();
		if (Core::$resultOp->error > 0) { /*die('errore db get record');*/ ToolsStrings::redirect(URL_SITE.'error/db'); }	
		return $obj;	
	}

	public static function createHash($sitecodekey,$username,$email) {
		return sha1($sitecodekey.$username.$email);
	}

	public static function add() 
	{
		//Sql::setDebugMode(1);
		$f = array();
		$fv = array();
	
		foreach (self::$details AS $key=>$value) {
			$f[] = $key;
			$fv[] = $value;
		}
		/*
		ToolsStrings::dumpArray($f);
		ToolsStrings::dumpArray($fv);
		*/
		Sql::initQuery(self::$dbTable,$f,$fv);
		Sql::insertRecord();		
	}

	public static function parseEmailText($text,$opt=array()) {
		$optDef = array('customFields'=>array(),'customFieldsValue'=>array());	
		$opt = array_merge($optDef,$opt);	
		$text = preg_replace('/%SITENAME%/',SITE_NAME,$text);
		if ((is_array($opt['customFields']) && count($opt['customFields'])) 
			&& (is_array($opt['customFieldsValue']) && count($opt['customFieldsValue'])) 
			&& (count($opt['customFields']) == count($opt['customFieldsValue']))
			) {			
			foreach ($opt['customFields'] AS $key=>$value) {
				$text = preg_replace('/'.$opt['customFields'][$key].'/',$opt['customFieldsValue'][$key],$text);
			}
		}
		if (isset(self::$details->username)) $text = preg_replace('/%USERNAME%/',self::$details->username,$text);
		if (isset(self::$details->email)) $text = preg_replace('/%EMAIL%/',self::$details->email,$text);
		return $text;
	}
		
}
?>