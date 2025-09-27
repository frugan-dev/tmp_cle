<?php

/* wscms/core/password.php v.3.5.4. 31/07/2019 */

//Sql::setDebugMode(1);

/* variabili ambiente */
$App->codeVersion = ' 3.5.4.';
$App->pageTitle = ucfirst((string) $_lang['password']);
$App->pageSubTitle = preg_replace('/%ITEM%/', (string) $_lang['password'], (string) $_lang['modifica la %ITEM%']);
$App->breadcrumb[] = '<li class="active"><i class="icon-user"></i> '.preg_replace('/%ITEM%/', (string) $_lang['password'], (string) $_lang['modifica %ITEM%']).'</li>';
$App->templateApp = Core::$request->action.'.html';
$App->id = intval(Core::$request->param);
if (isset($_POST['id'])) {
    $App->id = intval($_POST['id']);
}
$App->coreModule = true;

switch (Core::$request->method) {
    case 'update':
        if ($_POST) {
            $password = (isset($_POST['password']) && $_POST['password'] != '') ? SanitizeStrings::stripMagic($_POST['password']) : '';
            $passwordCK = (isset($_POST['passwordCK']) && $_POST['passwordCK'] != '') ? SanitizeStrings::stripMagic($_POST['passwordCK']) : '';
            if ($password != '') {
                if ($password === $passwordCK) {
                    $password = password_hash((string) $password, PASSWORD_DEFAULT);
                } else {
                    Core::$resultOp->error = 1;
                    Core::$resultOp->message = $_lang['Le due password non corrispondono!'];
                }
            } else {
                Core::$resultOp->error = 1;
                Core::$resultOp->message = $_lang['Devi inserire la password!'];
            }

            if (Core::$resultOp->error == 0) {
                /* (tabella,campi(array),valori campi(array),where clause, limit, order, option , pagination(default false)) */
                Sql::initQuery(DB_TABLE_PREFIX.'users', ['password'], [$password,$App->id], 'id = ?');
                Sql::updateRecord();
                if (Core::$resultOp->error == 0) {
                    Core::$resultOp->message = $_lang['Password modificata correttamente! Sarà effettiva al prossimo login.'];
                }
                $App->id	 = $_POST['id'];
            }
        } else {
            Core::$resultOp->error = 1;
            Core::$resultOp->message = $_lang['Devi inserire tutti i campi richiesti!'];
        }

        // no break
    default:
        if ($App->id > 0) {
            /* recupera i dati memorizzati */
            $App->item = new stdClass();
            /* (tabella,campi(array),valori campi(array),where clause, limit, order, option , pagination(default false)) */
            Sql::initQuery(Sql::getTablePrefix().'users', ['username','password'], [$App->id], 'id = ?');
            $App->item = Sql::getRecord();
            $App->defaultJavascript = "messages['Le due password non corrispondono!'] = '".addslashes((string) $_lang['Le due password non corrispondono!'])."'";
        } else {
            //ToolsStrings::redirect(URL_SITE_ADMIN."home");
            //die();
        }
        break;
}

$App->jscript[] = '<script src="'.URL_SITE_ADMIN.$App->pathApplicationsCore.'/templates/'.$App->templateUser.'/js/password.js" type="text/javascript"></script>';
