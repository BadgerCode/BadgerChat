<?php

if(!defined("IN_MYBB")){
    // Not going to give a message (people could easily tell if a site is running this)
    die();
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

function badgerchat_install(){
    // TODO: Proper migration management
    global $db;
    $migrationQueries = explode(";", file_get_contents(MYBB_ROOT . "inc/plugins/badgerchat/migrations/1.sql"));
    foreach($migrationQueries AS $migrationQuery) {
        $trimmedQuery = trim($migrationQuery);
        if(empty($trimmedQuery)){
            continue;
        }

        $db->write_query($trimmedQuery);
    }
}

function badgerchat_is_installed(){
    // TODO: Proper migration management
    return badgerchat_GetCurrentDBVersion() == 1;
}

function badgerchat_uninstall(){

}

function badgerchat_activate(){

}

function badgerchat_deactivate(){

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