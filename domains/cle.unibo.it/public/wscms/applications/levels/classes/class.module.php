<?php
/* wscms/levels/module.class.php v.3.5.2. 20/02/2018 */

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
	}