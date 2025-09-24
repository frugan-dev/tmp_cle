<?php
/*
	framework siti html-PHP-Mysql
	copyright 2011 Roberto Mantovani
	http://www.robertomantovani.vr.it
	email: me@robertomantovani.vr.it
	admin/classes/class.Fornitore.php v.1.0.0. 28/05/2020
*/

class Fornitori extends Core {

	public static $fornitoriList;
	public static $fornitoreDetails;
	public static $fornitoreCompanyDetails;
	public static $fornitoreOrders;
	public static $fornitoreFasceOrders;

	public static $optGetCompaniesCode;
	public static $optGetCompanyDetails;
	public static $optGetUserDetails;
	
    public function __construct(){
		parent::__construct();  		
	}
	
	public static function getFornitoreFasceOrdiniFromCode($companies_code = '',$opt=[])
	{
		$optDef = [];	
		$opt = array_merge($optDef,$opt);
		//Core::setDebugMode(1);
		$table = Config::$DatabaseTables['ass_fasce_fatturazioni_ordini_companies_code'].' AS assfafattor';
		$table .= ' INNER JOIN '.Config::$DatabaseTables['fasce_fatturazioni_ordini'].' AS fafattor 
		ON (assfafattor.fasce_fatturazioni_ordini_id = fafattor.id)';
		$f = ['assfafattor.*','fafattor.*'];
		$fv = [$companies_code];
		$clause = 'companies_code = ?';
		Sql::initQuery($table,$f,$fv,$clause,'assfafattor.fasce_fatturazioni_ordini_id ASC','');
		$foo = Sql::getRecords();
		
		if (Core::$resultOp->error > 0) { die('errore db get fasceordini fornitore'); ToolsStrings::redirect(URL_SITE.'error/db'); }
		self::$fornitoreFasceOrders = $foo;
		return self::$fornitoreFasceOrders;
	}
	
	public static function getFornitoreNumberOrdersFromCode($companies_code = '',$opt=[])
	{
		$optDef = [];	
		$opt = array_merge($optDef,$opt);
		$fv = [$companies_code];
		$clause = 'companies_code = ?';
		$foo = Sql::countRecordQry(Config::$DatabaseTables['orders'],'id',$clause,$fv);
		return $foo;	
	}

	public static function getFornitoreOrdersFromCodePdoObject($companies_code,$opt=[]) 
	{

		$optDef = [];	
		$opt = array_merge($optDef,$opt);
		//Core::setDebugMode(1);
		if ($companies_code != '')
		{
			$table = Config::$DatabaseTables['orders'];
			$f = ['*'];
			$fv = [$companies_code];
			$clause = 'companies_code = ?';
			$and = ' and ';

 			if (Config::$queryParams['where'] != '')
			 {
				$clause .=  $and . Config::$queryParams['where'];
				if (is_array(Config::$queryParams['where'])) $fv = array_merge($fv,Config::$queryParams['where']);
				$and =Config::$queryParams['where'];
			 }
			//ToolsStrings::dump($fv);	
			Sql::initQuery($table,$f,$fv,$clause,'','');
			$pdoObject = Sql::getPdoObjRecords();
			if (Core::$resultOp->error > 0) { /*ToolsStrings::redirect(URL_SITE.'error/db'); */ die('errore db lettura ordini fornitore'); }
			return $pdoObject;
		}
		return false;
	}

	public static function getFornitoreOrdersFromCode($companies_code,$opt=[]) 
	{
		$optDef = [];	
		$opt = array_merge($optDef,$opt);
		if ($companies_code != '')
		{
			$pdoObject = self::getFornitoreOrdersFromCodePdoObject($companies_code,$opt=[]);
			if (Core::$resultOp->error > 0) { /*ToolsStrings::redirect(URL_SITE.'error/db'); */ die('errore db lettura ordini fornitore'); }
			$foo = [];
			while ($row = $pdoObject->fetch()) {
				if (self::$optGetUserDetails == true && isset($row->users_id)) {

					$row->user_details = new stdClass();
					$row->user_details= Users::getUserDetails($row->users_id);
				}
				$foo[] = $row;
			}
			self::$fornitoreOrders = $foo;
			return self::$fornitoreOrders;
		}
		return false;
	}

	public static function getFornitoreCompanyDetailsFroCode($companies_code)
	{
		if ($companies_code != '')
		{
			$table = Config::$DatabaseTables['companies'];
			$f = ['*'];
			$fv = [$companies_code];
			$clause = 'code = ?';
			Sql::initQuery($table,$f,$fv,$clause,'','');
			$foo = Sql::getRecord();
			if (Core::$resultOp->error > 0) { /*ToolsStrings::redirect(URL_SITE.'error/db'); */ die('errore db lettura azienda fornitore'); }
			self::$fornitoreCompanyDetails = $foo;
			return self::$fornitoreCompanyDetails;					
		}
		return false;
	}

	public static function getFornitoreDetailsById($id)
	{
		if (intval($id) > 0) 
		{
			//Sql::setDebugMode(1);
			$table = Config::$DatabaseTables['users'].' AS users';
			$f = ['users.*'];
			//$f = array('prod.id,prod.title,prod.hide_users_ids');
			$fv = [$id];
			$clause = 'users.id = ?';
			$and = ' AND ';

			if (self::$optGetCompaniesCode == true) {
				$table .= ' INNER JOIN '.Config::$DatabaseTables['ass_companies_code_users'].' AS ass_code_users ON (ass_code_users.users_id = users.id)';
				$f[] = 'ass_code_users.companies_code';
			}
				
			Sql::initQuery($table,$f,$fv,$clause,'','');
			$foo = Sql::getRecord();
			if (Core::$resultOp->error > 0) { /*ToolsStrings::redirect(URL_SITE.'error/db'); */ die('errore db lettura fornitore'); }
			self::$fornitoreDetails = $foo;
			
			if (self::$optGetCompanyDetails == true && isset( self::$fornitoreDetails->companies_code )) {
				self::$fornitoreDetails->company = self::getFornitoreCompanyDetailsFroCode( self::$fornitoreDetails->companies_code );

			}
			
			self::$fornitoreDetails = $foo;
			return self::$fornitoreDetails;
		}
		return false;
	}

	public static function getFornitoreDetailsByCompaniesCode($code)
	{
 		if ($code != '') 
		{
			//Sql::setDebugMode(1);
			$table = Config::$DatabaseTables['ass_companies_code_users'].' AS ass';
			$table .= ' INNER JOIN '.Config::$DatabaseTables['users'].' AS users ON (users.id = ass.users_id)';
			$f = ['ass.*','users.*'];
			$fv = [$code];
			$clause = 'ass.companies_code = ? AND users.active = 1';
			$and = ' AND ';

			Sql::initQuery($table,$f,$fv,$clause,'','');
			$foo = Sql::getRecord();
			if (Core::$resultOp->error > 0) { /*ToolsStrings::redirect(URL_SITE.'error/db'); */ die('errore db lettura fornitore'); }
			self::$fornitoreDetails = $foo;
			return self::$fornitoreDetails;	
		}
		return false;
	}

	public static function getFornitoriList()
	{
		//Sql::setDebugMode(1);
		Sql::initQuery(Config::$DatabaseTables['users'],['*'],[],'levels_id_alias = 0','','');
		$foo = Sql::getRecords();
		if (Core::$resultOp->error > 0) { ToolsStrings::redirect(URL_SITE.'error/db'); die('errore db lettura fornitori'); }
		self::$fornitoriList = $foo;
		return self::$fornitoriList;
	}
	
}