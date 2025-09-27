<?php

/*	wscms/site-help/module.class.php v.3.0.0. 05/10/2016 */

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

}
