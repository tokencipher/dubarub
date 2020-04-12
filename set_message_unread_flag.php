<?php

session_start();

if (!isset($_SESSION['user_id']) && !isset($_SESSION['logged_in'])) {
  // User not logged in. Redirect them back to the login.php page.
  header('Location: index.php');
  exit;
}

ini_set( 'display_errors', 1 ); 
error_reporting( E_ALL );

require('php_class/class_Postmaster.php');
$user_name = $_POST['user_name'];
$message_id = $_POST['message_id'];

$mailbox = new Postmaster();
$mailbox->setUnreadFlag($user_name, $message_id);

?>