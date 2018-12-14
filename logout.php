<?php

require_once("php_class/class_Session.php");

session_start();
$online_flag = new Session();
$online_flag->setUserID($_SESSION['user_id']);

$_SESSION = array();
session_destroy();

$online_flag->setOnlineFlag("false");

header('Location: index.php');

?>