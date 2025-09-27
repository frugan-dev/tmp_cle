<?php

/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @copyright 2020 Websync
 * classes/class.Products.php v.1.0.0. 11/12/2020
*/

class Products extends Core
{
    public static $dbTablePrefix = '';
    public static $getCompanyOwner = '';
    public static $whereCompanyOwner = '';

    public static $hideProductForUsers = false;
    public static $hideProductForUsersId = 0;

    private static $dbTable = '';
    private static $dbTableCat = '';
    private static $dbTableAtt = '';

    private static $langUser = 'it';
    private static $optSqlOptions = '';
    private static $optImageFolder = 'products/';
    private static $optQryClause = '';

    private static $optQryFieldsValues = '';
    private static $optGetCategoryOwner;

    private static $optCustomTables = [];
    private static $subWhereToQuery = '';
    private static $userRoot = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public static function getAttributesList($companies_code)
    {
        //Sql::setDebugMode(1);
        Config::initQueryParams();
        Config::$queryParams['tables'] = Config::$DatabaseTables['products attributes'];
        Config::$queryParams['fields'] = ['*'];
        Config::$queryParams['where'] = 'active = 1';
        Config::$queryParams['and'] = ' AND ';
        if ($companies_code != '') {
            Config::$queryParams['where'] .= Config::$queryParams['and'].'companies_code = ?';
            Config::$queryParams['and'] = ' AND ';
            Config::$queryParams['fieldsVal'] = [$companies_code];
        }
        Sql::$options = ['fieldTokeyObj' => 'id'];
        Sql::initQueryBasic(Config::$queryParams['tables'], Config::$queryParams['fields'], Config::$queryParams['fieldsVal'], Config::$queryParams['where']);
        $foo = Sql::getRecords();
        if (Core::$resultOp->error > 0) {
            die('errore lettura attributi');
            die();
        }
        return $foo;

    }

    public static function getProductDetails($id)
    {
        //Sql::setDebugMode(1);
        $f = ['prod.*'];
        $fv = [$id,intval($id)];

        Sql::initQuery(self::$dbTable.' AS prod', $f, $fv, 'prod.alias = ? OR prod.id = ?');
        $obj = Sql::getRecord();
        //ToolsStrings::dumpArray($obj);die('fatto 1');
        if (Core::$resultOp->error > 0) {
            die('errore lettura dettagli prodotto');
            ToolsStrings::redirect(URL_SITE.'error/db');
            die();
        }
        $obj = self::addProductFields($obj);
        return $obj;
    }

    public static function getListino($categories_id)
    {
        //Sql::setDebugMode(1);
        $obj = [];
        Config::initQueryParams();
        Config::$queryParams['tables'] = self::$DatabaseTables['products'] . ' AS prod';
        Config::$queryParams['tables'] .= ' INNER JOIN '.self::$DatabaseTables['categories'] . ' AS cat ON (prod.categories_id = cat.id)';
        //Config::$queryParams['tables'] .= '	INNER JOIN '.self::$DatabaseTables['products attributes types'] . ' AS proatt ON (proatt.id = prod.attribute_types_id)';

        Config::$queryParams['fields'] = ['prod.*'];
        Config::$queryParams['fields'][] = 'cat.title AS category,cat.alias AS category_alias';
        //Config::$queryParams['fields'][] = 'proatt.title As attribute';

        // aggiunge filtro categoria
        if ($categories_id > 0) {
            Config::$queryParams['fieldsVal'][] = $categories_id;
            Config::$queryParams['where'] = 'categories_id = ?';
            Config::$queryParams['and'] = ' AND ';
        }
        // aggiunge filtro companies_code
        if (isset($_SESSION['globalCompanyCodeDefaultCode']) && $_SESSION['globalCompanyCodeDefaultCode'] != '') {
            Config::$queryParams['fieldsVal'][] = $_SESSION['globalCompanyCodeDefaultCode'];
            Config::$queryParams['where'] .= Config::$queryParams['and'].'prod.companies_code = ?';
            Config::$queryParams['and'] = ' AND ';
        }

        // aggiungi filtri custom
        if (isset(Config::$queryParams['fieldsValCustom']) && is_array(Config::$queryParams['fieldsValCustom']) && count(Config::$queryParams['fieldsValCustom']) > 0) {
            Config::$queryParams['fieldsVal'] = array_merge(Config::$queryParams['fieldsVal'], Config::$queryParams['fieldsValCustom']);
        }
        if (isset(Config::$queryParams['whereCustom']) && Config::$queryParams['whereCustom'] != '') {
            Config::$queryParams['where'] .= Config::$queryParams['and'].Config::$queryParams['whereCustom'];
            Config::$queryParams['and'] = ' AND ';
        }

        // active
        Config::$queryParams['where'] .= Config::$queryParams['and'].'cat.active = 1 AND prod.active = 1';

        Sql::initQueryBasic(Config::$queryParams['tables'], Config::$queryParams['fields'], Config::$queryParams['fieldsVal'], Config::$queryParams['where']);
        $pdoObject = Sql::getPdoObjRecords();
        while ($row = $pdoObject->fetch()) {
            if (
                self::$hideProductForUsers == false ||
                (self::$hideProductForUsers == true && !str_contains((string) $row->hide_users_ids, (string) self::$hideProductForUsersId))
            ) {
                $obj[] = $row;
            }
        }
        //ToolsStrings::dump($obj);die();
        return $obj;
    }

    public static function getProductsList($id, $initClause = '')
    {
        //Sql::setDebugMode(1);
        $obj = [];
        $f = ['prod.*'];
        //$f = array('prod.id,prod.title,prod.hide_users_ids');
        $fv = [];
        $clause = '';
        $and = '';
        if ($initClause != '') {
            $clause = $initClause;
            $and = ' AND ';
        }
        $table = Config::$DatabaseTables['products'].' AS prod';

        // prende solo quelle voci attive
        if (Config::$hideItemNoActiveSelectQuery == true) {
            $clause = 'prod.active = 1';
            $and = ' AND ';
        }

        $hideProductForUsersId = '';
        if (self::$hideProductForUsers == true) {
            //self::$hideProductForUsersId = '5';
            $hideProductForUsersId = '"'.self::$hideProductForUsersId.'"';
        }

        if (self::$getCompanyOwner == true) {
            if (isset(self::$optCustomTables['companies'])) {
                $table .= ' INNER JOIN '.self::$optCustomTables['companies'].' AS comp ON (prod.companies_code = comp.code)';
                $f[] = 'comp.ragione_sociale AS company_ragione_sociale';
            }
        }
        if (self::$whereCompanyOwner == true && $_SESSION['globalCompanyCodeDefaultCode'] != '') {
            $fv[] = $_SESSION['globalCompanyCodeDefaultCode'];
            $clause .= $and.'prod.companies_code = ?';
            $and = ' AND ';
        }
        if (intval($id > 0)) {
            $fv[] = intval($id);
            $clause .= $and.'( prod.categories_id = ? )';
            $and = ' AND ';
        }
        if (is_array(self::$optQryFieldsValues) && count(self::$optQryFieldsValues) > 0) {
            $fv = array_merge($fv, self::$optQryFieldsValues);
        }
        if (self::$optQryClause != '') {
            $clause .= $and.self::$optQryClause;
            $and = ' and ';
        }
        if (self::$subWhereToQuery != '') {
            $clause .= $and.self::$subWhereToQuery;
        }

        //ToolsStrings::dump($fv);echo 'clause: '.$clause;

        Sql::initQueryBasic($table, $f, $fv, $clause);
        $pdoObject = Sql::getPdoObjRecords();
        //if (Core::$resultOp->error > 0) {	ToolsStrings::redirect(URL_SITE.'error/db'); die(); }

        //echo $hideProductForUsersId;
        //self::$hideProductForUsers = false;
        while ($row = $pdoObject->fetch()) {
            if (
                self::$hideProductForUsers == false ||
                (self::$hideProductForUsers == true && !str_contains((string) $row->hide_users_ids, $hideProductForUsersId))
            ) {
                $row = self::addProductFields($row);
                if (self::$optGetCategoryOwner == true) {
                    $row->category = self::addCategoryOwnerFields($row->categories_id);
                }
                if (self::$getCompanyOwner == true) {
                    $row->companies_ragione_sociale = self::addCompanyOwnerFields($row->companies_code);
                }
                $obj[] = $row;
            }
        }
        //ToolsStrings::dump($obj);die();
        return $obj;
    }

    public static function addProductFields($proobject)
    {
        if (isset($proobject->summary)) {
            $proobject->summary_nop = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', $proobject->summary);
        }
        $proobject->content_nop = preg_replace('/<p[^>]*>(.*)<\/p[^>]*>/i', '$1', (string) $proobject->content);
        return $proobject;
    }

    public static function addAttributes($id)
    {
        //Sql::setDebugMode(1);
        $obj = null;
        if ($id > 0) {
            $t = Config::$DatabaseTables['product attributes'].' AS a INNER JOIN '.Config::$DatabaseTables['products attribute types'].' AS at ON (at.id = a.products_attribute_types_id)';
            $f = ['a.*','at.title As attribute'];
            Sql::initQuery($t, $f, [$id], 'a.products_id = ? AND a.active = 1');
            $obj = Sql::getRecords();
            //print_r($obj);
        }
        return $obj;
    }

    public static function addCategoryOwnerFields($id)
    {
        Subcategories::$langUser = self::$langUser;
        $obj = Subcategories::getCategoryDetails($id, self::$dbTableCat, ['findOne' => true]);
        return $obj;
    }

    public static function addCompanyOwnerFields($code)
    {
        /*
        if (isset(self::$optCustomTables['companies'])) {
            $f = array('companies.id as companies_id','companies.ragione_sociale AS companies_ragione_sociale');
            $fv = array($code);
            Sql::initQuery(self::$optCustomTables['companies'].' AS companies',$f,$fv,'companies.code = ?' );
            $obj = Sql::getRecord();
            return (isset($obj->companies_ragione_sociale) ? $obj->companies_ragione_sociale : '');
        }
        return false;
        */
    }

    public static function setDbTable($value)
    {
        self::$dbTable = $value;
    }

    public static function setDbTableAtt($value)
    {
        self::$dbTableAtt = $value;
    }

    public static function setDbTableCat($value)
    {
        self::$dbTableCat = $value;
    }

    public static function setLangUser($value)
    {
        self::$langUser = $value;
    }

    public static function setOptSqlOptions($value)
    {
        self::$optSqlOptions = $value;
    }

    public static function setUserRoot($value)
    {
        self::$userRoot = $value;
    }

    public static function setOptImageFolder($value)
    {
        self::$optImageFolder = $value;
    }

    public static function setOptQryClause($value)
    {
        self::$optQryClause = $value;
    }

    public static function setOptQryFieldsValues($value)
    {
        self::$optQryFieldsValues = $value;
    }

    public static function setOptGetCategoryOwner($value)
    {
        self::$optGetCategoryOwner = $value;
    }

    public static function setCustomTables($array)
    {
        self::$optCustomTables = $array;
    }

    public static function addSubWhereToQuery($value)
    {
        self::$subWhereToQuery = $value;
    }

}
