<?php
/* wscms/newsletter/module.class.php v.1.0.0. 20/06/2016 */

class Module {
	private $action;
	public $error;
	public $message;
	public $messages;
	
	public function __construct($action,$table) 	{
		$this->action = $action;
		$this->appTable = $table;
		$this->error = 0;	
		$this->message ='';
		$this->messages = array();		
		}
		
	public function getNewsletter($table,$id=0){
		$obj = '';
		
		/* prende la newsletter indicata */
		Sql::initQuery($table,array('*'),array($id),'active = 1 AND id = ?');
		$obj = Sql::getRecord();
		if (Core::$resultOp->error == 0) {
			/* se è ancora nullo prende il primo */
			if(!isset($obj->id) || intval($obj->id) == 0) {
				Sql::setClause('active = 1');
				Sql::setOrder('datatimeins ASC');
				$obj = Sql::getRecord();			
				/* se è ancora nullo segnale errore */
				if(!isset($obj->id) || intval($obj->id) == 0) {
					$this->message = "Devi creare o attivare almeno una newsletter!";
					$this->error = 1;
					}				
				}
			} else {
				$this->message = "Devi creare o attivare almeno una newsletter!";
				$this->error = 1;
				}
		return $obj;
		}


		
	public function getTemplatesArray($templatesFolder) 
	{
		//echo $templatesFolder;
		$arr = array();
		if ($handle = opendir($templatesFolder)){
			while ($file = readdir($handle)) {
				if (!is_dir(PATH_UPLOAD_DIR.$templatesFolder.$file)) {
					if ($file != "." && $file != ".." && !is_dir($templatesFolder.$file)) $arr[] = $file;
		  			}
				}
			}
		closedir($handle);		
		//ToolsStrings::dump($arr);
		return $arr;
	}
}
?>
