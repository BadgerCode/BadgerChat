<?php

class MessagePosterResult {
    static $Success = 1;
    static $Unauthorised = 2;
}

class MessagePoster
{
    static function PostMessage($postKey, $message)
    {
        if (!verify_post_check($postKey, true)) {
            return MessagePosterResult::$Unauthorised;
        }

        return MessagePosterResult::$Success;
    }
}