<?php

if(!defined("IN_MYBB")){
    die();
}

$plugins->add_hook("pre_output_page", "badgerchat_InsertIndexChatBox");

function badgerchat_Templates()
{
    return array(
        "badgerchat_index_chatbox",
        "badgerchat_index_style",
        "badgerchat_index_chatbox_row"
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
    for($version = 0; $version <= badgerchat_DatabaseVersion(); $version++)
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

    $query = $db->simple_select("badgerchat_version",
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

    $chatBoxRows = badgerchat_MockGenerateHTMLRows(badgerchat_MockChatData());

    $chatBox = "";
    eval("\$chatBox = \"".$templates->get("badgerchat_index_style")."\";");
    eval("\$chatBox .= \"".$templates->get("badgerchat_index_chatbox")."\";");

    return str_replace("{badgerchat_index_chatbox}", $chatBox, $page);
}

function badgerchat_MockGenerateHTMLRow($row)
{
    global $templates;
    $name = $row->User;
    $message = $row->Message;

    $rowHTML = "";
    eval("\$rowHTML = \"" . $templates->get("badgerchat_index_chatbox_row") . "\";");
    return $rowHTML;
}

function badgerchat_MockGenerateHTMLRows($rows)
{
    $rowsHTML = "";

    foreach($rows as $row)
    {
        $rowsHTML .= badgerchat_MockGenerateHTMLRow($row);
    }

    return $rowsHTML;
}

function badgerchat_MockChatData(){
    return Array(
        new badgerchat_Row("Badger", "Hello world!"),
        new badgerchat_Row("Badger", "Message"),
        new badgerchat_Row("Badger", "Another message"),
        new badgerchat_Row("Badger", "Goodbye")
    );
}

class badgerchat_Row{
    public $User;
    public $Message;

    function __construct($User, $Message)
    {
        $this->User = $User;
        $this->Message = $Message;
    }
}