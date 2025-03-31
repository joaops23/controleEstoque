<?php

namespace Services\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class Mailer {
    private PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->setCredentials();
    }


    private final function setCredentials(): void
    {
        $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $this->mail->isSMTP();
        $this->mail->Host = getenv('MAIL_HOST');
        $this->mail->SMTPAuth = true;
        $this->mail->Username = getenv('MAIL_USER');
        $this->mail->Password = getenv('MAIL_PASS');
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $this->mail->Port = getenv('MAIL_PORT');
    }

    public function send(OptionsSendMail $options)
    {
        try{
            $this->setDestination($options);
            $this->setBody($options);

            if(!$this->mail->send()) {
                return false;
            } 

            return true;
            
        } catch(PHPMailerException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function setDestination(OptionsSendMail $options): void
    {
        $this->mail->From = $options->from;
        $this->mail->FromName = (explode("@", $options->from))[0];
        $this->mail->addAddress(
                $options->to,
                (explode("@", $options->to))[0] 
        );

        $this->mail->Subject = $options->subject;
    }

    private function setBody(OptionsSendMail $options): void
    {
        $this->mail->CharSet = 'UTF-8';
        $this->mail->msgHTML($options->body);
        $this->mail->AltBody = "Mensagem de <NOME_DO_SISTEMA>";
        $this->mail->addAttachment($options->attach);
    }


}