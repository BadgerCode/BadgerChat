<?php

if(!defined("IN_MYBB")){
    // Not going to give a message (people could easily tell if a site is running this)
    die();
}

function badgerchat_info(){
    return array(
        "name"          => "BadgerChat",
        "Description"   => "A chat box plugin",
        "website"       => "https://github.com/BadgerCode/BadgerChat",
        "author"        => "Badger",
        "authorsite"    => "http://badgercode.co.uk",
        "version"       => "0.0.1",
        "guid"          => "",
        "compatibility" => "18*"
    );
}

function badgerchat_install(){
    
}

function badgerchat_is_installed(){
    /*
     * global $db;
     * return $db->table_exists("tableName");
     */
}

function badgerchat_uninstall(){

}

function badgerchat_activate(){

}

function badgerchat_deactivate(){

}