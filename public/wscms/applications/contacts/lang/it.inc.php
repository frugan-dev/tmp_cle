<?php

$_lang['mappa'] = 'mappa';

/* configurazione */
$_lang['configurazione modificata'] = 'configurazione modificata';
$_lang['indirizzo email - titolo'] = "L'indirizzo delle email inviate dal modulo contatti";
$_lang['label indirizzo email - titolo'] = 'La etichetta (label) delle email inviate dal modulo contatti';
$_lang['invia email debug'] = 'invia email di debug';
$_lang['email debug - titolo'] = 'Invia una email di debug per tutte le email inviate dal modulo contatti';
$_lang['indirizzo email debug - titolo'] = "L'indirizzo della email di debug per tutte le email inviate dal modulo contatti";

$_lang['testo intro'] = 'testo intro';
$_lang['testo intro - titolo'] = 'Il testo introduttivo visualizzato nel top della pagina contatti (dipende dal template utilizzato)';

$_lang['contenuto pagina'] = 'contenuto pagina';
$_lang['contenuto pagina - titolo'] = 'Il contenuto visualizzato nella pagina contatti (di solito sotto il form e che dipende comunque dal template utilizzato)';

$_lang['url privacy page'] = 'URL pagina privacy';
$_lang['url privacy page - titolo'] = "L'URL alla pagina della privacy<br><b>%URLSITE%</b> per url dinamico";

$_lang['soggetto email admin'] = 'Soggetto email amministratore';
$_lang['soggetto email admin - titolo'] = "Il soggetto della email di conferma inviata all'amministratore del sito.";
$_lang['contenuto email admin'] = 'Contenuto email amministratore';
$_lang['contenuto email admin - titolo'] = "Il contenuto della email di conferma inviata all'amministratore del sito.
<br>Variabili che si posso utilizzare:<br><b>%SITENAME%</b> = nome del sito; <b>%NAME%</b> = campo Nome; <b>%EMAIL%</b> = campo Email; <b>%MESSAGE%</b> = campo messaggio.<br>Altre variabili dipendono dai campi presenti nel modulo.";

$_lang['soggetto email utente'] = 'Soggetto email utente';
$_lang['soggetto email utente - titolo'] = "Il soggetto della email di conferma inviata all'utente che inviato il modulo contatti.";
$_lang['contenuto email utente'] = 'Contenuto email utente';
$_lang['contenuto email utente - titolo'] = "Il contenuto della email di conferma inviata all'utente che inviato il modulo contatti.<br>Variabili che si posso utilizzare:<br><b>%SITENAME%</b> = nome del sito; <b>%NAME%</b> = campo Nome; <b>%EMAIL%</b> = campo Email; <b>%MESSAGE%</b> = campo messaggio.<br>Altre variabili dipendono dai campi presenti nel modulo.";

$_lang['latitudine - titolo'] = 'I dati di latitudine della posizione nella mappa';
$_lang['longitudine - titolo'] = 'I dati di longitudine della posizione nella mappa';

Config::$langVars = array_merge(Config::$langVars, [
    'mappa' => 'mappa',
    'configurazione modificata' => 'configurazione modificata',
    'indirizzo email - titolo' => 'L\'indirizzo delle email inviate dal modulo contatti',
    'label indirizzo email - titolo' => 'La etichetta (label) delle email inviate dal modulo contatti',
    'invia email debug' => 'invia email di debug',
    'email debug - titolo' => 'Invia una email di debug per tutte le email inviate dal modulo contatti',
    'indirizzo email debug - titolo' => 'L\'indirizzo della email di debug per tutte le email inviate dal modulo contatti',

    'testo intro' => 'testo intro',
    'testo intro - titolo' => 'Il testo introduttivo visualizzato nel top della pagina contatti (dipende dal template utilizzato)',

    'contenuto pagina' => 'contenuto pagina',
    'contenuto pagina - titolo' => 'Il contenuto visualizzato nella pagina contatti (di solito sotto il form e che dipende comunque dal template utilizzato)',

    'url privacy page' => 'URL pagina privacy',
    'url privacy page - titolo' => 'L\'URL alla pagina della privacy<br><b>%URLSITE%</b> per url dinamico',

    'soggetto email admin' => 'Soggetto email amministratore',
    'soggetto email admin - titolo' => 'Il soggetto della email di conferma inviata all\'amministratore del sito.',
    'contenuto email admin' => 'Contenuto email amministratore',
    'contenuto email admin - titolo' => 'Il contenuto della email di conferma inviata all\'amministratore del sito.
    <br>Variabili che si posso utilizzare:<br><b>%SITENAME%</b> = nome del sito; <b>%NAME%</b> = campo Nome; <b>%EMAIL%</b> = campo Email; <b>%MESSAGE%</b> = campo messaggio.<br>Altre variabili dipendono dai campi presenti nel modulo.',

    'soggetto email utente' => 'Soggetto email utente',
    'soggetto email utente - titolo' => 'Il soggetto della email di conferma inviata all\'utente che inviato il modulo contatti.',
    'contenuto email utente' => 'Contenuto email utente',
    'contenuto email utente - titolo' => 'Il contenuto della email di conferma inviata all\'utente che inviato il modulo contatti.<br>Variabili che si posso utilizzare:<br><b>%SITENAME%</b> = nome del sito; <b>%NAME%</b> = campo Nome; <b>%EMAIL%</b> = campo Email; <b>%MESSAGE%</b> = campo messaggio.<br>Altre variabili dipendono dai campi presenti nel modulo.',

    'latitudine - titolo' => 'I dati di latitudine della posizione nella mappa',
    'longitudine - titolo' => 'I dati di longitudine della posizione nella mappa',

]);
