<?php
/*	framework siti html-PHP-Mysql	copyright 2009 Roberto Mantovani	http://www.robertomantovani.vr;it	email: me@robertomantovani.vr.it	slides-home-rev/module.class.php v.2.6.3. 11/04/2016
*/
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