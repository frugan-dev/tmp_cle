<?php
/* wscms/class.Menu.php v.4.0.0. 07/12/2020 */

class Menu extends Core {

	public static $output = '';
	public static $level = 0;
	public static $colum = array();
	public static $subItems = 0;
	public static $treeData = '';
	
	public function __construct() 	{
		parent::__construct();
	}

	public static function createMenuOutputFromTemplate($obj,$parent,$opt) 
	{	
		$optDef = array(
			'ulIsMain'=>1, 
		);
		$opt = array_merge($optDef,$opt);

		$has_children = false;
		if (is_array($obj) && count($obj) > 0) {
			foreach($obj AS $key=>$value) {				
				if (intval($value->parent) == $parent) { 	
				
					// per menu module
					if ($value->type == 'module-menu' && $value->type != '') {
						$alias = $value->alias;

						if (is_array($opt['modulesmenu']) && count($opt['modulesmenu']) > 0) {
							foreach($opt['modulesmenu'] AS $mmkey=>$mmvalue) {
								//echo '<br>module: #'.$mmkey.'#';
								//echo '<br>replaces: #'.$mmvalue['replace'].'#';
								$alias = preg_replace($mmvalue['replace'],$mmvalue['values'],$alias);
							}
						}
						$alias = preg_replace('/%SUBMODULEMENULABEL%/',$value->title,$alias);
						self::$output .= $alias;
	
					} else {

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

						// crea url 
						$url = $opt['urlDefault'];
						//ToolsStrings::dump($value);
						
						if (isset($value->type)) {
							$url = self::getUrlFromType($value,$value->title,$opt);
						}
						
						
						self::$output .= $li.PHP_EOL;
						self::$output .= $href.PHP_EOL;

						if ($value->alias == $opt['activepage']) {
							self::$output = preg_replace('/%CLASSACTIVE%/',$opt['classactive'],self::$output);
							self::$output = preg_replace('/%TEXTACTIVE%/',$opt['textactive'],self::$output);
						} else {
							self::$output = preg_replace('/%CLASSACTIVE%/','',self::$output);
							self::$output = preg_replace('/%TEXTACTIVE%/','',self::$output);
						}

						self::$output = preg_replace('/%URL%/',$url,self::$output);
						self::$output = preg_replace('/%URLTITLE%/',$value->title,self::$output);
						self::$output = preg_replace('/%TITLE%/',$value->title,self::$output);
						
						
						self::$output = preg_replace('/%LEVEL%/',self::$level,self::$output);
						self::$output = preg_replace('/%SONS%/',$value->sons,self::$output);
						self::$output = preg_replace('/%ID%/',$value->id,self::$output);
						self::$output = preg_replace('/%PARENT%/',$value->parent,self::$output);
						if (isset($value->alias)) {
							self::$output = preg_replace('/%ALIAS%/',$value->alias,self::$output);
						}


					

						self::$level++;	
							self::createMenuOutputFromTemplate($obj,$id,$opt); 
						self::$level--;	
															
						self::$output .= $liClose.PHP_EOL;	
					
					}
			
				}	 		
			}
		}
		if ($has_children === true && self::$level > $opt['ulIsMain']) {
			self::$output .= $ulClose.PHP_EOL;
		}
		return self::$output;
	}

	public static function createMenuOutput($obj,$parent,$opt) 
	{	
		$optDef = array(
			'modulesmenu'=>array(),'ulIsMain'=>0, 'ulMain'=>'<ul>', 'ulSubMenu'=>'<ul>',	 'ulDefault'=>'<ul>',	 'liMain'=>'<li>', 'liSubMenu'=>'<li>', 'liSubSubMenu'=>'<li>', 'liDefault'=>'<li>', 'hrefMain'=>'<a>', 'hrefSubMenu'=>'<a>', 'hrefdefault'=>'<a>', 'lang'=>'it', 'urldefault'=>'#!', 'pagesModule'=>'pages/', 'valueUrlDefault'=>'%ID%/%SEOCLEAN%', 'titleField'=>'', 'activepage'=>'pages'
			); 
		$opt = array_merge($optDef,$opt);

		$has_children = false;
		if (is_array($obj) && count($obj) > 0) {
			foreach($obj AS $key=>$value) {				
				if (intval($value->parent) == $parent) { 	
					// per menu module
					if ($value->type == 'module-menu' && $value->type != '') {
						$alias = $value->alias;

						if (is_array($opt['modulesmenu']) && count($opt['modulesmenu']) > 0) {
							foreach($opt['modulesmenu'] AS $mmkey=>$mmvalue) {
								//echo '<br>module: #'.$mmkey.'#';
								//echo '<br>replaces: #'.$mmvalue['replace'].'#';
								$alias = preg_replace($mmvalue['replace'],$mmvalue['values'],$alias);
							}
						}
						$alias = preg_replace('/%SUBMODULEMENULABEL%/',$value->title,$alias);
						self::$output .= $alias;
						
					} else {
					
						$ul = $opt['ulDefault'];
						$li = $opt['liDefault'];
						$href = $opt['hrefdefault'];						    
						if ($has_children === false) {
							$has_children = true;											
							if (self::$level == 0) $ul = $opt['ulMain'];
							if (self::$level > 0) $ul = $opt['ulSubMenu'];	
							$ul = preg_replace('/%ACTIVEPAGE%%/',$opt['activepage'],$ul);						
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
						
						//echo 'alias: '.$value->alias;
						if (self::$level == 0 && $value->alias == $opt['activepage']) {
							$li = preg_replace('/%CLASSACTIVE%/',' active',$li);
						} else {
							$li = preg_replace('/%CLASSACTIVE%/','',$li);
						}
						
						$li = preg_replace('/%CLASSACTIVE%/','',$li);	
					
						/* crea url */
						$hrefUrl = $opt['urldefault'];
						if (isset($value->type)) {
							$hrefUrl = self::getUrlFromType($value,$value->title,$opt);
						}           
						$li = preg_replace('/%URL%/',$hrefUrl,$li);
						$li = preg_replace('/%URLTITLE%/',$value->title,$li);
						$li = preg_replace('/%TITLE%/',$value->title,$li);
						$href = preg_replace('/%URL%/',$hrefUrl,$href);
						$href = preg_replace('/%URLTITLE%/',$value->title,$href);
						$href = preg_replace('/%TITLE%/',$value->title,$href);
							
						if (self::$level == 0 && $value->alias == $opt['activepage']) {
							$href = preg_replace('/%CLASSACTIVE%/',' active',$href);
						} else {
							$href = preg_replace('/%CLASSACTIVE%/','',$href);
						}
						
						$target = $value->target;
						if ($target == '') $target = '_self';
						$href = preg_replace('/%TARGET%/',$target,$href);
						
						self::$output .= $li.PHP_EOL;
						self::$output .= $href.PHP_EOL;
						self::$output = preg_replace('/%LEVEL%/',self::$level,self::$output);
						self::$output = preg_replace('/%SONS%/',$value->sons,self::$output);
						$id = intval($value->id);
						self::$level++;	
							self::createMenuOutput($obj,$id,$opt); 
						self::$level--;	
															
						self::$output .= '</li>'.PHP_EOL;	
					
					}
			
				}	 		
			}
		}
		if ($has_children === true && self::$level > $opt['ulIsMain']) {
			self::$output .= '</ul>'.PHP_EOL;
		}

		return self::$output;
	}	
		

	
	
	/* SQL QUERIES */
	public static function setMenuTreeData($opt) 
	{
		$optDef = array('langUser'=>'it','ordering'=>'ASC','getbreadcrumbs'=>0,'hideactive'=>1); 
		$opt = array_merge($optDef,$opt);
		$languages = self::$globalSettings['languages'];

		$table = DB_TABLE_PREFIX.'menu';		
		$qry = "SELECT m.id AS id, m.parent AS parent";	
		foreach($languages AS $lang) {		
			$qry .= ", m.title_".$lang." AS title_".$lang;
			}
		$qry .= ", m.ordering AS ordering, m.type AS type, m.url AS url, m.target AS target, m.alias AS alias, m.active AS active";
		foreach($languages AS $lang) {		
			$qry .= ", (SELECT mp.title_".$lang." FROM ".$table." AS mp WHERE m.parent = mp.id) AS titleparent_".$lang;
			}
		$qry .= ", (SELECT COUNT(id) FROM ".$table." AS s WHERE s.parent = m.id AND s.active = 1) AS sons";
		$qry .= " FROM ".$table." AS m WHERE ";
		
		if ( $opt['hideactive'] == 1 ) $qry .= "m.active = 1 AND ";
		
		$qry .= "m.parent = :parent ORDER BY ordering ".$opt['ordering'];		
		//echo $qry;
		Sql::resetListTreeData();
		Sql::resetListDataVar();
		Sql::setListTreeData($qry,0,$opt);
		$obj = Sql::getListTreeData();
		if (Core::$resultOp->error == 1) die('Errore database menu principale');
		return $obj;
	}	

	public static function resetOutput() {
		self::$output = '';	
	}

	private static function getTitlesVal($value,$opt) 
	{
		$titlesVal = array();
		$fieldTitle = 'title_';
 		$fieldTitleSeo = 'title_seo_';
 		$fieldTitleMeta = 'title_meta_';         		        		
 		$titlesVal['title'] = Multilanguage::getLocaleObjectValue($value,$fieldTitle,Config::$langVars['user'],array());
 		$titlesVal['titleSeo'] = Multilanguage::getLocaleObjectValue($value,$fieldTitleSeo,Config::$langVars['user'],array());
 		$titlesVal['titleMeta'] = Multilanguage::getLocaleObjectValue($value,$fieldTitleMeta,Config::$langVars['user'],array()); 
		return $titlesVal;	
	}

	private static function getUrlFromType($value,$title,$opt) 
	{
		switch($value->type) {
			case 'label':
				$url = $opt['urlDefault'];						
			break;
			case 'module-link':
				$url = URL_SITE.$value->url;
			break;	
			case 'modulemenu':
				$url = 'javascript:void(0);';
			break;		
			default:		      
	  			$url = $value->url;
			break;
	  	}
	  		
	  	$parentstring = '';
	  	/* trova il parent alias */
	  	$parentalias = '';
	  	if ($value->parent > 0) {
	  		if (isset($value->breadcrumbs[0]['alias'])) $parentalias = $value->breadcrumbs[0]['alias'].'/';
	  	}
		
		$url = preg_replace('/%URLSITE%/',URL_SITE,$url);	
		$url = preg_replace('/%ID%/',$value->id,$url);
		//$url = preg_replace('/%ALIAS%/',$value->alias,$url);
		$url = preg_replace('/%SEO%/',$title,$url);
		$url = preg_replace('/%SEOCLEAN%/', SanitizeStrings::urlslug($title,array('delimiter'=>'-')),$url);
		$url = preg_replace('/%SEOENCODE%/', urlencode($title),$url);
		$url = preg_replace('/%TITLE%/', urlencode($title),$url); 
		$url = preg_replace('/%PARENTSTRING%/', urlencode($parentstring),$url);
		$url = preg_replace('/%PARENTALIAS%/', $parentalias,$url);
				
		return $url;
	}
	
	
}
?>