<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @copyright 2020 Websync
 * classes/class.Applications.php v.1.0.0. 15/01/2021
*/
class Applications extends Core {

	public static $coreRequest = '';

	public function __construct() 	{
		parent::__construct();
	}

	public static function setFiltersSessions($post,$session)
	{
		//ToolsStrings::dumpArray($post);
		unset($session['filters']);
		if (isset($post) && is_array($post) && count($post) > 0) {
			foreach ($post AS $key=>$value) {
				if (isset($value) && $value != '') {
					$session['filters'][$key] = $value;
				}
			}
		}
		return $session;
	}

	public static function getWhereClauseDBFromFiltersPost($session,$whereClause,$whereAndClause,$fields,$fieldsValues,$exclude=[]) 
	{
			//print_r($session);
		$and = '';
		if (isset($session['filters']) && is_array($session['filters']) && count($session['filters']) > 0) {
			$sesswhereClause = '';

			// per date
			if ( isset($session['filters']['datadal']) || isset($session['filters']['dataal']) ) {
				$datadal = ($session['filters']['datadal'] ?? '');
				$dataal = ($session['filters']['dataal'] ?? '');

				if ($datadal != '' && $dataal == '') {
					$fieldsValues[] = DateFormat::dateFormating($datadal,'Y-m-d');
					$sesswhereClause .= $and." DATE_FORMAT(data,'%Y-%m-%d') = ?";
					$and = ' AND ';
				} else if ( $datadal != '' && $dataal != '' ) {
					$fieldsValues[] = DateFormat::dateFormating($datadal,'Y-m-d');
					$fieldsValues[] = DateFormat::dateFormating($dataal,'Y-m-d');
					$sesswhereClause .= $and." DATE_FORMAT(data,'%Y-%m-%d') BETWEEN ? AND ?";
					$and = ' AND ';

				}
				$whereAndClause = $and;
			}

			// fine date

			foreach ($session['filters'] AS $key=>$value) {
				if ( $value != '' && !in_array($key,$exclude) ) {
					$sesswhereClause .= $and.$key.' = ?';
					$and = ' AND ';
					$fieldsValues[] = $value;
					$whereAndClause = $and;
				}
			}

			//echo '<br>whereClause 0.1: '.$whereClause;

			if ( $sesswhereClause != '') {
				if ($whereClause != '') {
					$whereClause .= $whereAndClause.'('.$sesswhereClause.')';
				} else {
					$whereClause .= '('.$sesswhereClause.')';
				}

			}

			//echo '<br>whereClause 0.2: '.$whereClause;
		}
		return [$whereClause,$whereAndClause,$fields,$fieldsValues];
	}
	
	// for table
	public static function setTableFieldOrderSession($field='',$session = null,$opt=[]) 
	{
		$optDef = [	
		];	
		$opt = array_merge($optDef,$opt);
		
		if (!isset($session[$field])) {
			$session[$field] = '';
		}

	}

	public static function getTableFieldOrderOptions($field='',$session = null,$opt=[]) 
	{
		$optDef = [
			'icon asc' 		=> 'fa fa-long-arrow-up',
			'icon desc'		=> 'fa fa-long-arrow-down',
			'icon'		=> ''
		];	
		$opt = array_merge($optDef,$opt);	
		
		$order = $session[$field];
		$orderClick = '';
		
		$output = '<a href="" title="'.$field.' ordinato come '.$order.'; clicca per ordinare '.$orderClick.'"><i class="'.$optDef['icon '.$order].'"></i></a>';
		return $output;
	} 

	public static function resetDataTableArrayVars() {
		$array['whereAll'] = '';
		$array['andAll'] = '';
		$array['fieldsValueAll'] = [];
		$array['where'] = '';
		$array['and'] = '';
		$array['fieldsValue'] = [];
		$array['limit'] = '';
		$array['order'] = '';
		$array['filtering'] = false;
		$array['orderFields'] = '';
		return $array;
	}

}
?>