<?php

class MessagePosterResult {
    static $Success = 1;
    static $Unauthorised = 2;
    static $NoMessage = 3;
}

class MessagePoster
{
    // TODO: Return message object
    static function PostMessage($user, $message)
    {
        if ($user == null) {
            return MessagePosterResult::$Unauthorised;
        }

        // TODO: Checks for null and empty string?
        if(empty($message)){
            return MessagePosterResult::$NoMessage;
        }

        return MessagePosterResult::$Success;
    }
}