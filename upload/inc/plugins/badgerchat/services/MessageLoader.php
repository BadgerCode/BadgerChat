<?php

if(!defined("IN_MYBB")){
    die();
}

$InstallDirectory = MYBB_ROOT . "inc/plugins/badgerchat/";

require_once($InstallDirectory . "models/Message.php");
require_once($InstallDirectory . "models/ChatBoxRow.php");

class MessageLoader
{
    static function LoadFromBeforeStartDate(DateTime $startDate, $count){
        global $db;

        $messages = array();

        $formattedStartDate = $startDate->format("'Y-m-d H:i:s'");

        $query = $db->simple_select(
            "badgerchat_messages",
            "`Id`, `SentAt`, `uid`, `Ip`, `Message`",
            "`SentAt` < $formattedStartDate",
            array(
                "order_by"   => "SentAt",
                "order_dir"  => "DESC",
                "limit"      => $count
            )
        );

        //TODO: Get back more info on the user (name styling)
        while($row = $db->fetch_array($query))
        {
            array_push($messages, new Message(
                $row["Id"],
                $row["SentAt"],
                $row["uid"],
                $row["Ip"],
                $row["Message"]
            ));
        }

        return $messages;
    }
}