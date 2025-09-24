<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @copyright 2020 Websync
 * classes/class.Core.php v.1.0.0. 28/05/2021
*/
class Core extends Config {
	public static $request;
	public static $sessionValues;
	
	public function __construct()
	{			
		parent::__construct();
		self::$sessionValues = array();
	}
		
	public static function init()
	{
		self::$request = new stdclass;
		self::$request->type = 'module';
		self::$request->action = Config::$globalSettings['requestoption']['defaultaction'];
		self::$request->method = '';
		self::$request->param = '';
		self::$request->params = array();
		self::$request->urlparamrequest = array();
		// altre sezioni
		self::$request->page = 0;
		self::$request->lang = '';
		self::$request->templateUser = '';
		// pagina
		self::$request->page_alias = '';
		self::$request->page_id = 0;
	}
		
	public static function getRequest() 
	{	
    $reqs = (empty($_GET['request'])) ? '' : $_GET['request'];
		if (!empty($reqs)) {
			$parts = explode('/', $reqs);		
			$parts = self::parseInitReqs($parts);
		 
			self::$request->action = (isset($parts[0]) ? $parts[0] : Core::$globalSettings['defaultaction']);
			self::$request->method = (isset($parts[1]) ? $parts[1] : '');
			self::$request->param = (isset($parts[2]) ? $parts[2] : '');
			self::$request->params = array();
		} else {
			self::$request->action = Core::$globalSettings['requestoption']['defaultaction'];
			self::$request->param_alias = Core::$globalSettings['requestoption']['defaultaction'];
		}
		
		//ToolsStrings::dump($parts);
		//ToolsStrings::dump(self::$request->params);
		
		if (isset($_POST['action']) && $_POST['action'] != '') self::$request->action = $_POST['action'];
		if (isset($_POST['method']) && $_POST['method'] != '') self::$request->method = $_POST['method'];
		if (isset($_POST['id']) && $_POST['id'] != '') self::$request->param = intval($_POST['id']);
		if (isset($_POST['param']) && $_POST['param'] != '') self::$request->param = $_POST['param'];
		if (isset($_POST['pages']) && $_POST['pages'] != '') self::$request->page = $_POST['page'];
		if (isset($_POST['lang']) && $_POST['lang'] != '') self::$request->lang = $_POST['lang'];

		self::$request->action = (SanitizeStrings::xssClean(self::$request->action) ?? '');
		self::$request->method = SanitizeStrings::xssClean(self::$request->method);
		self::$request->param = SanitizeStrings::xssClean(self::$request->param);
		self::$request->urlparamrequest = $parts;
		
		//ToolsStrings::dump(self::$request->urlparamrequest);
		//die('class core');
		
		if (count(self::$request->params) > 0) {
			foreach (self::$request->params as $key => $value) {
				if (isset(self::$request->params[$key])) self::$request->params[$key] = SanitizeStrings::xssClean(self::$request->params[$key]);
			}
		}
	}			

	public static function parseInitReqs($parts)
	{
		$changeaction = false;
		$action = (isset($parts[0]) ? $parts[0] : '');
		$method = (isset($parts[1]) ? $parts[1] : '');
		$pageaction = $action;
		if (Core::$globalSettings['requestoption']['getlasturlparam'] == true) {
			$pageaction = end($parts);
		}

		$userLoggedData = new stdClass();
		$userLoggedData->is_root = 0;
		if (Core::$globalSettings['requestoption']['isRoot'] == 1) $userLoggedData->is_root = 1;

		if (isset($action) && $action != '') {

			// gestione lingua
			if (in_array('lang', $parts)) {
				$key = array_search('lang', $parts);
				$key1 = $key + 1;
				if (isset($parts[$key1])) $language = $parts[$key1];
				if (in_array($language, Core::$globalSettings['languages'])) {
					self::$request->type = "lang";
					self::$request->lang = $language;
					$_SESSION['lang'] = $language;
					unset($parts[$key]);
					unset($parts[$key1]);
					$parts = array_values($parts);
					$action = (isset($parts[0]) ? $parts[0] : '');
				}
			}

			// gestione template
			if (in_array('settpl', $parts)) {
				$key = array_search('settpl', $parts);
				$key1 = $key + 1;
				$template = $parts[$key1];
				if (in_array($template, Core::$globalSettings['requestoption']['templatesforusers'])) {
					self::$request->type = "template";
					self::$request->templateUser = $template;
					$_SESSION['template'] = $template;
					unset($parts[$key]);
					unset($parts[$key1]);
					$parts = array_values($parts);
					$action = (isset($parts[0]) ? $parts[0] : '');
				}
			}

			//ToolsStrings::dump($parts);
			//echo 'action: '.$action;

			// toglie la page
			if (in_array('page', $parts)) {

				//echo 'toglie la page';
				$key = array_search('page', $parts);
				$key1 = $key + 1;
				if (isset($parts[$key1])) self::$request->page = $parts[$key1];
				array_splice($parts, $key, 2);
			}

			$checkroute = false;

			if ($checkroute == false && isset(Core::$globalSettings['requestoption']['getactionfrommodulefields']) && Core::$globalSettings['requestoption']['getactionfrommodulefields'] != '') {
				$field = Core::$globalSettings['requestoption']['getactionfrommodulefields'];
				$key = array_search($action, array_column(Config::$userModules, $field));
				if ($key != '') {
					//echo '<br>e in un modulo db tramite name';
					$module = ToolsStrings::getArrayFromArrayListByKeyNumber(Config::$userModules, $key);
					if (in_array($parts, Core::$globalSettings['requestoption']['methods'])) {
						Core::$request->method = $parts[0];
						array_shift($parts);
					}
					if (isset($parts[0])) {
						Core::$request->param_id = $parts[0];
						if (isset($parts[1])) Core::$request->param_alias = $parts[1];
					}
					if (isset($parts[0])) $parts[0] = $module->name;
					$action = $module->name;
					$checkroute = true;
				}
			}

			if ($checkroute == false && array_key_exists($action, Config::$userModules)) {
			
			
			
				//echo '<br>e in un modulo db con accesso';

				if (in_array($parts, Core::$globalSettings['requestoption']['methods'])) {
					Core::$request->method = $parts[0];
					array_shift($parts);
				}
				if (isset($parts[0])) {
					Core::$request->param_id = $parts[0];
					if (isset($parts[1])) Core::$request->param_alias = $parts[1];
				}
				if (isset(Core::$globalSettings['requestoption']['getactionfrommodulefields']) && Core::$globalSettings['requestoption']['getactionfrommodulefields'] != '') {
					$field = Core::$globalSettings['requestoption']['getactionfrommodulefields'];
					if (isset($parts[0])) $parts[0] = Config::$userModules[$action]->name;
					$action = Config::$userModules[$action]->name;
				}
				
				//echo '<br>parts';
				//ToolsStrings::dump($parts);
				
				$checkroute = true;
			}

			if ($checkroute == false && in_array($action, Core::$globalSettings['requestoption']['othermodules'])) {
				//echo '<br>e in un modulo other';
				array_shift($parts);
				if (in_array($parts, Core::$globalSettings['requestoption']['methods'])) {
					Core::$request->method = $parts[0];
					array_shift($parts);
				}
				if (isset($parts[0])) {
					Core::$request->param_id = intval($parts[0]);
					Core::$request->param_alias = $parts[0];
					if (isset($parts[1]) && $parts[1] != '') Core::$request->param_alias = $parts[1];
				}
				$arr = array($action);
				$parts = array_merge($arr, $parts);
				$checkroute = true;
			}

			if ($checkroute == false) {
				//echo '<br>e nel modulo default';
				$action = Config::$globalSettings['requestoption']['defaultpagesmodule'];
				$method = (isset(Config::$globalSettings['requestoption']['methods'][0]) ? Config::$globalSettings['requestoption']['methods'][0] : '');
				if (isset($parts[0])) {
					Core::$request->param_id = intval($parts[0]);
					Core::$request->param_alias = $parts[0];
					if (isset($parts[1]) && $parts[1] != '') Core::$request->param_alias = $parts[1];
				}
				$arr = array($action, $method);
				$parts = array_merge($arr, $parts);
			}

			
		}
		
		//ToolsStrings::dump($parts);//die();
		return $parts;
	}

	public static function oldparseInitReqs($parts,$opz) {	
		$changeaction = false;
		$action = (isset($parts[0]) ? $parts[0] : '');
		if (isset($action) && $action != '') {	
			/* controlla se il lingua */
			if (in_array($action,Core::$globalSettings['languages'])) {
				self::$request->type = "lang";
				self::$request->lang = $action;
				unset($parts[0]);
				$parts = array_values($parts);				
				}
				
									
			$action = (isset($parts[0]) ? $parts[0] : '');	
			/* controlla se è nell/elenco moduli */
			Sql::initQuery(Sql::getTablePrefix().'modules',array('id'),array($action),'active = 1 AND alias = ?');
			$obj = Sql::getRecord();
			if (Core::$resultOp->error == 1) die('Errore db lettura moduli!');			
			if (Sql::getFoundRows() == 0) {				
				$changeaction = true;			
				}
				
			if ($changeaction == true && in_array($action,$opz['othermodules'])) {
				$changeaction = false;
				}
			
			
			if ($opz['managechangeaction'] == 1 && $changeaction == true) {
				$arr = array('page');
				$parts = array_merge($arr,$parts);				
				self::$request->type = "page";
				Sql::initQuery(Sql::getTablePrefix().'site_pages',array('id,alias'),array($action),'active = 1 AND (alias = ?)');
				Core::$request->page_data = Sql::getRecord();
				if (Core::$resultOp->error == 1) die('Errore db lettura pagina!');							
				if (isset(Core::$request->page_data->alias)) Core::$request->page_alias = Core::$request->page_data->alias;
				if (isset(Core::$request->page_data->id)) Core::$request->page_id = Core::$request->page_id;
				} else {
					Core::$request->page_alias = $action;
					Core::$request->page_id = 0;
					}
			}
		return $parts;
		}		

	public static function getRequestParam($param) {	
		$paramValue = '';	
		/* se è un method */
		if (self::$request->method === $param && isset(self::$request->param)) $paramValue = self::$request->param;
		/* se è un param */
		if (self::$request->param === $param && isset(self::$request->params[0])) $paramValue = self::$request->params[0];
		/* se è un params */
		if (is_array(self::$request->params) && count(self::$request->params) > 0) {
			$paramKey = -1;
			foreach (self::$request->params AS $key=>$value) {
				if ($value === $param) $paramKey = $key+1;
				/* se trova il key memorizza */
				if ($paramKey == $key && $value != '') $paramValue = $value;
				}
			}
		return $paramValue;
		}
		
	public static function createUrl($opz=array()) {
		
		/* opzioni */
		$otherparams = (isset($opz['otherparams']) ? $opz['otherparams'] : '');
		$parampage = (isset($opz['parampage']) ? $opz['parampage'] : true);		
				
		$url_arr = array();		
		if (self::$request->action != '') {
			$url_arr[] = self::$request->action;
			}
		if (self::$request->method != '') {
			$url_arr[] = self::$request->method;
			}
		if (self::$request->param != '') {
			$url_arr[] = self::$request->param;
			}
		
		if (isset(self::$request->params) && is_array(self::$request->params) && count(self::$request->params) > 0) {
			$url_arr = array_merge($url_arr,self::$request->params);
			}	
			
		/* aggiungi alti parametri se presenti */
		if (is_array($otherparams) && count($otherparams) > 0) {
			foreach ($otherparams AS $key=>$value) {
				$url_arr[] = $key;
				$url_arr[] = $value;
				}
			}	
			
		/* elimina il parametro page */
		if ($parampage == false) {
			$key = array_search('page',$url_arr);
			unset($url_arr[$key]);
			unset($url_arr[$key+1]);
			}			
		$url = URL_SITE.implode('/',$url_arr);
		return $url;
		}	
		
			
	public static function setDebugMode($value){
		self::$debugMode = $value;
		}
		
	public static function resetResultOp($value){
		self::$resultOp->type =  0;
		self::$resultOp->message =  '';
		self::$resultOp->messages =  array();	
		}
	
	public static function resetMessageToUser($value){
		self::$messageToUser->type =  0;
		self::$messageToUser->message =  '';
		self::$messageToUser->messages =  array();	
		}				

	}
