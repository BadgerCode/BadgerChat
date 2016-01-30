<?php

class MessagePosterResult {
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
            return MessagePosterResult::$Unauthorised;
        }

        // TODO: Checks for null and empty string?
        if(empty($message)){
            return MessagePosterResult::$NoMessage;
        }

        $escapedMessage = $db->escape_string($message);

        // TODO: Escape message
        // TODO: Check for errors
        $db->insert_query("badgerchat_messages",
            array(
                "uid" => $user['uid'],
                "Ip" => $ip,
                "Message" => $escapedMessage
            )
        );

        return MessagePosterResult::$Success;
    }
}