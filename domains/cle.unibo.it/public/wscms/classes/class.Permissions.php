<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @copyright 2020 Websync
 * classes/class.Permissions.php v.1.0.0. 08/07/2021
*/

class Permissions extends Core {
	
	static $accessModules = array();
	static $userModules = array();
	static $userModulestree = array();
	
	public function __construct() {
		parent::__construct();
	}
	
	public static function checkCsrftoken($returnUrl = '')
	{
		$csrftoken = filter_input(INPUT_POST, 'csrftoken', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$sessioncsrftoken = $_SESSION['csrftoken'];
		unset($_SESSION['csrftoken']);
		if (!$csrftoken || $csrftoken !== $sessioncsrftoken) {	
			$_SESSION['message'] = '1|La sessione di controllo è scaduta!';
			if ($returnUrl != '') {
				ToolsStrings::redirect($returnUrl); 
			} else {
				ToolsStrings::redirect(URL_SITE_ADMIN.'error/nopm'); 
			}
		}
		return true;
	}
	
	public static function returnCheckCsrftokenStatus()
	{
		$csrftoken = filter_input(INPUT_POST, 'csrftoken', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
		$sessioncsrftoken = $_SESSION['csrftoken'];
		if (!$csrftoken || $csrftoken !== $sessioncsrftoken) {	
			return false;
		}
		return true;
	}

	// controlla se un moduloè leggibile anche se non c'e il code in sessione
	public static function checkIfModulesIsCompanyCodeReadable($moduleName,$userLoggedData,$globalCompanyCodeDefaultCode) 
	{
		$result = false;
		if (isset($userLoggedData->is_root) && $userLoggedData->is_root == 1) {
			$result = true;	
		} else if ($globalCompanyCodeDefaultCode != '') {
			$result = true;
		}
		return $result;
	}

	// controlla se è un utente fornitore
	public static function checkUserIsFornitore($user_details)
	{
		$result = false;
		if (isset($user_details->is_root) && $user_details->is_root == 1) {
			$result = true;
		} else {
			if (isset($user_details->levels_id_alias) && $user_details->levels_id_alias == 0 ) {
				$result = true;
			}
		}
		return $result;
	}

	// controlla se è un utente cliente
	public static function checkUserIsCliente($user_details)
	{
		$result = false;
		if (isset($user_details->is_root) && $user_details->is_root == 1) {
			$result = true;
		} else {
			if (isset($user_details->levels_id_alias) && $user_details->levels_id_alias == 1 ) {
				$result = true;
			}
		}
		return $result;
	}

	public static function checkExistsSessionCode($user_details,$session,$opt=array('exLevelsAliasId'=>'','exMdules'=>'','redirectModule'=>"home")) 
	{
		$optDef = array();	
		$opt = array_merge($optDef,$opt); 
		$result = false;
		if (isset($user_details->is_root) && $user_details->is_root == 1) {
			$result = true;
		} else {
			ToolsStrings::redirect(URL_SITE.$opt['redirectModule']);
			die();
		}
		return $result;
	}

	public static function getQueryParamsForCompaniesCodeAccess($globalCompanyDefaultDetails_code,$user_details,$opt=array()) 
	{
		$optDef = array();	
		$opt = array_merge($optDef,$opt); 
		$tableAlias = (isset($opt['tableAlias']) &&  $opt['tableAlias'] != '' ? $opt['tableAlias'].'.' : '');
		$where = '';
		if (isset($user_details->is_root) && $user_details->is_root == 1) {
		} else {
			$where = "(".$tableAlias."companies_code = '".$globalCompanyDefaultDetails_code."')";
		}
		return $where;
	}

	public static function checkCompaniesCodeAccess($item_details,$item_id,$item_table,$globalCompanyDefaultDetails_code,$user_details) 
	{
		$result = false;
		if (isset($user_details->is_root) && $user_details->is_root == 1) {
			$result = true;
		} else {
			if (!isset($item_details->companies_code)) {
				// preleva il codice 
				Sql::initQuery($item_table,array('companies_code'),array($item_id),'id = ?','');
				$item_details = Sql::getRecord();	
			}
			// esite il codice e lo confronta
			if (isset($item_details->companies_code) && $item_details->companies_code === $globalCompanyDefaultDetails_code) {
				$result = true;
			} else {
			}
		}	

		return $result;
	}

	public static function dbgetObjUserModules()
	{	
		// carica array access modules
		$table = Sql::getTablePrefix().'modules';
		$fields = array('*');
		Sql::initQuery($table,$fields,array(),'active = 1','');
		Sql::setOptions(array('fieldTokeyObj'=>'name'));
		Sql::setOrder('section ASC, ordering ASC');
		$pdoObject = Sql::getPdoObjRecords();
		return($pdoObject);
	}
	
	public static function dbgetUserModules()
	{	
		$pdoObject = self::dbgetObjUserModules();
		while ($row = $pdoObject->fetch()) {
			self::$userModules[$row->name] = $row;				
		}
		//ToolsStrings::dump(self::$userModules);die();
	}

	public static function dbgetUserModulesTree()
	{	
		$pdoObject = self::dbgetObjUserModules();
		while ($row = $pdoObject->fetch()) {
			self::$userModulesTree[$row->section][] = $row;							
		}
		//ToolsStrings::dump(self::$userModules);die();
	}
	
	public static function getLevelModulesRights($levels_id) 
	{
		$table = Sql::getTablePrefix().'modules_levels_access AS mla INNER JOIN '.Sql::getTablePrefix()."modules AS m ON (mla.modules_id = m.id)";
		$fields = array('mla.*,m.name AS module_name');
		Sql::initQuery($table,$fields,array($levels_id),'mla.levels_id = ?','');
		Sql::setOptions(array('fieldTokeyObj'=>'module_name'));
		$obj = Sql::getRecords();	
		return $obj;
	
	}
	
	public static function getUserModules()
	{	
		self::dbgetUserModules();
		//ToolsStrings::dump(self::$userModules);die('fatto');
		return self::$userModules;		
	}

	public static function getUserModulesTree()
	{	
		self::dbgetUserModulesTree();
		//ToolsStrings::dump(self::$userModules);die('fatto');
		return self::$userModulesTree;		
	}
	
	public static function dbgetUserLevelModulesRights($user)
	{	
		//Sql::setDebugMode(1);
		// carica array access modules
		//echo '<br>carica db array access modules';
		$levels_id = (isset($user->levels_id) ? $user->levels_id : 0);

		//echo '$levels_id: '.$levels_id;
		$table = Sql::getTablePrefix().'modules_levels_access AS a INNER JOIN '.Sql::getTablePrefix().'modules AS m ON (a.modules_id = m.id)';
		$fields = array('a.id AS id, a.read_access AS read_access, a.write_access AS write_access,m.name AS module');
		Sql::initQuery($table,$fields,array($levels_id),'a.levels_id = ? AND m.active = 1','');
		Sql::setOptions(array('fieldTokeyObj'=>'module'));
		self::$accessModules = Sql::getRecords();	
		//ToolsStrings::dump(self::$accessModules);
	}
			
	public static function getUserLevelModulesRights($user)
	{	
		self::dbgetUserLevelModulesRights($user);
		return self::$accessModules;		
	}
	
	public static function checkIfModulesIsReadable($module,$user,$opt=array())
	{	
		$result = false;
		//echo '<br>modulo: '.$module;
		//echo 'NON hai accesso';		
		if (isset($user->is_root) && $user->is_root == 1) {
			//echo 'hai accesso 1';
			$result = true;
		} else {
			if (isset(self::$accessModules[$module]->read_access) && self::$accessModules[$module]->read_access == 1) {
				//echo 'hai accesso 2';
				$result = true;
			}
		}			
		// aggiunge il controllo sul core
		if (in_array($module,self::$globalSettings['requestoption']['coremodules'])) {
			$result = true;
			//echo 'hai accesso 3';
		}
       
		return $result;
	}
	
	public static function checkIfUserModuleIsActive($module)
	{
		//print_r(self::$userModules);die();
		//print_r(self::$globalSettings['requestoption']);die();
		$result = false;	
		if (array_key_exists($module,self::$userModules )) {
			$result = true;
		}
		if (in_array($module,self::$globalSettings['requestoption']['othermodules'])) {
			$result = true;
		}
		return $result;	
	}
	
	public static function checkIfModulesIsWritable($module,$user)
	{	
		$result = false;		
		if (isset($user->is_root) && $user->is_root == 1) {
			$result = true;
		} else {
			if (isset(self::$accessModules[$module]->write_access) && self::$accessModules[$module]->write_access == 1) {
				$result = true;
			}
		}			
		return $result;
	}

	public static function getUserLevels()
	{		
		Sql::initQuery(Sql::getTablePrefix().'levels',array('*'),array(),'active = 1','title ASC');
		Sql::setOptions(array('fieldTokeyObj'=>'id'));
		$obj = Sql::getRecords();
		$obj[0] = (object)array('id'=>0,'title'=>'Anonimo','modules'=>'','active'=>1);
		return $obj;		
	}
		
	public static function getUserLevelLabel($user_levels,$id_level,$is_root=0) 
	{
		//ToolsStrings::dump($user_levels);
		//echo '<br>id_level: '.$id_level;
		$s = '';
		if ($is_root == 1) {
			$s = 'Root';
		} else {
			//$s .= $id_level;
			if (is_array($user_levels) && count($user_levels) > 0) {
				foreach($user_levels AS $value) {



					if ($value->id == $id_level) {
						$s = $value->title;
						break;
					}
				}
			}
		}

		//echo 's: '.$s;
		return $s;
	}
		
	public static function checkAccessUserModule($moduleName,$userLoggedData,$userModulesActive) 
	{
		//ToolsStrings::dump($userModulesActive);
		//ToolsStrings::dump($userLoggedData);
		//echo '<br>modulename: '.$moduleName;

		if (isset($userLoggedData->is_root) && $userLoggedData->is_root === 1) {
			return true;
		} else {
			/* se è un modulo cre da l'accsso comunque */
			
			if (is_array($userModulesActive) && in_array($moduleName,$userModulesActive)) {
				 return true;
			} else {
				return false;
			}	
						 	
		}
	}

	public static function getSqlQueryItemPermissionForUser($userLoggedData,$opt=array()) 
	{
		$optDef = array('onlyuser'=>false,'fieldprefix'=>'');	
		$opt = array_merge($optDef,$opt); 
		$clause = '';
		$clauseValues = array();
		
		/* permissionfor user owner only */
		$clause = $opt['fieldprefix'].'users_id = ?';
		$clauseValues[] = $userLoggedData->id;
		
		if ($opt['onlyuser'] == false) {
			// add item public - access_type 0
			$clause .= ' OR '.$opt['fieldprefix'].'access_type = 0';
		}
			
		/* se root azzerra tutto */
		if (isset($userLoggedData->is_root) && intval($userLoggedData->is_root) === 1) {
			$clause = '';
			$clauseValues = array();
		}
		return array($clause,$clauseValues);
	}
		
	public static function  checkReadWriteAccessOfItem($table,$id,$userLoggedData) 
	{
		//print_r($userLoggedData);
		$access = 0;
		/* get item data */
		if ($id > 0) {
			$item = new stdClass;
			Sql::initQuery($table,array('id,users_id,access_type'),array($id),'id = ?');
			$item = Sql::getRecord();
			if (isset($item->id) && $item->id > 0) {

				/* if is ownwer read write */
				if ($userLoggedData->id === $item->users_id) {
					$access = 2;
				}
				
				/* if is not ownwer but item is public read */
				if ($userLoggedData->id <> $item->users_id && $item->access_type == 0) {
					$access = 1;
				}
			
				/* se root set read write */
				if (isset($userLoggedData->is_root) && intval($userLoggedData->is_root) === 1) {
					$access = 2;
				}
			}
		}
		/* if access = 0 go to error */
		if ($access == 0) ToolsStrings::redirect(URL_SITE.'error/access');
		return $access;
	}
		
}
?>
