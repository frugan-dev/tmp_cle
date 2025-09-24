<?php
//-------------------------------------------// *** framework siti html-PHP-Mysql// copyright 2009 Roberto Mantovani// http://www.robertomantovani.vr;it// email: me@robertomantovani.vr.it// site-galleries/Application.class.php v.2.6.2.1. 15/03/2016

class Application {
	public $error;
	public $message;
	public $messages;
	
	public function __construct(private $action,$appTable) 	{
		$this->appTable = $appTable;
		$this->error = 0;	
		$this->message ='';
		$this->messages = [];	
		}
	}
?>