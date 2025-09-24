<?php
/* wscms/class.Pages.php v.3.5.4. 03/07/2019 */

class Pages extends Core {

	public static $output = '';
	public static $level = 0;
	public static $colum = [];
	public static $subItems = 0;
	public static $treeData = '';
	
	public function __construct() 	
	{
		parent::__construct();
	}
	
	public static function createMenuFromSubProducts($obj,$opt) 
	{	
		$optDef = []; 
		$opt = array_merge($optDef,$opt);
		self::resetOutput();
		
		if (is_array($obj) && count($obj) > 0) {
			foreach( $obj AS $key=>$value ) {		
				self::$output .= '<li>'.PHP_EOL; 	
				
				//self::$output .= $obj->title.PHP_EOL; 	
				
				self::$output .= '</li>'.PHP_EOL; 
			}	 		
		}
		return self::$output;
	}	

	public static function createMenuDivFromSubPages($obj,$parent,$opt) 
	{	
		$optDef = [
			'ulIsMain'=>1, 
			'pagesModule' => 'pages/',
			'valueUrlDefault' => '%ID%/%SEOCLEAN%'
		];
		$opt = array_merge($optDef,$opt);

		$has_children = false;
		if (is_array($obj) && count($obj) > 0) {
			foreach($obj AS $key=>$value) {				
				if (intval($value->parent) == $parent) { 	
				
					
						$id = intval($value->id);
						$ul = $opt['ulDefault'];
						$ulClose = $opt['ulDefaultClose'];

						$li = $opt['liDefault'];
						$liClose = $opt['liDefaultClose'];

						$href = $opt['hrefDefault'];
						$hrefClose = $opt['hrefDefaultClose'];

						if ($has_children === false) {
							$has_children = true;											
							if (self::$level == 0) {
								$ul = $opt['ulMain'];
								$ulClose = $opt['ulMainClose'];
							}

							if (self::$level > 0) {
								$ul = $opt['ulSubMenu'];	
								$ulClose = $opt['ulSubMenuClose'];	
							}
					
							if (self::$level > $opt['ulIsMain']) self::$output .= $ul.PHP_EOL;  
						}			
						
						if (self::$level == 0) {
							$li = $opt['liMain']; 
							$liClose = $opt['liMainClose']; 
						}
						
						if (self::$level == 0 && $value->sons == 0) {
							$ul = $opt['liDefault'];
							$ulClose = $opt['liDefaultClose'];
						}

						if (self::$level == 0 && $value->sons > 0) {
							$li = $opt['liSubMenu'];
							$liclose = $opt['liSubMenuClose'];
							$href = $opt['hrefSubMenu'];
							$hrefClose = $opt['hrefSubMenuClose'];
						}

						if (self::$level > 0 && $value->sons == 0)  {
							$li = $opt['liDefault'];					
							$liClose = $opt['liDefaultClose'];
							$href = $opt['hrefMain'];
							$hrefClose = $opt['hrefMainClose'];
						
						}

						if (self::$level > 0 && $value->sons > 0) {
							$li = $opt['liSubSubMenu'];					
							$liClose = $opt['liSubSubMenuClose'];
							$href = $opt['hrefSubMenu'];
							$hrefClose = $opt['hrefSubMenuClose'];
						}

						if (self::$level > 0 && $value->sons > 1) {
							$li = $opt['liSubSubMenu'];					
							$liClose = $opt['ulSubSubMenuClose'];
							$href = $opt['hrefSubMenu'];
							$hrefClose = $opt['hrefSubMenuClose'];
						}
						
						$href = $href.$hrefClose;

						/* crea titles */
						$titlesVal = self::getTitlesVal($value,$opt);         

						// crea url 
						$url = $opt['urlDefault'];
						$url = URL_SITE.$opt['pagesModule'].$opt['valueUrlDefault']; 
						

						if ($value->url != '') {
							$url = $value->url;
							$url = ToolsStrings::parseHtmlContent($url,[]);
						}


						$url = preg_replace('/%SEO%/',(string) $titlesVal['titleSeo'],(string) $url);
						$url = preg_replace('/%SEOCLEAN%/', (string) SanitizeStrings::urlslug($titlesVal['titleSeo'],['delimiter'=>'-']),$url);
						$url = preg_replace('/%SEOENCODE%/', urlencode((string) $titlesVal['titleSeo']),$url);

						$title = $titlesVal['title'];

						//$title .= 'id:%ID%  Level:%LEVEL% Sons:%SONS% ';
						//$url = $title;
						
						
						self::$output .= $li.PHP_EOL;
						self::$output .= $href.PHP_EOL;

						if ($value->alias == $opt['activepage']) {
							self::$output = preg_replace('/%CLASSACTIVE%/',(string) $opt['classactive'],self::$output);
							self::$output = preg_replace('/%TEXTACTIVE%/',(string) $opt['textactive'],(string) self::$output);
						} else {
							self::$output = preg_replace('/%CLASSACTIVE%/','',self::$output);
							self::$output = preg_replace('/%TEXTACTIVE%/','',(string) self::$output);
						}

						self::$output = preg_replace('/%URL%/',$url,(string) self::$output);
						self::$output = preg_replace('/%URLTITLE%/',(string) $title,(string) self::$output);
						self::$output = preg_replace('/%TITLE%/',(string) $title,(string) self::$output);
						
						
						self::$output = preg_replace('/%LEVEL%/',(string) self::$level,(string) self::$output);
						self::$output = preg_replace('/%SONS%/',(string) $value->sons,(string) self::$output);
						self::$output = preg_replace('/%ID%/',(string) $value->id,(string) self::$output);
						self::$output = preg_replace('/%PARENT%/',(string) $value->parent,(string) self::$output);
						if (isset($value->alias)) {
							self::$output = preg_replace('/%ALIAS%/',$value->alias,(string) self::$output);
						}

						self::$level++;	
							self::createMenuDivFromSubPages($obj,$id,$opt); 
						self::$level--;	
															
						self::$output .= $liClose.PHP_EOL;					
			
				}	 		
			}
		}
		if ($has_children === true && self::$level > $opt['ulIsMain']) self::$output .= $ulClose.PHP_EOL;
		return self::$output;
	}	
	
	public static function createMenuFromSubPages($obj,$parent,$opt) {	
		$optDef = [
			'ulIsMain'=>0, 'ulMain'=>'<ul>', 'ulSubMenu'=>'<ul>',	 'ulDefault'=>'<ul>',	 'liMain'=>'<li>', 'liSubMenu'=>'<li>', 'liSubSubMenu'=>'<li>', 'liDefault'=>'<li>', 'hrefMain'=>'<a>', 'hrefSubMenu'=>'<a>', 'hrefdefault'=>'<a>', 'lang'=>'it', 'urldefault'=>'#!', 'pagesModule'=>'pages/', 'valueUrlDefault'=>'%ID%/%SEOCLEAN%', 'titleField'=>'', 'activepage'=>'pages'
			]; 
		$opt = array_merge($optDef,$opt);

		$has_children = false;

		if (is_array($obj) && count($obj) > 0) {
			foreach($obj AS $key=>$value) {				
				if (intval($value->parent) == $parent) { 	
					$ul = $opt['ulDefault'];
					$li = $opt['liDefault'];
					$href = $opt['hrefdefault'];
					
					/* crea voce mnu */
           		if ($value->menu > 0) {	
						    
						if ($has_children === false) {
							$has_children = true;											
							if (self::$level == 0) $ul = $opt['ulMain'];
							if (self::$level > 0) $ul = $opt['ulSubMenu'];	
							$ul = preg_replace('/%ACTIVEPAGE%%/',(string) $opt['activepage'],(string) $ul);						
	            		if (self::$level > $opt['ulIsMain']) self::$output .= $ul.PHP_EOL;  
	           		}								
						/* gestione tag dinamici */
						if (self::$level == 0) $li = $opt['liMain']; 
						if (self::$level == 0 && $value->sons > 0) $li = $opt['liSubMenu'];
						if (self::$level > 0 && $value->sons == 0)  $ul = $opt['liDefault'];
						if (self::$level > 0 && $value->sons > 0) $li = $opt['liSubSubMenu'];				
						
						if (self::$level == 0 && $value->sons == 0) $href = $opt['hrefMain'];
						if (self::$level == 0 && $value->sons > 0) $href = $opt['hrefSubMenu'];
						if (self::$level > 0 && $value->sons > 0) $href = $opt['hrefSubMenu'];
	
						
						/* active page */
						
						//echo 'alias: '.$value->alias;
	         		if (self::$level == 0 && $value->alias == $opt['activepage']) {
	         			$li = preg_replace('/%CLASSACTIVE%/',' active',(string) $li);
	         			} else {
	         				$li = preg_replace('/%CLASSACTIVE%/','',(string) $li);
	         				}	
	
		       		/* crea titles */
		       		$titlesVal = self::getTitlesVal($value,$opt);          		
		       		/* crea url */
		       		$hrefUrl = $opt['urldefault'];
		       		if (isset($value->type)) {
		       			$hrefUrl = self::getUrlFromPageType($value,$titlesVal,$opt);
		       			}           
	 					$li = preg_replace('/%URL%/',(string) $hrefUrl,$li);
	 					$li = preg_replace('/%URLTITLE%/',(string) $titlesVal['titleSeo'],$li);
	 					$li = preg_replace('/%TITLE%/',(string) $titlesVal['title'],$li);
	 					$href = preg_replace('/%URL%/',(string) $hrefUrl,(string) $href);
	 					$href = preg_replace('/%URLTITLE%/',(string) $titlesVal['titleSeo'],$href);
	 					$href = preg_replace('/%TITLE%/',(string) $titlesVal['title'],$href);
	 					
	 					if (self::$level == 0 && $value->alias == $opt['activepage']) {
	         			$href = preg_replace('/%CLASSACTIVE%/',' active',$href);
	         			}
	 					
	 					
	 					self::$output .= $li.PHP_EOL;
	 					self::$output .= $href.PHP_EOL;
	 					
	 					
	 					
	 					self::$output = preg_replace('/%LEVEL%/',(string) self::$level,self::$output);
	 					self::$output = preg_replace('/%SONS%/',(string) $value->sons,(string) self::$output);
	 					    					
	 					$id = intval($value->id);
	 					self::$level++;	
							self::createMenuFromSubPages($obj,$id,$opt); 
						self::$level--;										
						self::$output .= '</li>'.PHP_EOL;
						}
	 				}	 		
		 		}
		 	}
			if ($has_children === true && self::$level > $opt['ulIsMain']) self::$output .= '</ul>'.PHP_EOL;
		return self::$output;
		}	
				
	
	
		public static function getTitlesVal($value,$opt) 
	{
		$titlesVal = [];
		$fieldTitle = 'title_';
 		$fieldTitleSeo = 'title_seo_';
 		$fieldTitleMeta = 'title_meta_';         		        		
 		$titlesVal['title'] = Multilanguage::getLocaleObjectValue($value,$fieldTitle,Config::$langVars['user'],[]);
 		$titlesVal['titleSeo'] = Multilanguage::getLocaleObjectValue($value,$fieldTitleSeo,Config::$langVars['user'],[]);
 		$titlesVal['titleMeta'] = Multilanguage::getLocaleObjectValue($value,$fieldTitleMeta,Config::$langVars['user'],[]); 
		return $titlesVal;	
	}
	
	public static function getUrlFromPageType($value,$titlesVal,$opt) {
		$url = match ($value->type) {
            'label' => $opt['urldefault'],
            'module-link' => URL_SITE.$value->url,
            'url' => $value->url,
            default => URL_SITE.$opt['pagesModule'].$opt['valueUrlDefault'],
        };
	  		
	  	$parentstring = '';
	  	/* trova il parent alias */
	  	$parentalias = '';
	  	if ($value->parent > 0) {
	  		if (isset($value->breadcrumbs[0]['alias'])) $parentalias = $value->breadcrumbs[0]['alias'].'/';
	  		}
		
		$url = preg_replace('/%URLSITE%/',URL_SITE,(string) $url);	
		$url = preg_replace('/%ID%/',(string) $value->id,$url);
		$url = preg_replace('/%ALIAS%/',(string) $value->alias,$url);
		$url = preg_replace('/%SEO%/',(string) $titlesVal['titleSeo'],$url);
		$url = preg_replace('/%SEOCLEAN%/', (string) SanitizeStrings::urlslug($titlesVal['titleSeo'],['delimiter'=>'-']),$url);
		$url = preg_replace('/%SEOENCODE%/', urlencode((string) $titlesVal['titleSeo']),$url);
		$url = preg_replace('/%TITLE%/', urlencode((string) $titlesVal['titleSeo']),$url); 
		$url = preg_replace('/%PARENTSTRING%/', urlencode($parentstring),$url);
		$url = preg_replace('/%PARENTALIAS%/', $parentalias,$url);
				
		return $url;
	}
		
	/* SQL QUERIES */

	// funzione usate per compatibilita CLE
	// da camcellare in altri progetti 
	public static function setMainTreePagesDataCle($opt) {
		$optDef = ['fieldKey'=>'alias','table'=>'pages','table template'=>'pagetemplates','ordering'=>'ASC']; 
		$opt = array_merge($optDef,$opt);

		$table = DB_TABLE_PREFIX.$opt['table'];
		$tableTemplate = DB_TABLE_PREFIX.$opt['table template'];
		
		$qry = "SELECT c.id AS id,c.parent AS parent,";	
		foreach(Config::$globalSettings['languages'] AS $lang) {		
			$qry .= "c.title_meta_".$lang." AS meta_title_".$lang.",
			c.title_seo_".$lang." AS title_seo_".$lang.",
			c.title_".$lang." AS title_".$lang.",";
			}
		$qry .= "
		c.id_template AS id_template,
		c.ordering AS ordering,
		c.menu AS menu,
		c.alias AS alias,
		c.url AS url,
		c.target AS target,
		c.active AS active,
		(SELECT tp.title_it FROM ".$tableTemplate." AS tp WHERE c.id_template = tp.id)  AS template_name,";
		
		$qry .= "(SELECT p.alias FROM ".$table." AS p WHERE c.parent = p.id) AS aliasparent,";
		foreach(Config::$globalSettings['languages'] AS $lang) {		
			$qry .= "(SELECT p.title_".$lang." FROM ".$table." AS p WHERE c.parent = p.id) AS titleparent_".$lang.",";
			}
		$qry .= "(SELECT COUNT(id) FROM ".$table." AS s WHERE s.parent = c.id) AS sons FROM ".$table." AS c WHERE c.active = 1 AND c.parent = :parent ORDER BY ordering ".$opt['ordering'];		
		
		//echo $qry;die();
		
		Sql::resetListTreeData();
		Sql::resetListDataVar();
		Sql::setListTreeData($qry,0,$opt);
		$obj = Sql::getListTreeData();
		if (Core::$resultOp->error == 1) die('Errore database lettura  albero pagine dinamiche');
		return $obj;
	}

	public static function setMainTreePagesData($opt) {
		$optDef = ['hideMenu'=>0,'fieldKey'=>'alias','table'=>'pages','table template'=>'pagetemplates','ordering'=>'ASC','hideMenu'=>0]; 
		$opt = array_merge($optDef,$opt);

		$table = DB_TABLE_PREFIX.$opt['table'];
		$tableTemplate = DB_TABLE_PREFIX.$opt['table template'];
		
		$qry = "SELECT c.id AS id,c.parent AS parent,";	
		foreach(Config::$globalSettings['languages'] AS $lang) {		
			$qry .= "c.meta_title_".$lang." AS meta_title_".$lang.",c.title_seo_".$lang." AS title_seo_".$lang.",c.title_".$lang." AS title_".$lang.",";
			}
		$qry .= "c.id_template AS id_template,c.ordering AS ordering,c.menu AS menu,c.alias AS alias,c.url AS url,c.target AS target,c.active AS active,(SELECT tp.title FROM ".$tableTemplate." AS tp WHERE c.id_template = tp.id)  AS template_name,";
		
		$qry .= "(SELECT p.alias FROM ".$table." AS p WHERE c.parent = p.id) AS aliasparent,";
		foreach(Config::$globalSettings['languages'] AS $lang) {		
			$qry .= "(SELECT p.title_".$lang." FROM ".$table." AS p WHERE c.parent = p.id) AS titleparent_".$lang.",";
			}
		$qry .= "(SELECT COUNT(id) FROM ".$table." AS s WHERE s.parent = c.id) AS sons FROM ".$table." AS c WHERE c.active = 1 AND c.parent = :parent";
		
		if ($opt['hideMenu'] == 1) $qry .= " AND menu = 1";
		$qry .= " ORDER BY ordering ".$opt['ordering'];		
		
		//echo $qry;die();
		
		Sql::resetListTreeData();
		Sql::resetListDataVar();
		Sql::setListTreeData($qry,0,$opt);
		$obj = Sql::getListTreeData();
		if (Core::$resultOp->error == 1) die('Errore database lettura  albero pagine dinamiche');
		return $obj;
	}

	public static function setMainTreePagesDataOld($opt) {
		$optDef = ['fieldKey'=>'alias','table'=>'pages','table template'=>'pagetemplates','ordering'=>'ASC']; 
		$opt = array_merge($optDef,$opt);
		$languages = self::$globalSettings['languages'];

		$table = DB_TABLE_PREFIX.$opt['table'];
		$tableTemplate = DB_TABLE_PREFIX.$opt['table template'];
		
		$qry = "SELECT c.id AS id,c.parent AS parent,";	
		foreach($languages AS $lang) {		
			$qry .= "c.meta_title_".$lang." AS meta_title_".$lang.",c.title_seo_".$lang." AS title_seo_".$lang.",c.title_".$lang." AS title_".$lang.",";
			}
		$qry .= "c.id_template AS id_template,c.ordering AS ordering,c.type AS type,c.menu AS menu,c.alias AS alias,c.url AS url,c.target AS target,c.active AS active,(SELECT tp.title FROM ".$tableTemplate." AS tp WHERE c.id_template = tp.id)  AS template_name,";
		
		$qry .= "(SELECT p.alias FROM ".$table." AS p WHERE c.parent = p.id) AS aliasparent,";
		$qry .= "(SELECT p.type FROM ".$table." AS p WHERE c.parent = p.id) AS typeparent,";
		foreach($languages AS $lang) {		
			$qry .= "(SELECT p.title_".$lang." FROM ".$table." AS p WHERE c.parent = p.id) AS titleparent_".$lang.",";
			}
		$qry .= "(SELECT COUNT(id) FROM ".$table." AS s WHERE s.parent = c.id) AS sons FROM ".$table." AS c WHERE c.active = 1 AND c.parent = :parent ORDER BY ordering ".$opt['ordering'];		
		Sql::resetListTreeData();
		Sql::resetListDataVar();
		Sql::setListTreeData($qry,0,$opt);
		$obj = Sql::getListTreeData();
		if (Core::$resultOp->error == 1) die('Errore database pagine dinamiche nel menu');
		return $obj;
	}	
	
		
	public static function setMainTreeProductsData($opt) {
		$optDef = ['tablecat'=>'ec_subcategories','tablepro'=>'ec_products','langUser'=>'it','getbreadcrumbs'=>1];
		$opt = array_merge($optDef,$opt);
		$languages = self::$globalSettings['languages'];

		$tableScat = DB_TABLE_PREFIX.$opt['tablecat'];
		$tableProd = DB_TABLE_PREFIX.$opt['tablepro'];
		
		/* prendo le sottocategorie in elenco con la loro struttuta */
		$subcategories = Subcategories::getSubCategoriesStructure(['table'=>'ec_subcategories']);
		//print_r($subcategories);
		
		$products = [];
		
		if ( isset($subcategories) && is_array($subcategories) && count($subcategories) > 0 ) {
			foreach ( $subcategories AS $category ) {
			
				/* prendo i prodotti dalle categorie */
				$qry = "SELECT id";	
				$fields = [];
				$fields[] = 'id';
				foreach ($languages AS $lang) $fields[] = "title_".$lang." AS title_".$lang;
				$fields[] = "title_".$opt['langUser']." AS title";
				$fieldsValues = [$category->id];
				Sql::initQuery($tableProd,$fields,$fieldsValues,'id_cat = ? AND active = 1','ordering ASC');
				$products[$category->id] = Sql::getRecords();		
			}		
		}

		return $products;
	}
	
	public static function resetOutput() {
		self::$output = '';	
	}
}
?>