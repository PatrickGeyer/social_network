<?php

include_once('system.class.php');
include_once('phrase.class.php');
include_once('user.class.php');
include_once('entity.class.php');
include_once('files.class.php');
include_once ('facebook.class.php');
include_once('group.class.php');
include_once('notifications.class.php');
include_once('collaborator.class.php');
include_once('home.class.php');

$user = User::getInstance();
$entity = Entity::getInstance();
$base = new Base();
$files = Files::getInstance();
$group = Group::getInstance();
$system = System::getInstance();
$notification = Notification::getInstance();
$home = Home::getInstance();
$database_connection = Database::getConnection();
$phrase = Phrase::getInstance();
$facebook = new FB(array(
    'appId' => '219388501582266',
    'secret' => 'c1684eed82295d4f1683367dd8c9a849',
    'fileUpload' => false, // optional
    'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
));
