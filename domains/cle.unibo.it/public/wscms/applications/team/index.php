<?php
/**
 * Framework Siti HTML-PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * admin/homeinfobox/index.php v.4.0.0. 15/12/2022
*/

//Core::setDebugMode(1);

include_once(PATH.$App->pathApplications.Core::$request->action."/lang/".$_lang['user'].".inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/config.inc.php");
include_once(PATH.$App->pathApplications.Core::$request->action."/classes/class.module.php");

$App->sessionName = Core::$request->action;
$App->codeVersion = $App->params->codeVersion;
$App->breadcrumb[] = $App->params->breadcrumb;
$App->pageTitle = $App->params->pageTitle;

$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);

switch(substr(Core::$request->method,-4,4)) {	
	case 'Conf':
		$Module = new Module(Core::$request->action,$App->params->tables['team']);
		include_once(PATH.$App->pathApplications.Core::$request->action."/config.php");
	break;

	default:
		$Module = new Module(Core::$request->action,$App->params->tables['team']);
		include_once(PATH.$App->pathApplications.Core::$request->action."/team.php");
	break;							
}	
?>
