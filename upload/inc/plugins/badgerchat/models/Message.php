<?php
class Message
{
    public $Id;
    public $SentAt;
    public $User;
    public $Ip;
    public $Message;

    function __construct($Id, $SentAt, $User, $Ip, $Message)
    {
        $this->Id = $Id;
        $this->SentAt = $SentAt;
        $this->User = $User;
        $this->Ip = $Ip;
        $this->Message = $Message;
    }
}