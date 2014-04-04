<?php

require_once('system.class.php');
require_once('phrase.class.php');
require_once('user.class.php');
require_once('entity.class.php');
require_once('files.class.php');
require_once ('facebook.class.php');
require_once('calendar.class.php');
require_once('group.class.php');
require_once('notifications.class.php');
require_once('home.class.php');

Registry::setup();

class Registry {

    static $objects = array();

    static function getInstance() {
        if (is_null(self :: $registry)) {
            self :: $registry = new Registry();
        }
        return self :: $registry;
    }

    public static function setup() {
        self :: $objects['user'] = User::getInstance();
        self :: $objects['entity'] = Entity::getInstance();
        self :: $objects['calendar'] = Calendar::getInstance();
        self :: $objects['base'] = new Base();
        self :: $objects['files'] = Files::getInstance();
        self :: $objects['group'] = Group::getInstance();
        self :: $objects['system'] = System::getInstance();
        self :: $objects['phrase'] = Phrase::getInstance();
        self :: $objects['notification'] = Notification::getInstance();
        self :: $objects['home'] = Home::getInstance();
        self :: $objects['db'] = Database::getConnection();
        self :: $objects['facebook'] = new FB(array(
            'appId' => '219388501582266',
            'secret' => 'c1684eed82295d4f1683367dd8c9a849',
            'fileUpload' => false, // optional
            'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
        ));
    }

    public function __set($key, $value) {
        self :: $objects[$key] = $value;
    }
    
    public static function set($key, $value) {
        self :: $objects[$key] = $value;
    }

    public function __get($key) {
        return self :: $objects[$key];
    }
    
    public static function get($key) {
        return self :: $objects[$key];
    }

}
