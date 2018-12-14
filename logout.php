<?php

require_once("php_class/class_Session.php");

session_start();
$_SESSION = array();
session_destroy();

header('Location: index.php');

?>