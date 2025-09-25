<?php
/* 
classes/class.Categories.php v.1.0.0. 27/07/2016
*/
class CategoriesCle extends Core {	
	static $totalpage = 0;
	static $treeResult = '';
	static $level = 0;
	private static $pagination;
	private static $arrayTitle = []; /* gestione titoli parent sub categorie */

	public function __construct(){
		parent::__construct();
		}
		
	public static function checkIfCatExists($opz) {
		$check = false;
		$table = (isset($opz['table']) && $opz['table'] != '' ? $opz['table'] : '');
      $fieldRif = (isset($opz['fieldRif']) && $opz['fieldRif'] != '' ? $opz['fieldRif'] : 'id_cat');
      $valueRif = (isset($opz['valueRif']) && $opz['valueRif'] != '' ? $opz['valueRif'] : '');      
		Sql::initQuery($table,['id'],[$valueRif],$fieldRif.' = ?');
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

	public static function generateCategoriesTreeUlList($obj,$parent,$opz,$activePage=1) {
		$has_children = false;
		$classMainUl = (isset($opz['classMainUl']) && $opz['classMainUl'] != '' ? $opz['classMainUl'] : '');  
      $classSubUl = (isset($opz['classSubUl']) && $opz['classSubUl'] != '' ? $opz['classSubUl'] : ''); 
		$ulclass = '';		
		
		$classMainLi = (isset($opz['classMainLi']) && $opz['classMainLi'] != '' ? $opz['classMainLi'] : '');  
      $classSubLiParent = (isset($opz['classSubLiParent']) && $opz['classSubLiParent'] != '' ? $opz['classSubLiParent'] : ''); 
      $classSubLi = (isset($opz['classSubLi']) && $opz['classSubLi'] != '' ? $opz['classSubLi'] : ''); 
      $classDefLi = (isset($opz['classDefLi']) && $opz['classDefLi'] != '' ? $opz['classDefLi'] : '');     
      $classLi = $classDefLi;  	
		
		$classMainAref = (isset($opz['classMainAref']) && $opz['classMainAref'] != '' ? $opz['classMainAref'] : '');  
		$classSubArefParent = (isset($opz['classSubArefParent']) && $opz['classSubArefParent'] != '' ? $opz['classSubArefParent'] : ''); 
      $classSubAref = (isset($opz['classSubAref']) && $opz['classSubAref'] != '' ? $opz['classSubAref'] : ''); 
		$classAref = $classSubAref;		
		
		$aRefSuffixStringMain = (isset($opz['aRefSuffixStringMain']) && $opz['aRefSuffixStringMain'] != '' ? $opz['aRefSuffixStringMain'] : ''); 
      $aRefSuffixStringSubParent = (isset($opz['aRefSuffixStringSubParent']) && $opz['aRefSuffixStringSubParent'] != '' ? $opz['aRefSuffixStringSubParent'] : '');  
      $aRefSuffixStringSub = (isset($opz['aRefSuffixStringSub']) && $opz['aRefSuffixStringSub'] != '' ? $opz['aRefSuffixStringSub'] : '');
		$aRefSuffixString =  $aRefSuffixStringSub; 
		
		$titlePrefixStringMain = (isset($opz['titlePrefixStringMain']) && $opz['titleSuffixStringMain'] != '' ? $opz['titlePrefixStringMain'] : ''); 
      $titlePrefixStringSubParent = (isset($opz['titlePrefixStringSubParent']) && $opz['titlePrefixStringSubParent'] != '' ? $opz['titlePrefixStringSubParent'] : '');  
      $titlePrefixStringSub = (isset($opz['titlePrefixStringSub']) && $opz['titlePrefixStringSub'] != '' ? $opz['titlePrefixStringSub'] : '');
		$titlePrefixString =  $titlePrefixStringSub;       
		
		$titleSuffixStringMain = (isset($opz['titleSuffixStringMain']) && $opz['titleSuffixStringMain'] != '' ? $opz['titleSuffixStringMain'] : ''); 
      $titleSuffixStringSubParent = (isset($opz['titleSuffixStringSubParent']) && $opz['titleSuffixStringSubParent'] != '' ? $opz['titleSuffixStringSubParent'] : '');  
      $titleSuffixStringSub = (isset($opz['titleSuffixStringSub']) && $opz['titleSuffixStringSub'] != '' ? $opz['titleSuffixStringSub'] : '');
		$titleSuffixString =  $titleSuffixStringSub;  
		
		$showId = (isset($opz['showId']) && $opz['showId'] != '' ? $opz['showId'] : false);
		
		$langSuffix = (isset($opz['langSuffix']) && $opz['langSuffix'] != '' ? $opz['langSuffix'] : 'it');
		$valueUrlDefault = (isset($opz['valueUrlDefault']) && $opz['valueUrlDefault'] != '' ? $opz['valueUrlDefault'] : '');
		$valueUrlEmpty = (isset($opz['valueUrlEmpty']) && $opz['valueUrlEmpty'] != '' ? $opz['valueUrlEmpty'] : '');
			
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
						if (self::$level > $opz['MainUl']) self::$treeResult .= '<ul'.$strShowUlId.' class="'.$ulclass.'">'."\n";  
          		}
          		
          		$fieldTitle = 'title_';
          		$fieldTitleSeo = 'title_seo_';
          		$fieldTitleMeta = 'title_meta_';  
          		
					/* gestione multilingua */  
          		$valueTitle = Multilanguage::getLocaleObjectValue($value,$fieldTitle,$langSuffix,[]);
          		$valueTitleSeo = Multilanguage::getLocaleObjectValue($value,$fieldTitleSeo,$langSuffix,[]);
          		$valueTitleMeta = Multilanguage::getLocaleObjectValue($value,$fieldTitleMeta,$langSuffix,[]);
          		
          		   
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
					$pagesModule = (isset($opz['pagesModule']) && $opz['pagesModule'] != '' ? $opz['pagesModule'] :  URL_SITE.'page/');			 		
			 		$hrefValue = $pagesModule;		
			 				 		
          		/* sostituisce l'id e altro */
	      		$hrefValue = preg_replace('/{{ID}}/',(string) $value->id,(string) $hrefValue);
	      		$hrefValue = preg_replace('/{{SEO}}/',(string) $valueTitleSeo,(string) $hrefValue);
	      		$hrefValue = preg_replace('/{{SEOCLEAN}}/', ToolsStrings::url_slug($valueTitleSeo,[]),(string) $hrefValue);
	      		$hrefValue = preg_replace('/{{SEOENCODE}}/', urlencode((string) $valueTitleSeo),(string) $hrefValue);  
	      		$hrefValue = preg_replace('/{{TITLE}}/', urlencode((string) $valueTitleSeo),(string) $hrefValue);     
	      		     		              
					self::$treeResult .= '<li'.$strShowLiId.' class="'.$classLi.'">'."\n";					
					self::$treeResult .= '<a'.$strShowHrefId.' class="'.$classAref.'" href="'.$hrefValue.'"';
					self::$treeResult .= $aRefSuffixString.'>'."\n"; 
					self::$treeResult .= $titlePrefixString.$valueTitle.$titleSuffixString."\n";  					
					self::$treeResult .= '</a>'."\n";
					$id = intval($value->id);
					self::$level++;	
					self::generateCategoriesTreeUlList($obj,$id,$opz); 
					self::$level--;										
					self::$treeResult .= '</li>'."\n";
		 			}	 		
		 		}
		 	}
			if ($has_children === true && self::$level > $opz['MainUl']) self::$treeResult .= '</ul>'."\n";
		}

	public static function getObjFromSubCategories($opz) {		
		$tableCat = (isset($opz['tableCat']) && $opz['tableCat'] != '' ? $opz['tableCat'] : '');
		$tableItem = (isset($opz['tableItem']) && $opz['tableItem'] != '' ? $opz['tableItem'] : '');
		$initParent = (isset($opz['initParent']) && $opz['initParent'] != '' ? $opz['initParent'] : 0);	
		$imageField = (isset($opz['imageField']) && $opz['imageField'] != '' ? $opz['imageField'] : false);
		$countItems = (isset($opz['countItems']) && $opz['countItems'] != '' ? $opz['countItems'] : false);
		$ordering = (isset($opz['ordering']) && $opz['ordering'] != '' ? $opz['ordering'] : 'ordering ASC');
		
		$qry = "SELECT c.id AS id,
		c.parent AS parent,
		c.title_it AS title_it,";
		if ($imageField == true) $qry .= "c.filename AS filename,c.org_filename AS org_filename,";
		$qry .= "c.ordering AS ordering,
		c.type AS type,
		c.active AS active,";
		if ($countItems== true) $qry .= "(SELECT COUNT(i.id) FROM ".$tableItem." AS i WHERE i.id_cat = c.id) AS items,";
		$qry .= "(SELECT COUNT(id) FROM ".$tableCat." AS s WHERE s.parent = c.id)  AS sons,
		(SELECT p.title_it FROM ".$tableCat." AS p WHERE c.parent = p.id)  AS titleparent_it
		FROM ".$tableCat." AS c
		WHERE c.parent = :parent 
		ORDER BY ".$ordering;	
					
		if (isset($opz['qry']) && $opz['qry'] != '' ) $qry = $opz['qry'];		
		$obj = '';

		//echo $qry;

		Sql::resetListTreeData();
		Sql::resetListDataVar();
		Sql::setListTreeData($qry,$initParent,$opz);
		$obj = Sql::getListTreeData();
		return $obj;		
		}
				
	public static function getCategoryDetails($id,$table,$opz) {
		$obj =  new stdClass;
		$findOne = ($opz['findOne'] ?? true);
		$actived = ($opz['actived'] ?? true);							
		/* prende la categoria indicata */
		$clause = 'id = ?';
		if ($actived == true) $clause .= ' AND active = 1';
		Sql::initQuery($table,['*'],[$id],$clause);
		$obj = Sql::getItemData();		
		if (!isset($obj->id) || (isset($obj->id) && (int)$obj->id == 0)) {
			if($findOne == true) {
				/* prende la prima disponibile */
				Sql::initQuery($table,['*'],[]);
				$obj = Sql::getItemData();
				}			
			}
		return $obj;
		}
		

	public static function getCategoryType($id,$table){	
		Sql::initQuery($table,['type'],[$id],'id = ?');
		$itemData = Sql::getItemData();	
		return $itemData->type;	
		}
		
	public static function checkIssetCategory($table,$opz){	
		Sql::initQuery($table,['id']);
		$count = Sql::countRecord();
		if (self::$resultOp->type == 0) {
			return ($count == 0 ? false : true);
			} else {
				return false;
				}	
		}
	
	public static function checkIssetOwner($table,$id,$opz){	
		Sql::initQuery($table,['id'],[$id],'id = ?');
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
