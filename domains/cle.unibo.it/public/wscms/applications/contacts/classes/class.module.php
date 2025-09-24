<?php
/* wscms/ecommerce/class/class.module.phpv.3.2.0. 02/03/2017 */

class Module {
	private $action;
	public $error;
	public $message;
	public $messages;
	
	public function __construct($action,$table) 	{
		$this->action = $action;
		$this->table = $table;
		$this->error = 0;	
		$this->message ='';
		$this->messages = array();		
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
		
	}
?>
