<?php

/* pages/site_main.php v.3.5.4. 27/06/2019 */

$App->mainMenu = '';
$optMainMenuDiv['activepage'] = $App->pageActive;
$optMenuPagesDiv['activepage'] = $App->pageActive;

// modulo pages
$App->menuPages = '';
if (is_array($dataMenuPages) && count($dataMenuPages) > 0) {
    $App->menuPages = Pages::createMenuDivFromSubPages($dataMenuPages, 0, $optMenuPagesDiv);
}
//ToolsStrings::dump($App->menuPages);die('fatto');

$App->menuSitePages = '';
if (is_array($dataMenuSitePages) && count($dataMenuSitePages) > 0) {
    $App->menuSitePages = Pages::createMenuDivFromSubPages($dataMenuSitePages, 0, $optMenuPagesDiv);
}

$optMainMenuDiv['modulesmenu'] = [
    'pages' => ['replace' => '/%MENUPAGES%/','values' => $App->menuPages],
    //'categories'=>array('replace'=>'/%MENUCATEGORIES%/','values'=>$App->menuCategories)
    'site-pages' => ['replace' => '/%MENUSITEPAGES%/','values' => $App->menuSitePages],
    //'categories'=>array('replace'=>'/%MENUCATEGORIES%/','values'=>$App->menuCategories)
];
$App->mainMenu = Menu::createMenuOutputFromTemplate($dataMainMenu, 0, $optMainMenuDiv);

// messaggi sistema
$App->systemMessages = '';
$systemMessages = new stdClass();
if (isset($_SESSION['message']) && $_SESSION['message'] != '') {
    $mess = explode('|', (string) $_SESSION['message']);
    unset($_SESSION['message']);
}
if (isset($mess[0])) {
    $systemMessages->error = $mess[0];
}
if (isset($mess[1])) {
    $systemMessages->message = $mess[1];
}
$appErrors = Utilities::getMessagesCore($systemMessages);
[$show, $error, $type, $content] = $appErrors;
if ($show == true) {
    if ($type == 0 && $error > 0) {
        $type = $error;
    }

    if (isset($templateSystemMessages) && $templateSystemMessages != '') {
        $App->systemMessages .= $templateSystemMessages['container'];
        if ($type == 2) {
            $App->systemMessages = preg_replace('/%ALERT%/', (string) $templateSystemMessages['warning'], $App->systemMessages);
        }
        if ($type == 1) {
            $App->systemMessages = preg_replace('/%ALERT%/', (string) $templateSystemMessages['danger'], (string) $App->systemMessages);
        }
        if ($type == 0) {
            $App->systemMessages = preg_replace('/%ALERT%/', (string) $templateSystemMessages['success'], (string) $App->systemMessages);
        }
        if ($type > 2) {
            $App->systemMessages = preg_replace('/%ALERT%/', (string) $templateSystemMessages['danger'], (string) $App->systemMessages);
        }
        $App->systemMessages = preg_replace('/%MESSAGE%/', (string) $content, (string) $App->systemMessages);
    } else {
        $App->systemMessages .= '<div id="systemMessageID" class="alert';
        if ($type == 2) {
            $App->systemMessages .= ' alert-warning';
        }
        if ($type == 1) {
            $App->systemMessages .= ' alert-danger';
        }
        if ($type == 0) {
            $App->systemMessages .= ' alert-success';
        }
        if ($type > 2) {
            $App->systemMessages .= ' alert-danger';
        }
        $App->systemMessages .= '">'.$content.'</div>';
    }
}

// cookie modal
Config::$langVars['modal privacy policy content'] = preg_replace('/%URLPRIVACYPOLICYPAGE%/', (string) $App->urlprivacypolicypage, (string) Config::$langVars['modal privacy policy content']);
Config::$langVars['modal privacy policy content'] = preg_replace('/%URLCOOKIEPOLICYPAGE%/', (string) $App->urlcookiepolicypage, (string) Config::$langVars['modal privacy policy content']);
