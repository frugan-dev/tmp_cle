<?php 
/**
 * Framework siti html-PHP-Mysql
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * admin/classes/class.Multilanguage.php v.2.6.4. 30/07/2016
*/

class Multilanguage extends Core {

	public function __construct() 	{
		parent::__construct();
		}
		
	private static function getString($str,$opz){
		$opzDef = ['htmlawed'=>false,'numchars'=>'','tagp'=>'','htmlout'=>false,'striptags'=>false,'xss'=>false,'urlslug'=>false,'deletetagp'=>false,'striptags'=>false,'parse'=>false];	
		$opz = array_merge($opzDef,$opz);	

		if ($opz['parse'] == true) $str = ToolsStrings::parseHtmlContent($str);
		
		/* opzioni */
		if ($opz['deletetagp'] == true) {
			$str = ltrim((string) $str,'<p>');
			$str = rtrim($str,'</p>');
			}
			
		if ($opz['htmlout'] == true) $str = SanitizeStrings::htmlout($str);
		if ($opz['urlslug'] == true) $str = SanitizeStrings::urlslug($str);
		if ($opz['htmlawed'] == true) $str = htmLawed::hl($str);	
		if ($opz['xss'] == true) $str = SanitizeStrings::xss($str);
		if ($opz['striptags'] == true) $str = strip_tags((string) $str);	
				
		if ($opz['numchars'] != '') {
			$arr = [];
			$arr['numchars'] = $opz['numchars'];
			if (isset($opz['suffix'])) $arr['suffix'] = $opz['suffix'];
			$str = ToolsStrings::getStringFromTotNumberChar($str,$arr);
			}

		return $str;		
		}
		
	public static function getLocaleObjectValue($obj,$field,$localesuffix,$opz){
		$str = '';
		$opzDef = ['deflocalesuffix'=>'it'];	
		$opz = array_merge($opzDef,$opz);			
		$rifObj = $field.$localesuffix;
		$rifObjDef = $field.$opz['deflocalesuffix'];		
		if (isset($obj->$rifObj) && $obj->$rifObj != '') {
			$str = $obj->$rifObj;
			} else if (isset($obj->$rifObjDef)) {
				$str = $obj->$rifObjDef;				
				}
		$str = self::getString($str,$opz);
		return $str;
		}
		
	public static function getLocaleArrayValue($array,$field,$localeSuffix,$params){
		$str = '';
		$defLocaleSuffix = 'it';
		$defLocaleSuffix = ($params['deflocalesuffix'] ?? $defLocaleSuffix);	
		$rifString = $field.$localeSuffix;
		$rifStringDef = $field.$defLocaleSuffix;
		
		if (isset($array[$rifString]) && $array[$rifString] != '') {
			$str = $array[$rifString];
			} else if (isset($array[$rifStringDef])) {
				$str = $array[$rifStringDef];				
				}
				
		$str = static::getString($str,$params);
		return $str;
		}

	
	public static function getLanguageUrl($action,$method,$param,$params,$lang)
	{
		$strUrl = URL_SITE;
		$url = [];
		$urlFinal = [];
		if ($action != '') $url[] = $action;
		if ($method != '') $url[] = $method;
		if ($param != '') $url[] = $param;
		if (is_array($params) && count($params) > 0) {
			array_merge($url,$params);	
		}
		if (is_array($url) && count($url) > 0) {	
			$k = array_search('lang', $url);
			if ($k > 0) {
				if (isset($url[$k])) unset($url[$k]);
				if (isset($url[$k+1])) unset($url[$k+1]);
			}
			$strUrl .= implode('/',$url).'/';	
		}		 
		$strUrl = rtrim($strUrl,'/').'/lang/'.$lang;
		return $strUrl;
	}
}