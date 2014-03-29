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
require_once('collaborator.class.php');
require_once('home.class.php');

$user = User::getInstance($args = array());
$entity = Entity::getInstance($args = array());
$calendar = Calendar::getInstance($args = array());
$base = new Base();
$files = Files::getInstance($args = array());
$group = Group::getInstance($args = array());
$system = System::getInstance($args = array());
$phrase = Phrase::getInstance($args = array());

$notification = Notification::getInstance(array(
    'user' => $user,
    'group' => $group,
    'phrase' => $phrase
));
$home = Home::getInstance($args = array());
$database_connection = Database::getConnection();
$facebook = new FB(array(
    'appId' => '219388501582266',
    'secret' => 'c1684eed82295d4f1683367dd8c9a849',
    'fileUpload' => false, // optional
    'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
));
