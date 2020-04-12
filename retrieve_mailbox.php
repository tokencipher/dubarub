<?php

// retrieve_mailbox.php

/**
 * Start the session.
 */
session_start();
header('Content-Type:application/json;charset=utf-8');

/**
* for a 30 minute timeout, specified in seconds
*/

/** 
* Here we look for the user's LAST_ACTIVITY timestamp. If 
* it's set and indicates our $timeout_duration has passed,
* end the previous session .
*/

ini_set( 'display_errors', 1 ); 
error_reporting( E_ALL );

/**
 * Check if the user is logged in.
 */
 
if (!isset($_SESSION['user_id']) && !isset($_SESSION['logged_in'])) {
  // User not logged in. Redirect them back to the login.php page.
  header('Location: index.php');
  exit;
}

$user_name = $_SESSION['user_name'];

require('php_class/class_Postmaster.php');
$post_master = new Postmaster();

$mailbox = $post_master->retrieveMailbox($user_name);

echo json_encode($mailbox);

?>
