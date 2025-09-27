<?php

/*	framework siti html-PHP-Mysql	copyright 2011 Roberto Mantovani	http://www.robertomantovani.vr;it	email: me@robertomantovani.vr.it	core/logout.php v.2.6.3. 22/03/2016
*/

/* Istanziamo l'oggetto */
$my_session = new my_session(SESSIONS_TIME, SESSIONS_GC_TIME, AD_SESSIONS_COOKIE_NAME);
/* Richiamiamo il metodo che distrugge la sessione */
$my_session->my_session_destroy();
/* Richiamiamo il metodo che pulire la tabella */
$my_session->my_session_gc();
/* cancello il cookie */
setcookie(AD_SESSIONS_COOKIE_NAME, '', ['expires' => time() - 1]);
setcookie(DATA_SESSIONS_COOKIE_NAME, '', ['expires' => time() - 1]);
ToolsStrings::redirect(URL_SITE_ADMIN);
