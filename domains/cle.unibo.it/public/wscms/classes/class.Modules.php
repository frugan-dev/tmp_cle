<?php
/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @copyright 2020 Websync
 * classes/class.Module.php v.1.0.0. 11/08/2021
*/

class Modules extends Core {

    public function __construct() {
		parent::__construct();
    }

    /* 
    Controlla se un dato esiste già nella tabella->campo indicata
    Parametri richiesti:
    $table @string = la tabella della ricerca
    $fieldid @string = il campo COUNT() della tabella della ricerca
    $field @string = il campo della tabella della ricerca
    $labelfield @string = la etichetta del campo di ricerca
    $value @string = il valore da ricercare
    $matchtype @string = il controlllo da fare (=, like, ecc)
    Risposta:
    array(
        result => 1 = il dato esiste; 0 = il dato non esiste
        messagge => eventuale messaggio
    )
    */
    public static function checkIfItemExistInDb($table,$fieldId,$field,$labelfield,$value,$matchtype = '=',$opt = array()) {
        //Core::setDebugMode(1);
        $foo = 0;
        if ($table != '' && $field != '') {         
            $clause = $field . $matchtype . ' ?';
            $fieldsValue = array($value);
            $subclause = array();
            // aggiunge exclude
            if (isset($opt['excludefields']) && is_array($opt['excludefields']) && count($opt['excludefields']) > 0) {
                foreach ($opt['excludefields'] AS $key=>$value) {
                    if (isset($opt['excludefieldsValue'][$key])) {
                        $subclause[] = $value . " " . $opt['excludefieldsmatchtype'][$key] . " ?";
                        $fieldsValue[] = $opt['excludefieldsValue'][$key];
                    }
                }
            }
            if (count($subclause) > 0) $clause .= ' AND ('.implode(' AND ',$subclause).')';           
            Config::$queryParams = array();
            Config::$queryParams['tables'] = $table;
            Config::$queryParams['keyRif'] = $fieldId;
            Config::$queryParams['whereClause'] = $clause;
            Config::$queryParams['fieldsValues'] = $fieldsValue;

            //ToolsStrings::dump(Config::$queryParams);
            
            $foo = Sql::checkIfRecordExists();
        }
        if ($foo > 0) {
            $data['result'] = 1;
            $data['message'] = preg_replace('/%ITEM%/',$labelfield,Config::$langVars['Il valore per il campo %ITEM% è già presente nel nostro database!']);
        } else {
            $data['result'] = 0;
            $data['message'] = preg_replace('/%ITEM%/',$labelfield,Config::$langVars['Il valore per il campo %ITEM% è disponibile!']);
        }
       return $data;
    }

}
