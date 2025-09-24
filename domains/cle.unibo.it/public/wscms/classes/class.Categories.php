<?php
/*
	Framework App PHP-Mysql
	copyright 2011 Roberto Mantovani
	http://www.robertomantovani.vr;it
	email: me@robertomantovani.vr.it
	classes/class.Categories.php v.1.0.0. 27/03/2018
*/
class Categories extends Core {	
	static $totalpage = 0;
	static $treeResult = '';
	static $level = 0;
	private static $pagination;
	private static $arrayTitle = array(); /* gestione titoli parent sub categorie */

	public function __construct(){
		parent::__construct();
		}
		
	public static function checkIfCatExists($opt) {
		$check = false;
		$table = (isset($opt['table']) && $opt['table'] != '' ? $opt['table'] : '');
      $fieldRif = (isset($opt['fieldRif']) && $opt['fieldRif'] != '' ? $opt['fieldRif'] : 'id_cat');
      $valueRif = (isset($opt['valueRif']) && $opt['valueRif'] != '' ? $opt['valueRif'] : '');      
		Sql::initQuery($table,array('id'),array($valueRif),$fieldRif.' = ?');
		$count = Sql::countRecord();
		if (Sql::$error == 0) {
			if ($count > 0) $check = true;
			}				
		return $check;
		}
		
	public static function checkIfCatExistsInObject($obj,$id_cat) {
		$check = false;
		if (is_array($obj) && count($obj) > 0) {
			foreach ($obj AS $key => $val) {
				if ($val->id == $id_cat) {
					$check = true;
					break;
					}
				}
			}
		return $check;
		}
		
	public static function checkIfCatExistsInObjectOrGetOne($obj,$id_cat) {
		$res = self::checkIfCatExistsInObject($obj,$id_cat);
		if ($res == false) {
			if (is_array($obj) && count($obj) > 0) {
				foreach ($obj AS $key => $val) {
					$id_cat = $val->id;
					break;
					}
				}
			}
		return $id_cat;
		}

	public static function generateCategoriesTreeUlList($obj,$parent,$opt,$activePage=1) {
		$has_children = false;
		$classMainUl = (isset($opt['classMainUl']) && $opt['classMainUl'] != '' ? $opt['classMainUl'] : '');  
      $classSubUl = (isset($opt['classSubUl']) && $opt['classSubUl'] != '' ? $opt['classSubUl'] : ''); 
		$ulclass = '';		
		
		$classMainLi = (isset($opt['classMainLi']) && $opt['classMainLi'] != '' ? $opt['classMainLi'] : '');  
      $classSubLiParent = (isset($opt['classSubLiParent']) && $opt['classSubLiParent'] != '' ? $opt['classSubLiParent'] : ''); 
      $classSubLi = (isset($opt['classSubLi']) && $opt['classSubLi'] != '' ? $opt['classSubLi'] : ''); 
      $classDefLi = (isset($opt['classDefLi']) && $opt['classDefLi'] != '' ? $opt['classDefLi'] : '');     
      $classLi = $classDefLi;  	
		
		$classMainAref = (isset($opt['classMainAref']) && $opt['classMainAref'] != '' ? $opt['classMainAref'] : '');  
		$classSubArefParent = (isset($opt['classSubArefParent']) && $opt['classSubArefParent'] != '' ? $opt['classSubArefParent'] : ''); 
      $classSubAref = (isset($opt['classSubAref']) && $opt['classSubAref'] != '' ? $opt['classSubAref'] : ''); 
		$classAref = $classSubAref;		
		
		$aRefSuffixStringMain = (isset($opt['aRefSuffixStringMain']) && $opt['aRefSuffixStringMain'] != '' ? $opt['aRefSuffixStringMain'] : ''); 
      $aRefSuffixStringSubParent = (isset($opt['aRefSuffixStringSubParent']) && $opt['aRefSuffixStringSubParent'] != '' ? $opt['aRefSuffixStringSubParent'] : '');  
      $aRefSuffixStringSub = (isset($opt['aRefSuffixStringSub']) && $opt['aRefSuffixStringSub'] != '' ? $opt['aRefSuffixStringSub'] : '');
		$aRefSuffixString =  $aRefSuffixStringSub; 
		
		$titlePrefixStringMain = (isset($opt['titlePrefixStringMain']) && $opt['titleSuffixStringMain'] != '' ? $opt['titlePrefixStringMain'] : ''); 
      $titlePrefixStringSubParent = (isset($opt['titlePrefixStringSubParent']) && $opt['titlePrefixStringSubParent'] != '' ? $opt['titlePrefixStringSubParent'] : '');  
      $titlePrefixStringSub = (isset($opt['titlePrefixStringSub']) && $opt['titlePrefixStringSub'] != '' ? $opt['titlePrefixStringSub'] : '');
		$titlePrefixString =  $titlePrefixStringSub;       
		
		$titleSuffixStringMain = (isset($opt['titleSuffixStringMain']) && $opt['titleSuffixStringMain'] != '' ? $opt['titleSuffixStringMain'] : ''); 
      $titleSuffixStringSubParent = (isset($opt['titleSuffixStringSubParent']) && $opt['titleSuffixStringSubParent'] != '' ? $opt['titleSuffixStringSubParent'] : '');  
      $titleSuffixStringSub = (isset($opt['titleSuffixStringSub']) && $opt['titleSuffixStringSub'] != '' ? $opt['titleSuffixStringSub'] : '');
		$titleSuffixString =  $titleSuffixStringSub;  
		
		$showId = (isset($opt['showId']) && $opt['showId'] != '' ? $opt['showId'] : false);
		
		$langSuffix = (isset($opt['langSuffix']) && $opt['langSuffix'] != '' ? $opt['langSuffix'] : 'it');
		$valueUrlDefault = (isset($opt['valueUrlDefault']) && $opt['valueUrlDefault'] != '' ? $opt['valueUrlDefault'] : '');
		$valueUrlEmpty = (isset($opt['valueUrlEmpty']) && $opt['valueUrlEmpty'] != '' ? $opt['valueUrlEmpty'] : '');
			
		if(is_array($obj) && count($obj) > 0) {
			foreach($obj AS $key=>$value) {		
				if (intval($value->parent) == $parent) {				    
					if ($has_children === false) {
		            /* Switch the flag, start the list wrapper, increase the level count */         
		            $has_children = true;	            
		         	/* mostra id */
		           	$strShowHrefId = '';
		           	$strShowLiId = '';
		           	$strShowUlId = '';
		           	if ($showId == true) {
		           		$strShowHrefId = ' id="APage'.$value->id.'ID"';
		           		$strShowLiId = ' id="liPage'.$value->id.'ID"';
		           		$strShowUlId = ' id="UlPage'.$value->id.'ID"';  
		           		}	            
		            if (self::$level == 0) $ulclass = $classMainUl;
						if (self::$level > 0) $ulclass = $classSubUl;
						if (self::$level > $opt['MainUl']) self::$treeResult .= '<ul'.$strShowUlId.' class="'.$ulclass.'">'."\n";  
          		}
          		
          		$fieldTitle = 'title_';
          		$fieldTitleSeo = 'title_seo_';
          		$fieldTitleMeta = 'title_meta_';  
          		
					/* gestione multilingua */  
          		$valueTitle = Multilanguage::getLocaleObjectValue($value,$fieldTitle,$langSuffix,array());
          		$valueTitleSeo = Multilanguage::getLocaleObjectValue($value,$fieldTitleSeo,$langSuffix,array());
          		$valueTitleMeta = Multilanguage::getLocaleObjectValue($value,$fieldTitleMeta,$langSuffix,array());
          		
          		   
          		if(self::$level == 0) $classLi = $classMainLi; 
          		if(self::$level == 0 && $value->sons == 0) $classLi = $classSubLi; 
         		if(self::$level > 0 && $value->sons > 0) $classLi = $classSubLiParent;
         		if(self::$level > 0 && $value->sons == 0) $classLi = $classDefLi;
					if(self::$level == 0) $classAref = $classMainAref; 
          		if(self::$level == 0 && $value->sons == 0) $classAref = $classSubAref; 
         		if(self::$level > 0 && $value->sons > 0) $classAref = $classSubArefParent;			
					if(self::$level == 0) $aRefSuffixString = $aRefSuffixStringMain; 
          		if(self::$level == 0 && $value->sons == 0) $aRefSuffixString = $aRefSuffixStringSub; 
         		if(self::$level > 0 && $value->sons > 0) $aRefSuffixString = $aRefSuffixStringSubParent;     		
					if(self::$level == 0) $titlePrefixString = $titlePrefixStringMain; 
          		if(self::$level == 0 && $value->sons == 0) $titlePrefixString = $titlePrefixStringSub; 
         		if(self::$level > 0 && $value->sons > 0) $titlePrefixString = $titlePrefixStringSubParent;
         		if(self::$level > 0 && $value->sons == 0) $titlePrefixString = $titlePrefixStringSub;
         		if(self::$level == 0) $titleSuffixString = $titleSuffixStringMain; 
          		if(self::$level == 0 && $value->sons == 0) $titleSuffixString = $titleSuffixStringSub; 
         		if(self::$level > 0 && $value->sons > 0) $titleSuffixString = $titleSuffixStringSubParent;
         		if(self::$level > 0 && $value->sons == 0) $titleSuffixString = $titlePrefixStringSub;						
					$pagesModule = (isset($opt['pagesModule']) && $opt['pagesModule'] != '' ? $opt['pagesModule'] :  URL_SITE.'page/');			 		
			 		$hrefValue = $pagesModule;		
			 				 		
          		/* sostituisce l'id e altro */
	      		$hrefValue = preg_replace('/{{ID}}/',$value->id,$hrefValue);
	      		$hrefValue = preg_replace('/{{SEO}}/',$valueTitleSeo,$hrefValue);
	      		$hrefValue = preg_replace('/{{SEOCLEAN}}/', ToolsStrings::url_slug($valueTitleSeo,array()),$hrefValue);
	      		$hrefValue = preg_replace('/{{SEOENCODE}}/', urlencode($valueTitleSeo),$hrefValue);  
	      		$hrefValue = preg_replace('/{{TITLE}}/', urlencode($valueTitleSeo),$hrefValue);     
	      		     		              
					self::$treeResult .= '<li'.$strShowLiId.' class="'.$classLi.'">'."\n";					
					self::$treeResult .= '<a'.$strShowHrefId.' class="'.$classAref.'" href="'.$hrefValue.'"';
					self::$treeResult .= $aRefSuffixString.'>'."\n"; 
					self::$treeResult .= $titlePrefixString.$valueTitle.$titleSuffixString."\n";  					
					self::$treeResult .= '</a>'."\n";
					$id = intval($value->id);
					self::$level++;	
					self::generateCategoriesTreeUlList($obj,$id,$opt); 
					self::$level--;										
					self::$treeResult .= '</li>'."\n";
		 			}	 		
		 		}
		 	}
			if ($has_children === true && self::$level > $opt['MainUl']) self::$treeResult .= '</ul>'."\n";
		}

	public static function getObjFromSubCategories($opt) {
		$optDef = array('type'=>1,'multilanguage'=>1,'ordering'=>1,'active'=>1);	
		$opt = array_merge($optDef,$opt);	
		$tableCat = (isset($opt['tableCat']) && $opt['tableCat'] != '' ? $opt['tableCat'] : '');
		$tableItem = (isset($opt['tableItem']) && $opt['tableItem'] != '' ? $opt['tableItem'] : '');
		$initParent = (isset($opt['initParent']) && $opt['initParent'] != '' ? $opt['initParent'] : 0);	
		$imageField = (isset($opt['imageField']) && $opt['imageField'] != '' ? $opt['imageField'] : false);
		$countItems = (isset($opt['countItems']) && $opt['countItems'] != '' ? $opt['countItems'] : false);
		$qry = "SELECT c.id AS id,
		c.parent AS parent,
		c.title AS title,";
		if ($imageField == true) $qry .= "c.filename AS filename,c.org_filename AS org_filename,";
		if ($opt['ordering'] == 1) $qry .= "c.ordering AS ordering,";
		if ($opt['type'] == 1) $qry .= "c.type AS type,";		
		$qry .= "c.active AS active,";
		if ($countItems == true) $qry .= "(SELECT COUNT(i.id) FROM ".$tableItem." AS i WHERE i.id_cat = c.id) AS items,";
		$qry .= "(SELECT COUNT(id) FROM ".$tableCat." AS s WHERE s.parent = c.id)  AS sons,
		(SELECT p.title FROM ".$tableCat." AS p WHERE c.parent = p.id)  AS titleparent
		FROM ".$tableCat." AS c
		WHERE c.parent = :parent"; 
		if ($opt['active'] == 1) {
			$qry .= " AND active = '1'";
			}
		if ($opt['ordering'] == 1) $qry .= " ORDER BY ordering DESC";	
					
		if (isset($opt['qry']) && $opt['qry'] != '' ) $qry = $opt['qry'];		
		$obj = '';
		Sql::resetListDataVar();
		$obj = Sql::getListParentData($qry,array(),$initParent,$opt);
		return $obj;		
		}
				
	public static function getCategoryDetails($id,$table,$opt) {
		$obj =  new stdClass;
		$findOne = (isset($opt['findOne']) ? $opt['findOne'] : true);
		$actived = (isset($opt['actived']) ? $opt['actived'] : true);							
		/* prende la categoria indicata */
		$clause = 'id = ?';
		if ($actived == true) $clause .= ' AND active = 1';
		Sql::initQuery($table,array('*'),array($id),$clause);
		$obj = Sql::getItemData();		
		if (!isset($obj->id) || (isset($obj->id) && (int)$obj->id == 0)) {
			if($findOne == true) {
				/* prende la prima disponibile */
				Sql::initQuery($table,array('*'),array());
				$obj = Sql::getItemData();
				}			
			}
		return $obj;
		}
		

	public static function getCategoryType($id,$table){	
		Sql::initQuery($table,array('type'),array($id),'id = ?');
		$itemData = Sql::getItemData();	
		return $itemData->type;	
		}
		
	public static function checkIssetCategory($table,$opt){	
		Sql::initQuery($table,array('id'));
		$count = Sql::countRecord();
		if (self::$resultOp->type == 0) {
			return ($count == 0 ? false : true);
			} else {
				return false;
				}	
		}
	
	public static function checkIssetOwner($table,$id,$opt){	
		Sql::initQuery($table,array('id'),array($id),'id = ?');
		$count = Sql::countRecord();
		if(Sql::$error == 0) {
			return ($count == 0 ? false : true);
			} else {
				self::$error = 1;
				echo self::$message = Sql::$message;
				return false;
				}	
		}

	public static function resetTreeResult() {
		self::$treeResult = '';
		}
	
	public static function getTreeResult() {
		return self::$treeResult;
		}

	}
?>
