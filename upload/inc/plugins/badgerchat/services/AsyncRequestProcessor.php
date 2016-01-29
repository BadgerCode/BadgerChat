<?php

if(!defined("IN_MYBB")){
    die();
}

$InstallDirectory = MYBB_ROOT . "inc/plugins/badgerchat/";

require_once($InstallDirectory . "services/MessageLoader.php");
require_once($InstallDirectory . "services/MessagePoster.php");

class AsyncRequestTypes{
    static $LoadMostRecentMessages = 1;
    static $PostMessage = 2;
}

class AsyncRequestProcessor {

    static function ProcessRequest($action){
        global $mybb;

        switch($action){
            case AsyncRequestTypes::$LoadMostRecentMessages:
                return MessageLoader::LoadFromBeforeStartDate(new DateTime(), 20);
            case AsyncRequestTypes::$PostMessage:
                return MessagePoster::PostMessage($mybb->user, $_POST['badgerchat_message']);
            default:
                return "Unknown request type";
        }
    }
}

class MessageFactory {
    function __construct()
    {

    }
}