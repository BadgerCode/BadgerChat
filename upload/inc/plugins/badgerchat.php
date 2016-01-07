<?php

if(!defined("IN_MYBB")){
    die();
}

class BadgerChatConfiguration {

    static $InstallDirectory = MYBB_ROOT . "inc/plugins/badgerchat/";
    static $DatabaseVersion = 1;
}

require_once(BadgerChatConfiguration::$InstallDirectory . "services/AsyncRequestProcessor.php");


$plugins->add_hook("pre_output_page", "badgerchat_InsertIndexChatBox");
$plugins->add_hook("xmlhttp", "badgerchat_AsyncRequests");

function badgerchat_Templates()
{
    return array(
        "badgerchat_index_chatbox",
        "badgerchat_index_style"
    );
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
    for($version = 1; $version <= BadgerChatConfiguration::$DatabaseVersion; $version++)
    {
        badgerchat_RunDBMigration("$version");
    }
}

function badgerchat_is_installed()
{
    global $db;
    return $db->table_exists("badgerchat_version")
           && badgerchat_GetCurrentDBVersion() == BadgerChatConfiguration::$DatabaseVersion;
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
    $migrationQueries = explode(";", file_get_contents(BadgerChatConfiguration::$InstallDirectory . "migrations/{$scriptNumber}.sql"));

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

    $template = file_get_contents(BadgerChatConfiguration::$InstallDirectory . "templates/{$templateName}.html");
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
    global $mybb;

    if($mybb->input['action'] == "badgerchat"){
        $result = AsyncRequestProcessor::ProcessRequest($mybb->input['chatboxRequestType']);
        echo json_encode($result);
        return;
    }
}