<?php
// classes/class.ToolsStrings.php v.1.3.0. 08/10/2020

class ToolsStrings extends Core {

	public function __construct() {
		parent::__construct();
	}
	
	public static function dumpArray($array) {
		print("<pre style='font-size:10px'>".print_r($array,true)."</pre>");
	}

	public static function dump($var) {
		print("<pre style='font-size:10px'>".print_r($var,true)."</pre>");
	}

	public static function redirect($url) {
		$protocol = "http://";
		$server_name = $_SERVER["HTTP_HOST"];
		if ($server_name != '') {
			$protocol = "http://";
			if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == "on")) {
				$protocol = "https://";
				}
			if (preg_match("#^/#", (string) $url)) {
				$url = $protocol.$server_name.$url;
				} else if (!preg_match("#^[a-z]+://#", (string) $url)) {
					$script = $_SERVER['PHP_SELF'];
					if (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] != '' && $_SERVER['PATH_INFO'] != $_SERVER['PHP_SELF']) {
						$script = substr((string) $script, 0, strlen((string) $script) - strlen((string) $_SERVER['PATH_INFO']));
						}
					$url = $protocol.$server_name.(preg_replace("#/[^/]*$#", "/", (string) $script)).$url;
					}
			$url = str_replace(" ","%20",$url);
			header("Location: ".$url);
			die();
			}
			exit;
		}
		
	public static function getAlias($oldalias,$alias,$value,$opz) {
		if ($alias == '') $alias = SanitizeStrings::getAliasString($value,[]);
		$aliascheck = false;
		do {
			$check = self::checkIssetAlias($alias,$opz);
			if ($check == true) {
				if($oldalias != $alias) {
					$alias .= (string)random_int(1,10);	
					Core::$resultOp->error = 2;
					Core::$resultOp->messages[] = "Alias cambiato perché GIA' esistente!";
					}			
				}
		} 
		while($aliascheck == true);
		if ($alias == '') $alias .= (string)time();
		return $alias;
		}
		
	public static function checkIssetAlias($alias,$opz) {
		$opzDef = ['idfield'=>'id','aliasfield'=>'alias'];	
		$opz = array_merge($opzDef,$opz);
		$count = 0;
		Sql::initQuery($opz['table'],[$opz['idfield']],[$alias],$opz['aliasfield'].' = ?');
		$count = Sql::countRecord();
		if(Core::$resultOp->error == 0) {
			return ($count == 1 ? true : false);
			} else {
				return true;
				}
		}
		
	public static function getStringFromTotNumberChar($str,$opz){
		$opzDef = ['numchars'=>100,'suffix'=>''];	
		$opz = array_merge($opzDef,$opz);
		$str = strip_tags((string) $str);
		if (strlen($str) > $opz['numchars']) $str = mb_strcut($str,0,$opz['numchars']).$opz['suffix'];
		return $str;
		}

	public static function setNewPassword($caratteri_disponibili,$lunghezza){
		$password = "";
		for($i = 0; $i<$lunghezza; $i++){
			$password = $password.substr((string) $caratteri_disponibili,random_int(0,strlen((string) $caratteri_disponibili)-1),1);
			}
		return $password;
   	}

   /* SPECIFICHE ARRAY */
   
   public static function multiSearch(array $array, array $pairs){
		$found = [];
		foreach ($array as $aKey => $aVal) {
			$coincidences = 0;
			foreach ($pairs as $pKey => $pVal) {
				if (array_key_exists($pKey, $aVal) && $aVal[$pKey] == $pVal) {
					$coincidences++;
					}
				}
			if ($coincidences == count($pairs)) {
				$found[$aKey] = $aVal;
				}
			}
		return $found;
		}
		
	public static function arrayInsert(&$array, $position, $insert){
	    if (is_int($position)) {
	        array_splice($array, $position, 0, $insert);
		    } else {
		        $pos   = array_search($position, array_keys($array));
		        $array = array_merge(
		            array_slice($array, 0, $pos),
		            $insert,
		            array_slice($array, $pos)
		        );
		    }
		}
		
	public static function arrayDeleteByValue($array,$value){
		$key = array_search($value,$array);
		if($key!==false){
			unset($array[$key]);
			}
		return $array;
		}
		
	public static function  multi_array_key_exists($needle, $haystack) 
	{
		foreach ($haystack as $key=>$value) {
			if ($needle===$key) {
				return $key;
				}
			if (is_array($value)) {
				if (self::multi_array_key_exists($needle, $value)) {
					return $key . ":" . self::multi_array_key_exists($needle, $value);
					}
				}
			}
		return false;
	}
		
	 /* SPECIFICHE ARRAY->OBJECT */
	 public static function  findValueInArrayWithObject($arrayobject,$rifobject,$rifvalue,$opt) 
	 {
	 	$optDef = [];	
		$opt = array_merge($optDef,$opt);
	 	$result = false;
	 	if (is_array($arrayobject) && $rifobject != '' && $rifvalue != '') {
			foreach ($arrayobject AS $key=>$value) {
				if (isset($value->$rifobject) && $value->$rifobject == $rifvalue) $result = true;
			}	 	
	 	}
		return $result;
	}

		
	   /* OUTPUT HTML CONTENT */	

	public static function getHtmlContent($obj,$value,$opz) {
		$str = 'error object value';
		$opzDef = [];	
		$opz = array_merge($opzDef,$opz);		
		if (isset($obj->$value)) $str = $obj->$value;	
		$str = self::filterHtmlContent($str,$opz);
		return $str;	
		}

		
	public static function filterHtmlContent($str,$opz) {
		$opzDef = ['htmlout'=>false,'htmlawed'=>true,'parse'=>true,'striptags'=>false];
		$opz = array_merge($opzDef,$opz);		
		
		if ($opz['striptags'] == true) {
			$str = strip_tags((string) $str);
			$opz['htmLawed'] = false;
			}
			
		if ($opz['htmlout'] == true) {
			$str = SanitizeStrings::htmlout($str);
			$opz['htmLawed'] = false;
			}	
			
		if (isset($opz['maxchar']) && $opz['maxchar'] > 0) {
			$str = ToolsStrings::getStringFromTotNumberChar($str,['numchars'=>$opz['maxchar']]);
			$opz['htmLawed'] = false;
			}		
		
		//if ($opz['htmlawed'] == true) $str = htmLawed::hl($str);
		if ($opz['parse'] == true) $str = self::parseHtmlContent($str);
		return $str;	
		}

		
	public static function parseHtmlContent($str,$opz=[]) 
	{		
		$opzDef = ['customtag'=>'','customtagvalue'=>'','parseuploads'=>false];
		$opz = array_merge($opzDef,$opz);
		if ($opz['parseuploads'] == true) {
			$str = preg_replace('/..\/uploads\//',UPLOAD_DIR,(string) $str);
			$str = preg_replace('/uploads\//',UPLOAD_DIR,(string) $str);
			}
		$str = preg_replace('/%AZIENDAREFERENTE%/',(string) self::$globalSettings['azienda referente'],(string) $str);
		$str = preg_replace('/%AZIENDAINDIRIZZO%/',(string) self::$globalSettings['azienda indirizzo'],(string) $str);
		$str = preg_replace('/%AZIENDAPROVINCIA%/',(string) self::$globalSettings['azienda provincia'],(string) $str);
		$str = preg_replace('/%AZIENDAPROVINCIAABBREVIATA%/',(string) self::$globalSettings['azienda targa'],(string) $str);
		$str = preg_replace('/%AZIENDATARGA%/',(string) self::$globalSettings['azienda targa'],(string) $str);
		$str = preg_replace('/%AZIENDACAP%/',(string) self::$globalSettings['azienda cap'],(string) $str);
		$str = preg_replace('/%AZIENDACOMUNE%/',(string) self::$globalSettings['azienda comune'],(string) $str);
		$str = preg_replace('/%AZIENDANAZIONE%/',(string) self::$globalSettings['azienda nazione'],(string) $str);		
		if (isset(Config::$globalSettings['azienda stato'])) $str = preg_replace('/%AZIENDASTATO%/',Config::$globalSettings['azienda stato'],(string) $str);		
		$str = preg_replace('/%AZIENDAEMAIL%/',(string) self::$globalSettings['azienda email'],(string) $str);
		$str = preg_replace('/%AZIENDATELEFONO%/',(string) self::$globalSettings['azienda telefono'],(string) $str);
		$str = preg_replace('/%AZIENDAFAX%/',(string) self::$globalSettings['azienda fax'],(string) $str);
		if (isset(Config::$globalSettings['azienda mobile'])) $str = preg_replace('/%AZIENDAMOBILE%/',Config::$globalSettings['azienda mobile'],(string) $str);
		$str = preg_replace('/%AZIENDACODICEFISCALE%/',(string) self::$globalSettings['azienda codice fiscale'],(string) $str);
		$str = preg_replace('/%AZIENDAPARTITAIVA%/',(string) self::$globalSettings['azienda partita iva'],(string) $str);
		$str = preg_replace('/%AZIENDALATITUDINE%/',(string) self::$globalSettings['azienda latitudine'],(string) $str);
		$str = preg_replace('/%AZIENDALONGITUDINA%/',(string) self::$globalSettings['azienda longitudine'],(string) $str);
		
		$str = preg_replace('/%SITESLOGAN%/',(string) self::$globalSettings['azienda slogan'],(string) $str);
		$str = preg_replace('/%SITENAME%/',(string) self::$globalSettings['azienda sito'],(string) $str);

		$str = preg_replace('/%URLSITE%/',URL_SITE,(string) $str);
		$str = preg_replace('/{{URLSITE}}/',URL_SITE,(string) $str);
		
		if ($opz['customtag'] != '') {
			if ($opz['customtagvalue'] != '') $str = preg_replace('/'.$opz['customtag'].'/',(string) $opz['customtagvalue'],(string) $str);
		}	

		return $str;	
	}	
		
	public static function encodeHtmlContent($str,$opz=[]) {
		$opzDef = ['customtag'=>'','customtagvalue'=>''];
		$opz = array_merge($opzDef,$opz);
		if ($opz['customtag'] != '') {
			if ($opz['customtagvalue'] != '') $str = preg_replace('/'.$opz['customtag'].'/',(string) $opz['customtagvalue'],(string) $str);
			}	
		return $str;	
		}		
	

	}
?>
