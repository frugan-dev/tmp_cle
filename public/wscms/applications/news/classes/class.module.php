<?php
/* wscms/news/class.module.php v.3.5.4. 10/09/2019 */

class Module {
	public $error;
	public $message;
	public $messages;

	public function __construct(private $action,$table) 	{
		$this->table = $table;
		$this->error = 0;	
		$this->message ='';
		$this->messages = [];	
	}
		
	public function getAlias($id,$alias,$title) {
		if ($alias == '') $alias = $title;
		$alias = SanitizeStrings::cleanTitleUrl($title);		
		$clause = 'alias = ?';
		$fieldValues = [$alias];
		if ($id > 0) {
			$clause .= 'AND id <> ?';
			$fieldValues[] = $id;
		}
		Sql::initQuery($this->table,['id'],$fieldValues,$clause);
		$count = Sql::countRecord();
		if (Core::$resultOp->error == 0) {
			if ($count > 0) $alias .= $alias.time();		
		}
		return $alias;
	}

}
?>