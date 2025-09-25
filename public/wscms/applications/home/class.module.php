<?php
/* wscms/site-home/module.class.php v.3.5.0. 19/12/2017 */

class Module {
	public $error;
	public $message;
	public $messages;
	
	public function __construct(private $action,$table) 	{
		$this->appTable = $table;
		$this->error = 0;	
		$this->message ='';
		$this->messages = [];	
		}
		
	public function getItemBlockUrl($arr,$lastLogin) {
		/* preleva i dati del lla prima voce */
		$where = 'created > ?';
		if (isset($arr['query opt']['clause']) && $arr['query opt']['clause'] != '') $where = $arr['query opt']['clause'];	
		Sql::initQuery($arr['table'],['*'],[$lastLogin],$where,'','',false);		
		$itemData = Sql::getRecord();		
		$str = $arr['url item']['string'];
		$opt = $arr['url item']['opt'];
		$str = preg_replace('/%URLSITEADMIN%/',URL_SITE_ADMIN,(string) $str);	
		/* aggoinge parametri */
		if (isset($opt) && is_array($opt) && count($opt) > 0) {
			$str.= '/';
			foreach ($opt As $key=>$value) {
				switch($key) {
					case 'fieldItemRif':
						if (isset($itemData->$value)) $str.= $itemData->$value;
					break;
					}
				}
			rtrim($str,'/');
			}
			
		return $str;
		}	
			
	public function getItemUrl($itemData,$arr) {
		$str = $arr['string'];
		$opt = $arr['opt'];
		$str = preg_replace('/%URLSITEADMIN%/',URL_SITE_ADMIN,(string) $str);	
		/* aggoinge parametri */
		if (isset($opt) && is_array($opt) && count($opt) > 0) {
			$str.= '/';
			foreach ($opt As $key=>$value) {
				switch($key) {
					case 'fieldItemRif':
						if (isset($itemData->$value)) $str.= $itemData->$value.'/';
					break;
					}
				}
			$str = rtrim($str,'/');
			}
			
		return $str;
		}
	}
?>