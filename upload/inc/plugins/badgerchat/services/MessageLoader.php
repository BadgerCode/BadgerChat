<?php

if(!defined("IN_MYBB")){
    die();
}

$InstallDirectory = MYBB_ROOT . "inc/plugins/badgerchat/";

require_once($InstallDirectory . "services/UsernameFormatter.php");
require_once($InstallDirectory . "models/Message.php");
require_once($InstallDirectory . "models/ChatBoxRow.php");
require_once MYBB_ROOT . "inc/class_parser.php";

class MessageLoader
{
    static function LoadFromBeforeStartDate(DateTime $startDate, $count){
        global $db, $parser, $mybb;

        $messages = array();

        $formattedStartDate = $startDate->format("'Y-m-d H:i:s'");

        $TABLE_PREFIX = TABLE_PREFIX;
        $query = $db->query("SELECT * FROM(
                                SELECT m.`Id`, m.`SentAt`, m.`uid`, m.`Ip`, m.`Message`,
                                    u.`username`, u.`usergroup`, u.`displaygroup`
                                FROM {$TABLE_PREFIX}badgerchat_messages m
                                INNER JOIN {$TABLE_PREFIX}users u ON u.uid = m.uid
                                WHERE m.`SentAt` < {$formattedStartDate}
                                ORDER BY m.`SentAt` DESC LIMIT {$count}
                             ) messages
                             ORDER BY SentAt ASC"
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

            $displayName = UsernameFormatter::Format($row['uid'], $row['username'], $row['usergroup'], $row['displaygroup']);

            array_push($messages, new Message(
                $row["Id"],
                $row["SentAt"],
                $displayName,
                $row["Ip"],
                $parsedMessage
            ));
        }

        return $messages;
    }
}