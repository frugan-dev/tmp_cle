<?php

//-------------------------------------------// *** framework siti html-PHP-Mysql// copyright 2009 Roberto Mantovani// http://www.robertomantovani.vr;it// email: me@robertomantovani.vr.it// news/module.class.php v.2.6.3. 06/05/2016

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
