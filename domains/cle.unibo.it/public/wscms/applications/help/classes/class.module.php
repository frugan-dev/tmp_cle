<?php
/*	wscms/site-help/module.class.php v.3.0.0. 05/10/2016 */

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

	}
?>