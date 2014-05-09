<?php
class Registry {

    static $objects = array();

    public static function setup() {
        if (!class_exists('System')) {
            require 'system.class.php';
        }
        if (!class_exists('App')) {
            require 'app.class.php';
        }
        if (!class_exists('Phrase')) {
            require 'phrase.class.php';
        }
        if (!class_exists('User')) {
            require 'user.class.php';
        }
        if (!class_exists('Entity')) {
            require 'entity.class.php';
        }
        if (!class_exists('Files')) {
            require 'files.class.php';
        }
        if (!class_exists('FB')) {
            require 'facebook.class.php';
        }
        if (!class_exists('Calendar')) {
            require 'calendar.class.php';
        }
        if (!class_exists('Group')) {
            require 'group.class.php';
        }
        if (!class_exists('Notification')) {
            require 'notifications.class.php';
        }
        if (!class_exists('Home')) {
            require 'home.class.php';
        }
        self :: $objects['user'] = User::getInstance();
        self :: $objects['app'] = new App();
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
        Registry::setup();
        return self :: $objects[$key];
    }

}
Registry::setup();