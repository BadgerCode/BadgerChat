<?php

if(!defined("IN_MYBB")){
    die();
}

$plugins->add_hook("pre_output_page", "badgerchat_InsertIndexChatBox");
$plugins->add_hook("xmlhttp", "badgerchat_AsyncRequests");

function badgerchat_Templates()
{
    return array(
        "badgerchat_index_chatbox",
        "badgerchat_index_style"
    );
}

function badgerchat_DatabaseVersion()
{
    return 1;
}

function badgerchat_info(){
    return array(
        "name"          => "BadgerChat",
        "description"   => "A chat box plugin",
        "website"       => "https://github.com/BadgerCode/BadgerChat",
        "author"        => "Badger",
        "authorsite"    => "http://badgercode.co.uk",
        "version"       => "0.0.1",
        "guid"          => "",
        "compatibility" => "18*"
    );
}

function badgerchat_install()
{
    for($version = 1; $version <= badgerchat_DatabaseVersion(); $version++)
    {
        badgerchat_RunDBMigration("$version");
    }
}

function badgerchat_is_installed()
{
    global $db;
    return $db->table_exists("badgerchat_version")
           && badgerchat_GetCurrentDBVersion() == badgerchat_DatabaseVersion();
}

function badgerchat_uninstall()
{
    for($version = badgerchat_GetCurrentDBVersion(); $version > 0; $version--)
    {
        badgerchat_RunDBMigration("{$version}.down");
    }
}

function badgerchat_activate()
{
    foreach(badgerchat_Templates() AS $templateName) {
        badgerchat_AddTemplate($templateName);
    }
}

function badgerchat_deactivate()
{
    foreach(badgerchat_Templates() AS $templateName) {
        badgerchat_RemoveTemplate($templateName);
    }
}

function badgerchat_RunDBMigration($scriptNumber){
    global $db;
    $migrationQueries = explode(";", file_get_contents(MYBB_ROOT . "inc/plugins/badgerchat/migrations/{$scriptNumber}.sql"));

    foreach($migrationQueries AS $migrationQuery) {
        $trimmedQuery = trim($migrationQuery);
        if(empty($trimmedQuery)){
            continue;
        }
        $expandedQuery = str_replace("{MYBB_TABLE_PREFIX}", TABLE_PREFIX, $trimmedQuery);
        $db->write_query($expandedQuery);
    }
}

function badgerchat_GetCurrentDBVersion(){
    global $db;

    $query = $db->simple_select(
        "badgerchat_version",
        "Version",
        "",
        array("order_by" => "InstalledAt", "order_dir" => "DESC", "limit" => "1"));

    $currentVersion = $db->fetch_field($query, "Version");

    return is_numeric($currentVersion) ? intval($currentVersion) : -1;
}

function badgerchat_AddTemplate($templateName)
{
    global $db;

    $template = file_get_contents(MYBB_ROOT . "inc/plugins/badgerchat/templates/{$templateName}.html");
    $templateArray = array(
        "title"     => $templateName,
        "template"  => $db->escape_string($template),
        "sid"       => -1
    );
    $db->insert_query("templates", $templateArray);
}

function badgerchat_RemoveTemplate($templateName)
{
    global $db;
    $db->delete_query('templates', "title IN ('{$templateName}') AND SID=-1");
}

function badgerchat_InsertIndexChatBox($page)
{
    // TODO: Don't load messages until async request after page load
    global $templates;

    $chatBox = "";
    eval("\$chatBox = \"".$templates->get("badgerchat_index_style")."\";");
    eval("\$chatBox .= \"".$templates->get("badgerchat_index_chatbox")."\";");

    return str_replace("{badgerchat_index_chatbox}", $chatBox, $page);
}

function badgerchat_AsyncRequests()
{
    global $mybb, $charset;
    $result = "";

    if($mybb->input['action'] == "badgerchat"){
        switch($mybb->input['chatboxAction']){
            case "loadRecentMessages":
                $result = badgerchat_loadRecentMessages(20);
                break;
            case "addMessage":
                //TODO: Add message
                break;
        }
    }

    echo json_encode($result);
}

function badgerchat_loadRecentMessages($messageCount)
{
    global $db;
    $messages = array();

    $query = $db->simple_select(
        "badgerchat_messages",
        "`Id`, `SentAt`, `uid`, `Ip`, `Message`",
        "",
        array(
            "order_by"   => "SentAt",
            "order_dir"  => "DESC",
            "limit"      => $messageCount
        )
    );

    //TODO: Get back more info on the user (name styling)
    while($row = $db->fetch_array($query))
    {
        array_push($messages, new badgerchat_Message(
            $row["Id"],
            $row["SentAt"],
            $row["uid"],
            $row["Ip"],
            $row["Message"]
        ));
    }

    return $messages;
}

class badgerchat_Message
{
    public $Id;
    public $SentAt;
    public $User;
    public $Ip;
    private $Message;

    function __construct($Id, $SentAt, $User, $Ip, $Message)
    {
        $this->Id = $Id;
        $this->SentAt = $SentAt;
        $this->User = $User;
        $this->Ip = $Ip;
        $this->Message = $Message;
    }
}

class badgerchat_Row
{
    public $User;
    public $Message;

    function __construct($User, $Message)
    {
        $this->User = $User;
        $this->Message = $Message;
    }
}