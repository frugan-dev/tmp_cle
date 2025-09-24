<?php
/* wscms/core/login.php v.3.5.5. 14/05/2019 */

/* variabili ambiente */
$App->pageTitle = 'Login';
$App->pageSubTitle = 'Login';
$App->pathApplication = 'application/core/';
$App->templateApp = Core::$request->action.'.html';
$App->templateBase = 'struttura-login.html';
$App->coreModule = true;
switch(Core::$request->method) {
	case 'check':
		if (isset($_POST['submit'])) {
			$App->error = 0;
			
			if (Permissions::returnCheckCsrftokenStatus() == false) {
				$App->error = 1;
				$App->messages = 'La sessione di controllo non è valida!';
			}
			
			
			if ($_POST['username'] == "") {
				$App->error = 0;
				$App->messages = $_lang['Devi inserire un nome utente!'];
				} else {
					$username = SanitizeStrings::stripMagic(strip_tags($_POST['username']));
					}
			
			if ($_POST['password'] == "") {
				$App->error = 1;
				$App->messages  = $_lang['Devi inserire la password!'];
				} else { 
					$password = SanitizeStrings::stripMagic(strip_tags($_POST['password']));
					}
			
			if ($App->error == 0) {					
								
				/* guardo se esiste l' username */	
				$App->item = new stdClass;					
				/* (tabella,campi(array),valori campi(array),where clause, limit, order, option , pagination(default false)) */
				Sql::initQuery(DB_TABLE_PREFIX.'users',
				array('id','name','surname','username','password','levels_id','avatar','template','is_root'),
				array(),
				"username = '".$username."' AND active = 1");
				$App->item = Sql::getRecord();			
				if (Core::$resultOp->error == 0) {
					if (Sql::getFoundRows() > 0) {
						$templateUser = $App->item->template;
						if ($templateUser == '') $templateUser = TEMPLATE_DEFAULT;	
						
						//$password = md5($password);
						/* echo $password.'<br>'.$itemData->password; */
						//if ($password === $App->item->password) {						
						if (password_verify($password,$App->item->password)) {
							$userSess = array();
							if ($App->item->avatar != '') $userSess['avatar'] = 'ok';
							$idUser = $App->item->id;

							$now = date('Y-m-d H:i:s');
							$lastLogin = $now;
							/* controllo se esiste il cookie altrimenti lo creo */
							if(!isset($_COOKIE[DATA_SESSIONS_COOKIE_NAME])) {
								setcookie(DATA_SESSIONS_COOKIE_NAME, $now, time() + (86400 * 30), "/"); // 86400 = 1 day
								} else {
									$lastLogin = $_COOKIE[DATA_SESSIONS_COOKIE_NAME];
									//setcookie(DATA_SESSIONS_COOKIE_NAME, $now, 1577833200, "/");
									}		
							$my_session->my_session_register('lastLogin',$lastLogin);
							$my_session->my_session_register('idUser',$idUser);
							$_MY_SESSION_VARS = array();					
							$_MY_SESSION_VARS = $my_session->my_session_read();					
							ToolsStrings::redirect(URL_SITE_ADMIN."home");
							die();						
							} else {
								Core::$resultOp->messages[] = $_lang['I dati inseriti non corrispondono!'];
								}
					
						} else {
								Core::$resultOp->messages[] = $_lang['I dati inseriti non corrispondono!'];
								}		
					} else  { 
         			Core::$resultOp->message = $_lang['Errore accesso db!'];
         			}	
				
		 		} else  {
         		Core::$resultOp->message = $_lang['Accesso negato!'];        		
         		}	
         				
			} else  {
				Core::$resultOp->error = 1;
				Core::$resultOp->message = $_lang['Accesso negato!'];
				}
	break;	
	
	default:
	break;	
	}
?>
