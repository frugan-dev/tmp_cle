<?php
/* wscms/site-pages/index.php v.1.0.1. 07/09/2016 */

class Module {
	private $mainData;
	private $pagination;
	public $error;
	public $message;
	public $messages;

	public function __construct($table,private $action,public $mySessionsApp) 	{
		$this->table = $table;
		$this->error = 0;	
		$this->message ='';
		$this->messages = [];
		}
		
	public function getAlias($oldalias,$alias,$title) {
		if($alias == '') $alias = SanitizeStrings::cleanTitleUrl($title);
		$aliascheck = false;
		do {
			$check = $this->checkIssetAlias($alias);
			if($check == true) {
				if($oldalias != $alias) $alias .= (string)time();				
				}
		} 
		while($aliascheck == true);
		if($alias == '') $alias .= (string)time();
		return $alias;
		}
		
	public function checkIssetAlias($alias) {
		$count = 0;
		Sql::initQuery($this->table,['id'],[$alias],'alias = ?');
		$count = Sql::countRecord();
		if(Core::$resultOp->error == 0) {
			return ($count == 1 ? true : false);
			} else {
				return true;
				}
		}
	
	public function listMainData($fields,$page,$itemsForPage,$languages,$opz=[]) 
	{
		$opzDef = ['files'=>0,'tablefiles'=>'','images'=>0,'tableimages'=>''];	
		$opz = array_merge($opzDef,$opz);
		$qry = "SELECT c.id AS id,
		c.parent AS parent,";
		foreach($languages AS $lang) {
			$qry .= "c.title_".$lang." AS title_seo_".$lang.",
			c.title_meta_".$lang." AS title_meta_".$lang.",
			c.title_".$lang." AS title_".$lang.",
			";
			}
		$qry .= "c.ordering AS ordering,
		c.type AS type,
		c.alias AS alias,
		c.url AS url,
		c.menu AS menu,
		c.target AS target,
		c.active AS active,
		(SELECT COUNT(id) FROM ".$this->table." AS s WHERE s.parent = c.id)  AS sons,
		(SELECT p.title_it FROM ".$this->table." AS p WHERE c.parent = p.id)  AS titleparent_it,
		(SELECT tp.title_it FROM ". DB_TABLE_PREFIX."site_templates AS tp WHERE c.id_template = tp.id)  AS template_name";
		if ($opz['files'] == 1) $qry .= ",".PHP_EOL."(SELECT COUNT(fil.id) FROM ".$opz['tablefiles']." AS fil WHERE fil.id_owner = c.id) AS files";
		if ($opz['images'] == 1) $qry .= ",".PHP_EOL."(SELECT COUNT(img.id) FROM ".$opz['tableimages']." AS img WHERE img.id_owner = c.id) AS images";
		$qry .= " FROM ".$this->table." AS c
		WHERE c.parent = :parent 
		ORDER BY ordering ASC";		
		Sql::resetListTreeData();
		Sql::resetListDataVar();
		Sql::setListTreeData($qry,0);				
		$this->mainData = Sql::getListTreeData();
		}
	
	
		
	public function getTemplatesPage(){
		$obj = '';
		Sql::initQuery( DB_TABLE_PREFIX.'site_templates',['*'],[],'active = 1','ordering DESC','');
		$obj = Sql::getRecords();
		return $obj;
		}
		
	public function getSelectPageItems($case){
		$obj = '';
		switch($case) {
			case 'pageFiles':	
				Sql::initQuery();		
				Sql::setTable( DB_TABLE_PREFIX.'site_files');
				Sql::setFields(['id','title_it']);
				Sql::setClause('active = 1');
			break;
			case 'pageImages':
				Sql::initQuery();
				Sql::setTable( DB_TABLE_PREFIX.'site_images');
				Sql::setFields(['id','title_it']);			
				Sql::setClause('active = 1');
			break;	
			case 'pageGalleries':
				Sql::initQuery();
				Sql::setTable( DB_TABLE_PREFIX.'site_galleries_cat');
				Sql::setFields(['id','title_it']);
				Sql::setClause('active = 1');
			break;			
			case 'pageBlocks':
				Sql::initQuery();
				Sql::setTable( DB_TABLE_PREFIX.'site_blocks');
				Sql::setFields(['id','title_it']);
				Sql::setClause('active = 1');
			break;					
			default:
			break;			
			}
		$obj = Sql::getRecords();
		return $obj;
		}
			
	public function getPageItems($tableRif,$id,$case){
		$obj = '';
		switch($case) {
			case 'pageFiles':	
				Sql::initQuery();
				Sql::setTable( DB_TABLE_PREFIX.$tableRif.'_files');
				Sql::setClause('id_page = '.$id);
				Sql::setFields(['id_file','position']);
				Sql::setOptions(['fieldTokeyObj'=>'position']);
			break;
			case 'pageImages':			
				Sql::initQuery();
				Sql::setTable( DB_TABLE_PREFIX.$tableRif.'_images');
				Sql::setClause('id_page = '.$id);
				Sql::setFields(['id_image','position']);
				Sql::setOptions(['fieldTokeyObj'=>'position']);
			break;
			case 'pageGalleries':
				Sql::initQuery();
				Sql::setTable( DB_TABLE_PREFIX.$tableRif.'_galleries');
				Sql::setClause('id_page = '.$id);
				Sql::setFields(['id_gallery','position']);
				Sql::setOptions(['fieldTokeyObj'=>'position']);
			break;
			case 'pageBlocks':
				Sql::initQuery();
				Sql::setTable( DB_TABLE_PREFIX.$tableRif.'_blocks');
				Sql::setClause('id_page = '.$id);
				Sql::setFields(['id_block','position']);
				Sql::setOptions(['fieldTokeyObj'=>'position']);
			break;
			
			default:
			break;			
			}		
		$obj = Sql::getRecords();
		return $obj;
		}
		
	public function updatePageItems($tableRif,$id,$case){
		switch($case) {
			case 'pageFiles':				
				/* cancella i riferimenti file */
				Sql::initQuery( DB_TABLE_PREFIX.$tableRif.'_files',[],[$id],'id_page = ?');
				Sql::deleteRecord();
				/* memorizzo i vnuovi */				
				Sql::initQuery();
				Sql::setTable( DB_TABLE_PREFIX.$tableRif.'_files');
				Sql::setFields(['id_page','id_file','position']);				
				if(isset($_POST['file']) && is_array($_POST['file']) && count($_POST['file'] > 0)){
					foreach($_POST['file'] AS $key=>$value){
						if ($value > 0){
							Sql::setFieldsValue([$id,$value,$key]);
							Sql::insertRecord();							
							}
						}					
					}
			break;
			case 'pageImages':
				/* cancella i riferimenti image */
				Sql::initQuery( DB_TABLE_PREFIX.$tableRif.'_images',[],[$id],'id_page = ?');
				Sql::deleteRecord();
				/* memorizzo i nuovi */		
				Sql::initQuery( DB_TABLE_PREFIX.$tableRif.'_images',['id_page','id_image','position']);		
				if(isset($_POST['image']) && is_array($_POST['image']) && count($_POST['image'] > 0)){
					foreach($_POST['image'] AS $key=>$value){
						if ($value > 0){
							Sql::setFieldsValue([$id,$value,$key]);
							Sql::insertRecord();							
							}
						}					
					}
			break;			
			
			case 'pageGalleries':
				/* cancella i riferimenti galley */
				Sql::initQuery( DB_TABLE_PREFIX.$tableRif.'_galleries',[],[$id],'id_page = ?');
				Sql::deleteRecord();				
				/* memorizzo i vnuovi */				
				Sql::initQuery( DB_TABLE_PREFIX.$tableRif.'_galleries',['id_page','id_gallery','position']);			
				if(isset($_POST['gallery']) && is_array($_POST['gallery']) && count($_POST['gallery'] > 0)){
					foreach($_POST['gallery'] AS $key=>$value){
						if ($value > 0){
							Sql::setFieldsValue([$id,$value,$key]);
							Sql::insertRecord();							
							}
						}					
					}
			break;

			case 'pageBlocks':
				/* cancella i riferimenti block */
				Sql::initQuery( DB_TABLE_PREFIX.$tableRif.'_blocks',[],[$id],'id_page = ?');
				Sql::deleteRecord();
				/* memorizzo i vnuovi */	
				Sql::initQuery( DB_TABLE_PREFIX.$tableRif.'_blocks',['id_page','id_block','position']);							
				if(isset($_POST['block']) && is_array($_POST['block']) && count($_POST['block'] > 0)){
					foreach($_POST['block'] AS $key=>$value){
						if ($value > 0){
							Sql::setFieldsValue([$id,$value,$key]);
							Sql::insertRecord();							
							}
						}					
					}
			break;
			
			default:
			break;			
			}		
		}

		
	public function getTemplatePredefinito($id=0){
		$obj = '';
		/* prende il template indicato */
		Sql::initQuery( DB_TABLE_PREFIX.'site_templates',['*'],[(int)$id],'active = 1 AND id = ?');
		$obj = Sql::getRecord();
		/* se non è nulla prende il predefinito */
		if(!isset($obj->id) || intval($obj->id)== 0) {
			Sql::initQuery( DB_TABLE_PREFIX.'site_templates',['*'],[],'active = 1 AND predefinito = 1');
			$obj = Sql::getRecord();
			/* se è ancora nullo prende il primo */
			if(!isset($obj->id) || intval($obj->id) == 0) {
				Sql::initQuery( DB_TABLE_PREFIX.'site_templates',['*'],[],'active = 1');
				$obj = Sql::getRecord();
				/* se è ancora nullo segnale errore */
				if(!isset($obj->id) || intval($obj->id)== 0) {
					$this->message = "Devi creare almeno un template per le pagine!";
					$this->error = 1;
					}				
				}			
			}		
		return $obj;
		}
		
	public function deletePage($tableRif,$id) {
		/* controlla se la categoria ha figlie */
		Sql::initQuery($this->table,['id'],[$id],'parent = ?');
		$count = Sql::countRecord();
		if ($count > 0) {
			$this->error = 1;
			$this->message = 'Errore! La categoria ha ancora figlie associate!';
			} else {
				Sql::initQuery($this->table,[],[$id],'id = ?');
				Sql::deleteRecord();
				if(Core::$resultOp->error == 0) {
					/* cancella i contenuti associati da template */
					Sql::initQuery( DB_TABLE_PREFIX.$tableRif.'_contents',[],[$id],'id_page = ?');
					Sql::deleteRecord();
					/* cancella i riferimenti file */
					Sql::setTable( DB_TABLE_PREFIX.$tableRif.'_files');
					Sql::deleteRecord();
					/* cancella i riferimenti images */
					Sql::setTable( DB_TABLE_PREFIX.$tableRif.'_images');
					Sql::deleteRecord();
					/* cancella i riferimenti galleries */
					Sql::setTable( DB_TABLE_PREFIX.$tableRif.'_galleries');
					Sql::deleteRecord();
					/* cancella i riferimenti blocks */
					Sql::setTable( DB_TABLE_PREFIX.$tableRif.'_blocks');
					Sql::deleteRecord();		
					}			
				}			
		}
		
	public function manageParentField() {
		Sql::initQuery( DB_TABLE_PREFIX.'pages',['parent'],[$_POST['bk_parent'],0],'parent = ?');
		Sql::updateRecord();
		}
		
	/* gestione contenuti gestiti dal template */
		
	public function updatePageContents($tableRif,$id,$contents,$languageFields) {
		$fieldsValue = [];
		$filedsValue = [];		
		if ($contents > 0) {
			/* ciclo per le text area */
			for ($x=1;$x<=$contents;$x++) {	
				/* cancella il record con l'id della pagina e la posizione */		
				Sql::initQuery( DB_TABLE_PREFIX.$tableRif.'_contents',['*'],[$id,$x],'id_page = ? AND position = ?');
				Sql::deleteRecord();									
				$fieldsValue = [];
				$fields = [];
				$fields[] = 'id_page';
				$fields[] = 'position';	
				$fieldsValue[] = $id;
				$fieldsValue[] = $x;
				foreach ($languageFields AS $value) {					
					$fields[] = 'content_'.$value;					
					$f = 'content_html_'.$value.'_'.$x;					
					$content = ($_POST[$f] ?? "");
					$fieldsValue[] = $content;
					}	
				Sql::initQuery( DB_TABLE_PREFIX.$tableRif.'_contents',$fields,$fieldsValue);	
				Sql::insertRecord();			
				}
			}	
		}
		
		
	public function getPageContents($tableRif,$id,$contents,$inputName,$fieldName,$languageFields) {
		$arr = [];
		$fields = [];
		$inputValue = [];
		if ($contents > 0) {
			/* ciclo per prendere i dati */
			for ($x=1;$x<=$contents;$x++) {
				Sql::initQuery( DB_TABLE_PREFIX.$tableRif.'_contents',['*'],[$id,$x],'id_page = ? AND position = ?');
				if (!isset($arr1)) $arr1 = new stdClass();
				$arr1 = Sql::getRecord();
				foreach ($languageFields AS $value) {
					$f = 'content_'.$value;	
					$fInput = 'content_'.$value.'_'.$x;	
					$arr[$fInput] = ($arr1->$f ?? '');		
					}					
				}	
			}
		return $arr;
		}


	public function getEmptyPageContents($contents,$fieldName,$languageFields) {
		$fields = [];
		$filedsValue = [];		
		foreach ($languageFields AS $value) $fields[$value] = 'content_'.$value;
		$arr = [];
		if ($contents > 0) {			
			/* ciclo per prendere i dati */
			for ($x=1;$x<=$contents;$x++) {
				foreach ($languageFields AS $value) {
					$f = $fields[$value].'_'.$x;
					if (isset($arr[$f])) $arr[$f] = '';
					}				
				}	
			}
		return $arr;
	}

	
		
	public function getPageFilesAtt($id) {
		$arr = [];
		//$arr = array('position'=>'11','filename'=>'22','org_filename'=>'33','extension'=>'44','size'=>'55','type'=>'66');
		return $arr;
		}

	
	
	/* SEZIONE PER IL RECUPERO VAR */
	
	public function setAction($value){
		Core::$request->action = $value;
		}

	public function getMainData(){
		return $this->mainData;
		}
		
	public function getPagination(){
		return $this->pagination;
		}
		
	public function setMySessionApp($session){
		$this->mySessionsApp = $session;
		}
	
	}
?>
