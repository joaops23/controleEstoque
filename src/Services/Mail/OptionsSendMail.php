<?php

namespace Services\Mail;


class OptionsSendMail
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
        if(count($args) > 0) {
            foreach($args as $index => $value) {
                $this->$index = $value;
            }
        }
    }
}