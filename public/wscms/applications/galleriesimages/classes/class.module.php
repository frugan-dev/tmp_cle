<?php

/* wscms/slides-home-rev/class.module.php v.3.5.2. 26/04/2018 */

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
