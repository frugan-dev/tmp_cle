<?php
/* wscms/pagetemplates/module.class.php v.3.5.2. 20/02/2018 */

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

	}
?>