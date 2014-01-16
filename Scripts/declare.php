<?php

include_once('config.php');
include_once('system.class.php');
include_once('phrase.class.php');
include_once('user.class.php');
include_once('entity.class.php');
include_once('files.class.php');
include_once('group.class.php');
include_once('community.class.php');
include_once('notifications.class.php');
include_once('collaborator.class.php');
include_once('home.class.php');

$user = User::getInstance();
$entity = Entity::getInstance();
$files = Files::getInstance();
$group = Group::getInstance();
$community = Community::getInstance();
$system = System::getInstance();
$notification = Notification::getInstance();
$collaborator = new Collaborator;
$home = Home::getInstance();
$database_connection = Database::getConnection();
$phrase = Phrase::getInstance();