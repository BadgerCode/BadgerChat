<?php

$InstallDirectory = MYBB_ROOT . "inc/plugins/badgerchat/";

require_once($InstallDirectory . "models/Message.php");
require_once MYBB_ROOT . "inc/class_parser.php";

class MessagePosterResult {
    public $Status;
    public $Message;

    function __construct($status, $message)
    {
        $this->Status = $status;
        $this->Message = $message;
    }
}

class MessagePosterResultStatus {
    static $Success = 1;
    static $Unauthorised = 2;
    static $NoMessage = 3;
}

class MessagePoster
{
    // TODO: Return message object
    static function PostMessage($user, $ip, $message)
    {
        global $db;

        if ($user == null) {
            return new MessagePosterResult(MessagePosterResultStatus::$Unauthorised, null);
        }

        // TODO: Checks for null and empty string?
        if(empty($message)){
            return new MessagePosterResult(MessagePosterResultStatus::$NoMessage, null);
        }

        $escapedMessage = $db->escape_string($message);

        $parser = new postParser;
        $parser_options = array(
            'allow_mycode' => 1,
            'allow_smilies' => 1,
            'allow_imgcode' => 0,
            'allow_html' => 0,
            "allow_videocode" => 0
        );

        $now = date("Y-m-d H:i:s");

        // TODO: Strip HTML
        // TODO: Check for errors
        $addedId = $db->insert_query("badgerchat_messages",
            array(
                "SentAt" => $now,
                "uid" => $user['uid'],
                "Ip" => $ip,
                "Message" => $escapedMessage
            )
        );

        $addedMessage = new Message($addedId, $now, $user['username'], $ip, $message);

        return new MessagePosterResult(MessagePosterResultStatus::$Success,
                                       $addedMessage);
    }
}