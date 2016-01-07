<?php

if(!defined("IN_MYBB")){
    die();
}

$InstallDirectory = MYBB_ROOT . "inc/plugins/badgerchat/";

require_once($InstallDirectory . "services/MessageLoader.php");

class AsyncRequestTypes{
    static $LoadMostRecentShouts = 1;
}

class AsyncRequestProcessor {

    static function ProcessRequest($action){
        switch($action){
            case AsyncRequestTypes::$LoadMostRecentShouts:
                return MessageLoader::LoadFromBeforeStartDate(new DateTime(), 20);
                break;
            default:
                return "Unknown request type";
        }
    }
}