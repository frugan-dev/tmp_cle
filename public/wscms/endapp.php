<?php

/* app/endapp.php v.1.0.0. 29/04/2021 */

// messaggi sistema
if (isset($_MY_SESSION_VARS['message']) && $_MY_SESSION_VARS['message'] != '') {
    $mess = explode('|', (string) $_MY_SESSION_VARS['message']);
    $_MY_SESSION_VARS = $my_session->my_session_unsetVar('message');
}
if (isset($_SESSION['message']) && $_SESSION['message'] != '') {
    $mess = explode('|', (string) $_SESSION['message']);
    unset($_SESSION['message']);
}
if (isset($mess[0])) {
    Core::$resultOp->error = $mess[0];
}
if (isset($mess[1])) {
    Core::$resultOp->message = $mess[1];
}

$App->systemMessages = '';
$appErrors = Utilities::getMessagesCore(Core::$resultOp);
[$show, $error, $type, $content] = $appErrors;
if ($error > 0 && $type == 0) {
    $type = $error;
}
if ($show == true) {
    $App->systemMessages .= '<div class="row"><div class="col-md-12"><div id="systemMessageID" class="alert';
    if ($type == 2) {
        $App->systemMessages .= ' alert-warning';
    }
    if ($type == 1) {
        $App->systemMessages .= ' alert-danger';
    }
    if ($type == 0) {
        $App->systemMessages .= ' alert-success';
    }
    $App->systemMessages .= '">'.$content.'</div></div></div>';
}
// fine messaggi sistema

//ToolsStrings::dump(Config::$userModules);

//ToolsStrings::dump($App->user_module_active);

//ToolsStrings::dump($App->userLoggedData);

//ToolsStrings::dump(Config::$userModulesTree);

$App->rightCodeMenu = '';
foreach (Config::$userModulesTree as $sectionKey => $sectionModules) {
    $x1 = 0;
    foreach ($sectionModules as $module) {

        if (Permissions::checkIfModulesIsReadable($module->name, $App->userLoggedData, $App->user_module_active) == true) {

            //print_r($module);

            $outputMenu = '';

            $menu = json_decode((string) $module->code_menu) or die('Errore nel campo menu. Formato Json non valido!'.$module->code_menu);

            //print_r($menu);

            $havesubmenu = 0;
            if (isset($menu->submenus) && count($menu->submenus)) {
                $havesubmenu = 1;
            }

            $classLiMain = ' class="nav-item"';
            if (isset($App->breadcrumb[1]['name']) && $App->breadcrumb[1]['name'] == $module->name) {
                $classLiMain = ' class="nav-item active"';
            }

            $auth = 0;
            if (isset($menu->auth) && $menu->auth == '1') {
                $auth = 1;
            }

            if ($auth == 0) {

                $outputUlSubmenu = '';
                $outputLiSubmenu = '';

                $moduleName = ($module->name ?? '');
                $moduleLabel = ($module->label ?? '');
                $menuName = ($menu->name ?? '');
                $menuIcon = ($menu->icon ?? '');
                $menuAction = ($menu->action ?? '');
                $menuLabel = ($menu->label ?? '');
                $havesubmenu = 0;
                $collapsed = ' collapsed';

                // crea sub menu
                $divSubmenuClass = 'collapse';
                $divSubmenuData = ' aria-labelledby="heading'.$moduleName.'" data-parent="#accordionSidebar"';

                if (isset($menu->submenus) && is_array($menu->submenus) && count($menu->submenus) > 0) {

                    $havesubmenu = 1;
                    // crea li sub menu
                    foreach ($menu->submenus as $submenu) {
                        //print_r($submenu);
                        $havesubmenu1 = 0;
                        if (isset($submenu->submenus) && count($submenu->submenus)) {
                            $havesubmenu1 = 1;
                        }

                        $submanuClass = '';
                        if (isset($App->breadcrumb[2]['name']) && $App->breadcrumb[2]['name'] == $submenu->name) {
                            $submanuClass = ' class="active"';
                        }

                        $subauth = 0;
                        if (isset($submenu->auth) && $submenu->auth == '1') {
                            $subauth = 1;
                        }
                        if ($subauth == 0) {

                            $submenuUrl = URL_SITE_ADMIN;
                            $submanuLabel = $submenu->label;
                            if (isset($localStrings[$submanuLabel])) {
                                $submanuLabel = $localStrings[$submanuLabel];
                            }
                            $submenuName = ($submenu->name ?? '');
                            $submenuIcon = ($submenu->icon ?? '');
                            $submenuAction = ($submenu->action ?? '');

                            $submenuUrl .= $moduleName.'/'.$submenuName;

                            if ($submenuAction == Core::$request->action) {
                                $divSubmenuClass = 'collapse show';
                                $collapsed = '';
                            }

                            $submanuClass = 'collapse-item';
                            $outputLiSubmenu .= '<a class="'.$submanuClass.'" href="'.$submenuUrl.'">'.$submenuIcon.' '.$submanuLabel.($havesubmenu1 == 1 ? '<span class="fa arrow"></span>' : '').'</a>'.PHP_EOL;

                        }
                    }

                    $outputUlSubmenu .= '<div id="collapse'.$moduleName.'" class="'.$divSubmenuClass.'"'.$divSubmenuData.'><div class="bg-white py-2 collapse-inner rounded">'.$outputLiSubmenu.'</div></div>';
                }

                $liMainClass = 'nav-item';
                $liMainData = '';
                $liMainHrefClass = 'nav-link';
                $liMainHrefData = '';

                if ($havesubmenu == 1) {
                    $liMainHrefClass .= $collapsed;
                    $liMainHrefData = ' data-toggle="collapse" data-target="#collapse'.$moduleName.'" aria-expanded="true" aria-controls="collapse'.$moduleName.'"';
                }

                // crea il li principale
                $outputMenu = '<li class="'.$liMainClass.'"'.$liMainData.'>';
                PHP_EOL;
                $outputMenu .= '<a class="'.$liMainHrefClass.'"'.$liMainHrefData.' href="'.URL_SITE_ADMIN.$moduleName.'">'.$menuIcon.' <span>'.$menuLabel.'</span>'.($havesubmenu == 1 ? '<span class="fa arrow"></span>' : '').'</a>';

                $outputMenu .= $outputUlSubmenu;

                $outputMenu .= '</li>'.PHP_EOL;

                // sostituiso il modulename con la localizzazione se esiste
                if (isset($localStrings[$moduleLabel])) {
                    $moduleLabel = $localStrings[$moduleLabel];
                }
                $outputMenu = preg_replace('/%LABEL%/', $moduleLabel, $outputMenu);
                $outputMenu = preg_replace('/%NAME%/', $moduleLabel, (string) $outputMenu);

                $App->rightCodeMenu .= $outputMenu;

                $x1++;

            }

        }

    }

    if ($x1 > 0) {
        $App->rightCodeMenu .= '';
    }
}
