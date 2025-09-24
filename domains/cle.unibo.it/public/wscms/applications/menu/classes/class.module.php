<?php
/* wscms/pages/class.module.php v.3.5.2. 26/04/2018 */

class Module {
	private $action;
	private $mainData;
	private $pagination;
	public $error;
	public $message;
	public $messages;	
	public $mySessionsApp;

	public function __construct($table,$action,$session) 	{
		$this->table = $table;
		$this->action = $action;
		$this->error = 0;	
		$this->message ='';
		$this->messages = array();		
		$this->mySessionsApp = $session;
		}
		
	public function getAlias($id,$alias,$title) {
		if ($alias == '') $alias = $title;
		$alias = SanitizeStrings::cleanTitleUrl($title);
		
			
		$clause = 'alias = ?';
		$fieldValues = array($alias);
		if ($id > 0) {
			$clause .= 'AND id <> ?';
			$fieldValues[] = $id;
			}
		Sql::initQuery($this->table,array('id'),$fieldValues,$clause);
		$count = Sql::countRecord();
		if (Core::$resultOp->error == 0) {
			if ($count > 0) $alias .= $alias.time();		
			}
		return $alias;
		}
	
	public function listMainData($fields,$page,$itemsForPage,$languages,$opt=array()) {
		//Core::setDebugMode(1);
		$optDef = array('lang'=>'it','ordering'=>'ASC','levelString'=>'<button type="button" class="btn btn-primary btn-xs"><i class="fa fa-chevron-right "></i></button>&nbsp;');	
		$opt = array_merge($optDef,$opt);
		$qry = "SELECT c.id AS id,
		c.parent AS parent,";
		foreach($languages AS $lang) {
			$qry .= "c.title_".$lang." AS title_".$lang.",
			c.meta_title_".$lang." AS meta_title_".$lang.",
			c.title_seo_".$lang." AS title_seo_".$lang.",";
			}
		$qry .= "c.title_".$opt['lang']." AS title,";
		$qry .= "c.ordering AS ordering,
		c.type AS type,
		c.alias AS alias,
		c.url AS url,
		c.menu AS menu,
		c.target AS target,
		c.filename AS filename,c.org_filename AS org_filename,
		c.active AS active,
		(SELECT COUNT(id) FROM ".$this->table." AS s WHERE s.parent = c.id)  AS sons,";
		foreach($languages AS $lang) {
			$qry .= "(SELECT p.title_".$lang." FROM ".$this->table." AS p WHERE c.parent = p.id)  AS titleparent_".$lang.",";
			}
		
		$qry .= "(SELECT tp.title FROM ". DB_TABLE_PREFIX."pagetemplates AS tp WHERE c.id_template = tp.id)  AS template_name";
		$qry .= ",".PHP_EOL."(SELECT COUNT(blo.id) FROM ".$this->table."_blocks AS blo WHERE blo.id_owner = c.id) AS blocks";
		$qry .= ",".PHP_EOL."(SELECT COUNT(fil.id) FROM ".$this->table."_resources AS fil WHERE fil.id_owner = c.id AND resource_type = 2) AS files";
		$qry .= ",".PHP_EOL."(SELECT COUNT(img.id) FROM ".$this->table."_resources AS img WHERE img.id_owner = c.id AND resource_type = 1) AS images";
		$qry .= ",".PHP_EOL."(SELECT COUNT(imgg.id) FROM ".$this->table."_resources AS imgg WHERE imgg.id_owner = c.id AND resource_type = 3) AS imagesgallery";
		$qry .= ",".PHP_EOL."(SELECT COUNT(vid.id) FROM ".$this->table."_resources AS vid WHERE vid.id_owner = c.id AND resource_type = 4) AS videos";
		$qry .= " FROM ".$this->table." AS c
		WHERE c.parent = :parent 
		ORDER BY ordering ".$opt['ordering'];		
		//Sql::resetListTreeData();
		//Sql::resetListDataVar();
		//Sql::setListTreeData($qry,0,$opt);				
		$this->mainData = Sql::getListParentsDataObj($qry,array(),0,$opt);
		//print_r($this->mainData);
		}	
		
	public function getTemplatesPage(){
		$obj = '';
		Sql::initQuery( DB_TABLE_PREFIX.'pagetemplates',array('*'),array(),'active = 1','ordering DESC','');
		$obj = Sql::getRecords();
		return $obj;
		}
		
	public function getTemplatePredefinito($id=0){
		$obj = '';
		/* prende il template indicato */
		Sql::initQuery( DB_TABLE_PREFIX.'pagetemplates',array('*'),array((int)$id),'active = 1 AND id = ?');
		$obj = Sql::getRecord();
		/* se non è nulla prende il predefinito */
		if(!isset($obj->id) || intval($obj->id)== 0) {
			Sql::initQuery( DB_TABLE_PREFIX.'pagetemplates',array('*'),array(),'active = 1 AND predefinito = 1');
			$obj = Sql::getRecord();
			/* se è ancora nullo prende il primo */
			if(!isset($obj->id) || intval($obj->id) == 0) {
				Sql::initQuery( DB_TABLE_PREFIX.'pagetemplates',array('*'),array(),'active = 1');
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
		
		
	public function manageParentField() {
		Sql::initQuery( DB_TABLE_PREFIX.'pages',array('parent'),array($_POST['bk_parent'],0),'parent = ?');
		//Sql::updateRecord();
		}
		
	/* gestione contenuti gestiti dal template */

	
	
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
