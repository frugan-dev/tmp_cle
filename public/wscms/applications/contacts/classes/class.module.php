<?php

/* wscms/ecommerce/class/class.module.phpv.3.2.0. 02/03/2017 */

class Module
{
    public $error;
    public $message;
    public $messages;

    public function __construct(private $action, $table)
    {
        $this->table = $table;
        $this->error = 0;
        $this->message = '';
        $this->messages = [];
    }

    public function getAlias($id, $alias, $title)
    {
        if ($alias == '') {
            $alias = $title;
        }
        $alias = SanitizeStrings::cleanTitleUrl($title);
        $clause = 'alias = ?';
        $fieldValues = [$alias];
        if ($id > 0) {
            $clause .= 'AND id <> ?';
            $fieldValues[] = $id;
        }
        Sql::initQuery($this->table, ['id'], $fieldValues, $clause);
        $count = Sql::countRecord();
        if (Core::$resultOp->error == 0) {
            if ($count > 0) {
                $alias .= $alias.time();
            }
        }
        return $alias;
    }

}
