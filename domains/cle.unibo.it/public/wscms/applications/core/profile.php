<?php
/* wscms/core/profile.php v.3.5.4. 31/07/2019 */

//Sql::setDebugMode(1);

include_once(PATH.$App->pathApplicationsCore."class.module.php");
$Module = new Module(DB_TABLE_PREFIX."users");

/* variabili ambiente */
$App->codeVersion = ' 3.5.4.';
$App->pageTitle = ucfirst($_lang['profilo']);
$App->pageSubTitle = preg_replace('/%ITEM%/', $_lang['profilo'], $_lang['modifica il %ITEM%']);
$App->breadcrumb[] = '<li class="active"><i class="icon-user"></i> '.$_lang['profilo'].'</li>';
$App->templateApp = Core::$request->action.'.html';
$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) $App->id = intval($_POST['id']);
$App->coreModule = true;

$fields = array(
	'id'=>array('label'=>'ID','required'=>false,'type'=>'autoinc','primary'=>true),
	'name'=>array('label'=>'Nome','searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'surname'=>array('label'=>'Cognome','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'street'=>array('label'=>'Via','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'city'=>array('label'=>'Città','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'zip_code'=>array('label'=>'C.A.P.','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'province'=>array('label'=>'Provincia','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'state'=>array('label'=>'Stato','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'telephone'=>array('label'=>'Telefono','searchTable'=>false,'required'=>false,'type'=>'varchar'),
	'email'=>array('label'=>'Email','searchTable'=>true,'required'=>true,'type'=>'varchar'),
	'mobile'=>array('label'=>'Cellulare','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'fax'=>array('label'=>'Fax','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'skype'=>array('label'=>'Skype','searchTable'=>true,'required'=>false,'type'=>'varchar'),
	'template'=>array('label'=>'Template','searchTable'=>true,'type'=>'varchar'),
	'avatar'=>array('label'=>'Avatar','searchTable'=>false,'type'=>'blob'),
	'avatar_info'=>array('label'=>'Avatar Info','searchTable'=>false,'type'=>'varchar'),
	'active'=>array('label'=>'Attiva','required'=>false,'type'=>'int','defValue'=>0)
	);
                                 
switch(Core::$request->method) {
	case 'renderAvatarDB':
		$id = intval(Core::$request->param);
		$App->item = new stdClass;
	   if ($id > 0) {	
			Sql::initQuery(DB_TABLE_PREFIX.'users',array('*'),array($App->id),"id = ?");
			$App->item = Sql::getRecord();	
			if (Core::$resultOp->error == 0) {	
				if (isset($App->item->avatar)) {
					$array_avatarInfo = unserialize($App->item->avatar_info);					
					$img = $App->item->avatar;
					@header ("Content-type: ".$array_avatarInfo['type']);
					echo $img;				
					}
				}
			}
		die();
	break;
	
	default;
		$App->item = new stdClass;
		//echo $App->id;
		if ($App->id > 0) {
			if ($_POST) {			
				if (!isset($_POST['active'])) $_POST['active'] = 1;								
				/* recupero dati avatar */
				list($_POST['avatar'],$_POST['avatar_info']) = $Module->getAvatarData($App->id,$_lang);
				if ($Module->message != '') Core::$resultOp->messages[] = $Module->message;
				Core::$resultOp->type =  $Module->errorType;
				Core::$resultOp->error =  $Module->error;
				if (Core::$resultOp->error == 0) {
					/* controlla i campi obbligatori */
					Sql::checkRequireFields($fields);
					if (Core::$resultOp->error == 0) {	
						Sql::stripMagicFields($_POST);
						Sql::updateRawlyPost($fields,DB_TABLE_PREFIX."users",'id',$App->id);
						if(Core::$resultOp->error == 0) {
							Core::$resultOp->message = $_lang['Account modificato correttamente! Per rendere effettive le modifiche devi uscire dal sistema e loggarti nuovamente.'];	
							}
					}		
				} else {
					Core::$resultOp->error = 1;
					}	
				} 
								
		/* recupera i dati memorizzati */
		/* (tabella,campi(array),valori campi(array),where clause, limit, order, option , pagination(default false)) */
		Sql::initQuery(DB_TABLE_PREFIX.'users',array('*'),array($App->id),"id = ?");
		$App->item = Sql::getRecord();			
		$App->templatesAvaiable = $Module->getUserTemplatesArray();
		if($Module->error == 1) {	
			Core::$resultOp->error = 1;
			Core::$resultOp->message = $Module->message;
			}
		} else {
			ToolsStrings::redirect(URL_SITE_ADMIN."home");
			die();						
			}
	break;	
	}

	$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplicationsCore.'/templates/'.$App->templateUser.'/js/profile.js" type="text/javascript"></script>';

?>