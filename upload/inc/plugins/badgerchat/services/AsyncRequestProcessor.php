<?php

if(!defined("IN_MYBB")){
    die();
}

$InstallDirectory = MYBB_ROOT . "inc/plugins/badgerchat/";

require_once($InstallDirectory . "services/MessageLoader.php");

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
                $message = null;
                return MessagePoster::PostMessage($mybb->get_input('my_post_key'), $message);
            default:
                return "Unknown request type";
        }
    }
}
