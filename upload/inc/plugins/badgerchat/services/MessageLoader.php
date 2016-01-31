<?php

if(!defined("IN_MYBB")){
    die();
}

$InstallDirectory = MYBB_ROOT . "inc/plugins/badgerchat/";

require_once($InstallDirectory . "models/Message.php");
require_once($InstallDirectory . "models/ChatBoxRow.php");
require_once MYBB_ROOT . "inc/class_parser.php";

class MessageLoader
{
    static function LoadFromBeforeStartDate(DateTime $startDate, $count){
        global $db, $parser;

        $messages = array();

        $formattedStartDate = $startDate->format("'Y-m-d H:i:s'");

        $query = $db->simple_select(
            "badgerchat_messages",
            "`Id`, `SentAt`, `uid`, `Ip`, `Message`",
            "`SentAt` < $formattedStartDate",
            array(
                "order_by"   => "SentAt",
                "order_dir"  => "ASC",
                "limit"      => $count
            )
        );

        $parser = new postParser;
        $parser_options = array(
            'allow_mycode' => 1,
            'allow_smilies' => 1,
            'allow_imgcode' => 0,
            'allow_html' => 0,
            "allow_videocode" => 0
        );

        //TODO: Get back more info on the user (name styling)
        while($row = $db->fetch_array($query))
        {
            $parsedMessage = $parser->parse_message($row['Message'], $parser_options);

            array_push($messages, new Message(
                $row["Id"],
                $row["SentAt"],
                $row["uid"],
                $row["Ip"],
                $parsedMessage
            ));
        }

        return $messages;
    }
}