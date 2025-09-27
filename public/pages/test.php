<?php

/* test.php v.3.5.4. 01/10/2019 */

//Sql::setDebugMode(1);

/* gestione titoli pagina */
$App->titles = Utilities::getTitlesPage('Test', $App->modulePageData, $_lang['user'], []);
$App->breadcrumbs->title = 'Test';

if (Core::$resultOp->error == 0) {
    switch (Core::$request->method) {
        default:

            $App->blocchi = [
                '1' => 'blocco 1',
                '2' => 'blocco 2',
                '3' => 'blocco 3',
                '4' => 'blocco 4',
                '5' => 'blocco 5',
                '6' => 'blocco 6',
                '7' => 'blocco 7',
                '8' => 'blocco 8',
                '9' => 'blocco 9',
            ];

            break;
    }
}

/* SEZIONE VIEW */

switch ($App->view) {
    default:
        break;
}
