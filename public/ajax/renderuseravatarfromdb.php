<?php
/* ajax/renderuseravatarfromdb.php v.1.0.0. 03/07/2018 */
error_reporting(E_ALL);
ini_set('display_errors', 0);
define('PATH','../wscms/');

include_once(PATH."include/configuration.inc.php");

// autoload by composer
//include_once(PATH."classes/class.Config.php");
//include_once(PATH."classes/class.Core.php");
//include_once(PATH."classes/class.Sessions.php");
//include_once(PATH."classes/class.Sql.php");
//include_once(PATH."classes/class.SanitizeStrings.php");
//include_once(PATH."classes/class.Permissions.php");

setlocale(LC_TIME, 'ita', 'it_IT');

Config::setGlobalSettings($globalSettings);
Config::init();
Config::$defPath = '';
Core::init();
//Sql::setDebugMode(1);

// variabili globali
$App = new stdClass;
$_lang = Config::$langVars;
define('DB_TABLE_PREFIX',Sql::getTablePrefix());

$App->item = new stdClass;

$array_avatarInfo = '';
$avatarInfo = '';
$id = (isset($_GET['id']) ? intval($_GET['id']) : 0);
if ($id > 0) 
{	
	Sql::initQuery(DB_TABLE_PREFIX.'users',['*'],[$id],"id = ?");
	$App->item = Sql::getRecord();	
	/* ToolsStrings::dump($App->item); */
	if (isset($App->item->id))
	{
		if (Core::$resultOp->error == 0) 
		{	
			$avatarInfo = $App->item->avatar;
			$array_avatarInfo = unserialize($App->item->avatar_info);
		}
	}
}

if ($avatarInfo != '') {
	$img = $avatarInfo;
	@header ("Content-type: ".$array_avatarInfo['type']);
	echo $img;
} else {
	$file = PATH.'templates/default/img/avatar.png';
	@header ("Content-type: image/png");
	@header('Content-Length: ' . filesize($file));
	echo file_get_contents($file);
}
