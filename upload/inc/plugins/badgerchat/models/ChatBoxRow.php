<?php

class ChatBoxRow
{
    public $User;
    public $Message;

    function __construct($User, $Message)
    {
        $this->User = $User;
        $this->Message = $Message;
    }
}