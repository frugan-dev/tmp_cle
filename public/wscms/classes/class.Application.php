<?php
/*
	framework siti html-PHP-Mysql
	copyright 2009 Roberto Mantovani
	http://www.robertomantovani.vr.it
	email: me@robertomantovani.vr.it
	Application.class.php v.2.6.3.1 11/05/2016
*/
class Application extends Core {

	public function __construct() 	{
		parent::__construct();
		}
		
	public function getParentCategories($obj,$parent,$idcat) {
		Sql::initQuery(Sql::getTablePrefix().'catalogo_prodotti_cat',['id,parent,title_seo_it,title_it'],[$parent],'parent = ? AND active = 1');
		$listData = Sql::getRecordsData();
		if(count($listData) > 0) {
			foreach ($listData AS $key=>$value) {
				echo '<li class="list-sub-item"><a href="'.URL_SITE.'prodottilist/'.$value->id.'/'.$value->title_seo_it.'">'.$value->title_it.'</a></li>';
				$this->getParentCategories($obj,$value->id,$idcat);				
				}
			}		
		}	
		
		
		
	public function getPageHtmlContent($str) {
		$str = $this->filterHtmlContent($str,['parse'=>true]);
		return $str;
		}
		
	public function getHtmlContent($obj,$value,$opz) {
		$str = 'error object value';
		$opzDef = ['parse'=>true];	
		$opz = array_merge($opzDef,$opz);		
		if (isset($obj->$value)) $str = $obj->$value;	
		$str = $this->filterHtmlContent($str,$opz);
		return $str;	
		}
	}
?>