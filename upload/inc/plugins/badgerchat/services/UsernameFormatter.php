<?php
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 04/02/2016
 * Time: 22:07
 */
class UsernameFormatter
{
    static function Format($uid, $username, $userGroup, $displayGroup)
    {
        global $mybb;
        $formattedName = format_name($username, $userGroup, $displayGroup);
        $profileUrl = $mybb->settings['bburl'] . "/member.php?action=profile&uid=" . $uid;
        return "<a href=\"{$profileUrl}\">{$formattedName}</a>";
    }
}