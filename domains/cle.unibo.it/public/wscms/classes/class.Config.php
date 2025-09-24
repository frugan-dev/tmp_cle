<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @copyright 2020 Websync
 * classes/class.Config.php v.1.0.0. 28/05/2021
*/
class Config {
	static $confArray;
	public static $resultOp;
	public static $messageToUser;
	public static $debugMode;
	public static $moduleConfig;	
	public static $dbName;
	public static $databaseUsed; // locale o remoto
	public static $dbTablePrefix;
	public static $dbConfig;
	public static $globalSettings;

	public static $defPath;
	public static $langVars;

	public static $nowDateIso;
	public static $nowDateTimeIso;
	public static $nowTimeIso;

	public static $nowDateIta;
	public static $nowDateTimeIta;
	public static $nowTimeIta; 
	
	public static $userLoggedDataId;

	/*
	public static $whereClauseDbQuery;
	public static $whereAndClauseDbQuery;
	public static $fieldsValuesDbQuery;
	public static $fieldsDbQuery;
	*/

	public static $queryParams;

	public static $dbDebugMode; // @int(1) abilita il debus delle query database
	public static $hideItemNoActiveSelectQuery;
	public static $hideParentItemNoActiveSelectQuery = true;

	public static $modules;
	public static $userModules;
	public static $userModulesTree;
	public static $userLevels;

	public static $DatabaseTables;
	public static $DatabaseTablesFields;
		
	public function __construct()
	{	
	}	
		
	public static function init() 
	{

		self::$resultOp =  new stdclass;
		self::$resultOp->type = 0;
		self::$resultOp->error =  0;
		self::$resultOp->message =  '';
		self::$resultOp->messages =  [];	
		self::$messageToUser =  new stdclass;
		self::$messageToUser->type =  0;
		self::$messageToUser->message =  '';
		self::$messageToUser->messages =  [];
		self::$debugMode = 0;
		self::$dbName = DATABASE;
		self::$databaseUsed = DATABASEUSED;

		self::$nowDateIso = date('Y-m-d');
		self::$nowDateTimeIso = date('Y-m-d H:i:s');
		self::$nowTimeIso = date('H:i:s');

		self::$nowDateIta = date('d/m/Y');
		self::$nowDateTimeIta = date('d/m/Y H:i:s');
		self::$nowTimeIta = date('H:i:s'); 

		self::$dbTablePrefix = self::$globalSettings['database'][self::$databaseUsed]['tableprefix'];

		self::$defPath = PATH;
		self::$userLoggedDataId = 0;

		self::$queryParams = [];
		self::$dbDebugMode = 0;


		self::$hideItemNoActiveSelectQuery = false;
		self::$hideParentItemNoActiveSelectQuery = true;

		// init sessioni
		if (!isset($_SESSION['lang'])) $_SESSION['lang'] =  self::$globalSettings['default language'];


		// carica i dati modulo
		foreach(Core::$globalSettings['module sections'] AS $key=>$value) {
			Sql::initQuery(Config::$dbTablePrefix.'modules',['*'],[$key],'active = 1 AND section = ?','ordering ASC');
			self::$modules[$key] = Sql::getRecords();
			if (self::$resultOp->error == 1) die('Errore db livello utenti!');
		}

		// carica i moduli utente
		self::$userModules = Permissions::getUserModules();
		self::$userModulesTree = Permissions::getUserModulesTree();

		// carica i livelli utente
		self::$userLevels = Permissions::getUserLevels();
		//ToolsStrings::dump(self::$userLevels);

	}

	public static function initDatabaseTables($path = '') 
	{
		// carica file 
		if (file_exists($path."wscms/include/configuration_database_core_structure.php")) {
			include_once($path."wscms/include/configuration_database_core_structure.php");
		} else {
			die('il file '.$path.'wscms/include/configuration_database_core_structure.php non esiste!');
		}
		$tables = $DatabaseTables;
		$fields = $DatabaseTablesFields;	

		if (file_exists($path."wscms/include/configuration_database_modules_structure.php")) {
			include_once($path."wscms/include/configuration_database_modules_structure.php");
		} else {
			die('il file '.$path.'wscms/include/configuration_database_modules_structure.php non esiste!');
		}
		$tables1 = $DatabaseTables;
		$fields1 = $DatabaseTablesFields;

		self::$DatabaseTables = array_merge($tables,$tables1);
		self::$DatabaseTablesFields = array_merge($fields,$fields1);
		//ToolsStrings::dump(self::$DatabaseTables);
	}
	
	public static function loadLanguageVars($currentlanguage)
	{
		if ($currentlanguage != '') {
			if (file_exists(PATH."wscms/languages/".$currentlanguage.".inc.php")) {
				require_once(PATH."wscms/languages/".$currentlanguage.".inc.php");
			} else {
				require_once(PATH."wscms/languages/it.inc.php");
			}

			if (file_exists(PATH."languages/".$currentlanguage.".inc.php")) {
				include_once(PATH."languages/".$currentlanguage.".inc.php");
			} else {
				include_once(PATH."languages/en.inc.php");
			}	
		} else {
			include_once(PATH."wscms/languages/en.inc.php");
			include_once(PATH."languages/en.inc.php");
		}

		//ToolsStrings::dump($_lang);
		////ToolsStrings::dump(self::$langVars);
		self::$langVars = $_lang;
		//die('fatto');


	}
	
	public static function loadLanguageVarsAdmin($currentlanguage)
	{
		if ($currentlanguage != '') {
			if (file_exists(PATH."languages/".$currentlanguage.".inc.php")) {
				require_once(PATH."languages/".$currentlanguage.".inc.php");
			} else {
				require_once(PATH."languages/it.inc.php");
			}					
		} else {
			require_once(PATH."languages/it.inc.php");
		}
		self::$langVars = $_lang;
	}

	public static function loadCoreLanguages() {
		// carica lingue 
		if (self::$globalSettings['default language'] != '') {
			if (file_exists(self::$defPath."languages/".self::$globalSettings['default language'].".inc.php")) {
				include_once(self::$defPath."languages/".self::$globalSettings['default language'].".inc.php");
			} else {
				include_once(self::$defPath."languages/it.inc.php");				
			}
		} else {		
			include_once(self::$defPath."languages/it.inc.php");
		}
		self::$langVars = $_lang;
	}

	/*
	public static function initDatabaseTables() 
	{
		// carica file 
		if (file_exists(self::$defPath."include/configurationdatabasestructure.php")) {
			include_once(self::$defPath."include/configurationdatabasestructure.php");
		} else {
			die('il file '.self::$defPath.'include/configurationdatabasestructure.php non esiste!');
		}
		self::$DatabaseTables = $DatabaseTables;
		self::$DatabaseTablesFields = $DatabaseTablesFields;
		//ToolsStrings::dump(self::$DatabaseTables);
		//ToolsStrings::dump(self::$DatabaseTablesFields);	
	}
	*/

	public static function checkModuleConfig($table,$configs) {	
		if (Sql::tableExists($table) == true) {
		/* legge la configurazione */
		self::$moduleConfig = new stdClass();
		Sql::initQuery($table,['*'],[],'active = 1');
		Sql::setOptions(['fieldTokeyObj'=>'name']);
		self::$moduleConfig = Sql::getRecords();
		
		/* controlla se ci sono i parametri richiesti */
		if (is_array($configs) && count($configs) > 0) {
			foreach ($configs AS $value) {
				if (!isset(self::$moduleConfig[$value['name']]) || (isset(self::$moduleConfig[$value['name']]) && self::$moduleConfig[$value['name']]->name == '')) {
					self::$resultOp->error = 1;
					self::$resultOp->messages[] = 'Il parametro di configurazione "'.$value['name'].'" non è presente oppure è vuoto!';
					}
				}
			}
		
		if(self::$resultOp->type == 1) {
			self::$resultOp->error = 1;
			self::$resultOp->messages[] = 'La tabella della configurazione non è presente!';
			} else {
				/* controlla se ci sono presenti le configurazioni */
				}
		} else {
			self::$resultOp->error = 1;
			self::$resultOp->messages[] = 'La tabella della configurazione non è presente!';
			}
		}

	public static function read($name)
	{
		return self::$confArray[$name];
	}
	
	public static function write($name, $value) 
	{
		self::$confArray[$name] = $value;
	}

	public static function setGlobalSettings($globalSettings) 
	{
		self::$globalSettings = $globalSettings;
	}
		
	public static function getDatabaseSettings() 
	{
		$dbConfig = self::$globalSettings['database'][self::$dbName];
		return $dbConfig;
	}
		
	public static function setDatabase($database) 
	{
		self::$dbName = $database;
	}

	public static function setLangVars($value) 
	{
		$foo = $value;
	}

	public static function initQueryParams()
	{
		self::$queryParams = [];
		self::$queryParams['tables'] = '';
		self::$queryParams['fields'] = [];
		self::$queryParams['fieldsVal'] = [];
		self::$queryParams['where'] = '';
		self::$queryParams['and'] = '';


	}

}
?>
