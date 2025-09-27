<?php

/*	classes/class.Users.php v.1.0.0. 11/02/2021 */

class Users extends Core
{
    public static $dbTable;

    public static $whereDbClause = [];
    public static $fieldsDbSelect = ['users.*'];
    public static $fieldsDbValues = [];
    public static $clauseQueryDb = '';
    public static $details;

    public static $queryParams = [];

    public static $qryFields = ['*'];
    public static $qryFieldsValues = [];
    public static $qryClause = '';
    public static $qryAndClause = '';
    public static $qryOrder = '';
    public static $qryLimit = '';

    public static $HideLevelsIdAliasInQuery = '';
    public static $HideRootInQuery = true;

    public static $ViewLevelsIdAliasInQuery = '';

    public static $UserCompaniesCode = '';

    public static $langUser = 'it';

    public function __construct()
    {
        parent::__construct();
    }

    public static function initsetQueryParams()
    {
        self::$queryParams = [
            'tables'			=> self::$dbTable.' AS users',
            'fields'			=> ['users.*'],
            'fieldsValues'		=> [],
            'whereClause'		=> '',
            'whereClauseAnd'	=> '',
            'order'				=> 'users.surname ASC, users.name ASC',
            'tablePrefix'		=> '',
        ];
    }

    public static function checkIfUsersExist()
    {
        return Sql::checkIfRecordExists();
    }

    public static function getUsersFromCompaniesCode()
    {
        //echo 'code'.self::$UserCompaniesCode;
        $foo = [];
        //Core::setDebugMode(1);
        self::$queryParams['tables'] = 	Sql::getTablePrefix().'ass_companies_code_users AS ass INNER JOIN '.self::$dbTable.' AS users ON (ass.users_id = users.id)';
        self::$queryParams['fieldsValues'] = [
            'ass.*',
            'users.*',
            '(SELECT comuni.nome FROM '.Sql::getTablePrefix().'location_comuni AS comuni WHERE comuni.id = users.location_comuni_id) AS comune',
            '(SELECT province.nome FROM '.Sql::getTablePrefix().'location_province AS province WHERE province.id = users.location_province_id) AS provincia',
            '(SELECT nations.title_'.self::$langUser.' FROM '.Sql::getTablePrefix().'location_nations AS nations WHERE nations.id = users.location_nations_id) AS nations',
        ];
        self::$queryParams['fieldsValues'] = [self::$UserCompaniesCode];
        self::$queryParams['whereClause'] = 'ass.companies_code = ?';

        Sql::initQueryBasic(
            self::$queryParams['tables'],
            self::$queryParams['fields'],
            self::$queryParams['fieldsValues'],
            self::$queryParams['whereClause']
        );

        $pdoObject = Sql::getPdoObjRecords();
        if (Core::$resultOp->error > 0) {
            die('errore db get records users');
            ToolsStrings::redirect(URL_SITE.'error/db');
        }
        while ($row = $pdoObject->fetch()) {
            $foo[] = $row;
        }
        return $foo;
    }

    public static function getUsersList()
    {
        //Core::setDebugMode(1);
        $obj = [];

        // nascondi root
        if (self::$HideRootInQuery == true) {
            self::$queryParams['whereClause'] .= self::$queryParams['whereClauseAnd'] .self::$queryParams['tablePrefix'].'is_root = 0';
            self::$queryParams['whereClauseAnd'] = ' AND ';
        }

        // nascondi levels_id_alias_ids
        if (is_array(self::$HideLevelsIdAliasInQuery) && count(self::$HideLevelsIdAliasInQuery) > 0) {
            $subwhere = [];
            foreach (self::$HideLevelsIdAliasInQuery as $value) {
                $subwhere[] = self::$queryParams['tablePrefix'].'levels_id_alias <> ?';
                self::$queryParams['fieldsValues'][] = $value;
            }
            if (count($subwhere) > 0) {
                self::$queryParams['whereClause'] .= self::$queryParams['whereClauseAnd'] .'('.implode(' AND ', $subwhere).')';
                self::$queryParams['whereClauseAnd'] = ' AND ';
            }
        }

        // visualizza solo  levels_id_alias_ids
        if (is_array(self::$ViewLevelsIdAliasInQuery) && count(self::$ViewLevelsIdAliasInQuery) > 0) {
            $subwhere = [];
            foreach (self::$ViewLevelsIdAliasInQuery as $value) {
                $subwhere[] = self::$queryParams['tablePrefix'].'levels_id_alias = ?';
                self::$queryParams['fieldsValues'][] = $value;
            }
            if (count($subwhere) > 0) {
                self::$queryParams['whereClause'] .= self::$queryParams['whereClauseAnd'] .'('.implode(' AND ', $subwhere).')';
                self::$queryParams['whereClauseAnd'] = ' AND ';
            }
        }

        //ToolsStrings::dump(self::$queryParams['fieldsValues']);
        Sql::initQueryBasic(
            self::$queryParams['tables'],
            self::$queryParams['fields'],
            self::$queryParams['fieldsValues'],
            self::$queryParams['whereClause']
        );

        $pdoObject = Sql::getPdoObjRecords();
        if (Core::$resultOp->error > 0) {
            die('errore db get records users');
            ToolsStrings::redirect(URL_SITE.'error/db');
        }
        while ($row = $pdoObject->fetch()) {
            $obj[] = $row;
        }
        return $obj;
    }

    public static function oldGetUsersList()
    {
        //Core::setDebugMode(1);
        $obj = [];
        $table = self::$dbTable.' AS users';
        Sql::initQueryBasic($table, self::$fieldsDbSelect, self::$fieldsDbValues, self::$clauseQueryDb);
        $pdoObject = Sql::getPdoObjRecords();
        if (Core::$resultOp->error > 0) {
            die('errore db get records users');
            ToolsStrings::redirect(URL_SITE.'error/db');
        }
        while ($row = $pdoObject->fetch()) {
            $obj[] = $row;
        }
        return $obj;
    }

    public static function getUserDetails($id)
    {
        //Core::setDebugMode(1);
        $obj = new stdClass();
        Sql::initQuery(
            self::$dbTable.' AS users',
            [
                'users.*',
                '(SELECT comuni.nome FROM '.Sql::getTablePrefix().'location_comuni AS comuni WHERE comuni.id = users.location_comuni_id) AS comune',
                '(SELECT province.nome FROM '.Sql::getTablePrefix().'location_province AS province WHERE province.id = users.location_province_id) AS provincia',
                '(SELECT nations.title_'.self::$langUser.' FROM '.Sql::getTablePrefix().'location_nations AS nations WHERE nations.id = users.location_nations_id) AS nations',
            ],
            [$id],
            'users.id = ?',
            '',
            '',
            false
        );
        $obj = Sql::getRecord();
        //ToolsStrings::dump($obj);die();
        if (Core::$resultOp->error > 0) {
            die('errore db get record user');
            ToolsStrings::redirect(URL_SITE.'error/db');
        }
        return $obj;
    }

    public static function getUserDetailsFromCompaniesCode($code)
    {
        //Core::setDebugMode(1);
        $obj = new stdClass();
        Sql::initQuery(
            Sql::getTablePrefix().'ass_companies_code_users AS ass
			INNER JOIN '.self::$dbTable.' AS users ON (ass.users_id = users.id)',
            [
                'ass.*',
                'users.*',
                '(SELECT comuni.nome FROM '.Sql::getTablePrefix().'location_comuni AS comuni WHERE comuni.id = users.location_comuni_id) AS comune',
                '(SELECT province.nome FROM '.Sql::getTablePrefix().'location_province AS province WHERE province.id = users.location_province_id) AS provincia',
                '(SELECT nations.title_'.self::$langUser.' FROM '.Sql::getTablePrefix().'location_nations AS nations WHERE nations.id = users.location_nations_id) AS nations',
            ],
            [$code],
            'ass.companies_code = ? and levels_id_alias = 0',
            '',
            '',
            false
        );
        $obj = Sql::getRecord();
        //ToolsStrings::dump($obj);die();
        if (Core::$resultOp->error > 0) { /*die('errore db get record');*/ ToolsStrings::redirect(URL_SITE.'error/db');
        }
        return $obj;
    }

    public static function createHash($sitecodekey, $username, $email)
    {
        return sha1($sitecodekey.$username.$email);
    }

    public static function add()
    {
        //Sql::setDebugMode(1);
        $f = [];
        $fv = [];

        foreach (self::$details as $key => $value) {
            $f[] = $key;
            $fv[] = $value;
        }
        /*
        ToolsStrings::dumpArray($f);
        ToolsStrings::dumpArray($fv);
        */
        Sql::initQuery(self::$dbTable, $f, $fv);
        Sql::insertRecord();
    }

    public static function parseEmailText($text, $opt = [])
    {
        $optDef = ['customFields' => [],'customFieldsValue' => []];
        $opt = array_merge($optDef, $opt);
        $text = preg_replace('/%SITENAME%/', (string) SITE_NAME, (string) $text);
        if ((is_array($opt['customFields']) && count($opt['customFields']))
            && (is_array($opt['customFieldsValue']) && count($opt['customFieldsValue']))
            && (count($opt['customFields']) == count($opt['customFieldsValue']))
        ) {
            foreach ($opt['customFields'] as $key => $value) {
                $text = preg_replace('/'.$opt['customFields'][$key].'/', (string) $opt['customFieldsValue'][$key], (string) $text);
            }
        }
        if (isset(self::$details->username)) {
            $text = preg_replace('/%USERNAME%/', self::$details->username, (string) $text);
        }
        if (isset(self::$details->email)) {
            $text = preg_replace('/%EMAIL%/', self::$details->email, (string) $text);
        }
        return $text;
    }

}
