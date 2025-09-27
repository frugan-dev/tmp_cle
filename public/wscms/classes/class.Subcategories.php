<?php

/**
 * Framework App PHP-MySQL
 * PHP Version 7
 * @author Roberto Mantovani (<me@robertomantovani.vr.it>
 * @copyright 2009 Roberto Mantovani
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * classes/class.Subcategories.php v.1.3.0. 14/09/2020
*/

class Subcategories extends Core
{
    public static $ordering = 'title_it ASC';
    public static $langUser = 'it';
    public static $countItems = 1;
    public static $nameFieldKeyItems;
    public static $initParent = 0;
    public static $levelString = '<i class="fa fa-chevron-right"></i>&nbsp;';
    public static $fieldKey = '';
    public static $hideId = 0;
    public static $hideSons = 0;
    public static $rifId = '';
    public static $rifIdValue = '';
    public static $dbTable = '';
    public static $optAddCompanyOwnerFields = false;
    public static $dbTablePrefix = '';
    private static $dbTableItem = '';
    private static $dbDbFieldsToAdd = [];
    private static $subWhereToQuery = '';

    public function __construct()
    {
        parent::__construct();
    }

    public static function getObjFromSubCategories()
    {
        //Core::setDebugMode(1);
        $qry = 'SELECT c.id AS id,c.parent AS parent';

        //ToolsStrings::dump();

        foreach (Config::$globalSettings['languages'] as $lang) {
            $qry .= ',c.title_'.$lang.' AS title_'.$lang;
        }
        $qry .= ',c.ordering AS ordering,c.active AS active';
        // aggiunge custom fields
        if (is_array(self::$dbDbFieldsToAdd) && count(self::$dbDbFieldsToAdd) > 0) {
            $qry .= ','.implode(',', self::$dbDbFieldsToAdd);
        }
        if (self::$countItems == true) {
            $qry .= ',(SELECT COUNT(i.id) FROM '.self::$dbTableItem.' AS i WHERE i.categories_id = c.id) AS items';
        }
        $qry .= ',(SELECT p.title_'.Config::$langVars['user'].' FROM '.self::$dbTable.' AS p WHERE c.parent = p.id)  AS titleparent';
        $qry .= ',(SELECT COUNT(id) FROM '.self::$dbTable.' AS s WHERE s.parent = c.id)  AS sons';

        $qry .= ' FROM '.self::$dbTable.' AS c
		WHERE c.parent = :parent';

        // prende solo quelle voci attive
        if (Config::$hideItemNoActiveSelectQuery == true) {
            $qry .= ' AND c.active = 1';
        }

        if (self::$subWhereToQuery != '') {
            $qry .= ' AND '.self::$subWhereToQuery;
        }
        $qry .= ' ORDER BY '.self::$ordering;

        //echo $qry;
        //die('fatto');

        $opt = [
            'orgQry'							=> $qry,
            'qryCountParentZero'				=> '',
            'lang'								=> self::$langUser,
            'fieldKey'							=> self::$fieldKey,
            'hideId'							=> 0,
            'hideSons'							=> 0,
            'rifIdValue'						=> '',
            'rifId'								=> '',
            'getbreadcrumbs'					=> 0,
            'levelString'						=> self::$levelString,
        ];
        Sql::resetListTreeData();
        Sql::resetListDataVar();
        Sql::setListTreeData($qry, self::$initParent, $opt);
        $obj = Sql::getListTreeData();
        return $obj;
    }

    public static function getCategoryDetails($id, $table, $opz)
    {
        $obj =  new stdClass();
        $actived = ($opz['actived'] ?? true);
        /* prende la categoria indicata */
        $clause = 'id = ?';
        if ($actived == true) {
            $clause .= ' AND active = 1';
        }
        if (self::$subWhereToQuery != '') {
            $clause .= ' AND '.self::$subWhereToQuery;
        }
        Sql::initQuery($table, ['*'], [$id], $clause);
        $obj = Sql::getRecord();
        $obj = self::addCustomFields($obj);
        return $obj;
    }

    public static function setDbTable($value)
    {
        self::$dbTable = $value;
    }

    public static function setDbTableItem($value)
    {
        self::$dbTableItem = $value;
    }

    public static function addCustomFields($obj)
    {
        return $obj;
    }

    public static function dbDbFieldsToAdd($array)
    {
        self::$dbDbFieldsToAdd = $array;
    }

    public static function addSubWhereToQuery($value)
    {
        self::$subWhereToQuery = $value;
    }
}
