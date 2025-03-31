<?php

namespace Services\Mail;


abstract class OptionsSendMail
{
    public $to;
    public $from;
    public $reply;
    public $cc;
    public $attach;
    public $subject;
    public $body;

    public function __construct($args = array())
    {
        
    }
}