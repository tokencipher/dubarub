<?php

session_start();
header('Content-Type:application/json;charset=utf-8');

if (!isset($_SESSION['user_id']) && !isset($_SESSION['logged_in'])) {
  // User not logged in. Redirect them back to the login.php page.
  header('Location: index.php');
  exit;
}

ini_set( 'display_errors', 1 ); 
error_reporting( E_ALL );

require('php_class/class_Postmaster.php');

$user_name = $_SESSION['user_name'];

$mailbox = new Postmaster();
$unread_count = $mailbox->getUnreadCount($user_name);

echo json_encode(array("unreadCount"=>$unread_count));

?>