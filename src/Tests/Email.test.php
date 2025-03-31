<?php
use PHPUnit\Framework\TestCase;
use Services\Mail\OptionsSendMail;

final class EmailTest extends TestCase
{

    protected OptionsSendMail $options;

    public function TestSendByValidMail(): void
    {
        
    }



    private function setOptionsValid(): void 
    {
        $this->options->to;
        $this->options->from;
        $this->options->reply;
        $this->options->cc;
        $this->options->attach;
        $this->options->subject;
        $this->options->body;

    }
} 