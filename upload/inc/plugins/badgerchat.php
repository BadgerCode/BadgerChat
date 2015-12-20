<?php

if(!defined("IN_MYBB")){
    die();
}

$templates = array(
  "badgerchat_index_chatbox"
);
$databaseVersion = 1;

$plugins->add_hook("pre_output_page", "badgerchat_Insert_Index_ChatBox");

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
    global $databaseVersion;
    for($version = 1; $version <= $databaseVersion; $version++)
    {
        badgerchat_RunDBMigration($version);
    }
}

function badgerchat_is_installed()
{
    global $db, $databaseVersion;
    return $db->table_exists("badgerchat_version")
           && badgerchat_GetCurrentDBVersion() == $databaseVersion;
}

function badgerchat_uninstall()
{
    for($version = badgerchat_GetCurrentDBVersion(); $version > 0; $version--)
    {
        badgerchat_RunDBMigration($version);
    }
}

function badgerchat_activate()
{
    global $templates;
    foreach ($templates as $templateName) {
        badgerchat_AddTemplate($templateName);
    }
}

function badgerchat_deactivate(){
    global $templates;
    foreach ($templates as $templateName) {
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

function badgerchat_Insert_Index_ChatBox($page)
{
    global $templates;

    $chatBox = $templates->get("badgerchat_index_chatbox");
    return str_replace("{badgerchat_index_chatbox}", $chatBox, $page);
}
