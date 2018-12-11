<?php
/**
 * Start the session.
 */ 
session_start();

header('Content-Type: application/json;charset=utf-8');
include('php_inc/inc_db_qp4.php');
  if ($conn != FALSE) {

    // Retrieve the data
    $table = "user";
	$user_id = $_SESSION['id'];
	$sql = "SELECT avatar FROM $table WHERE u_id = $user_id";
	$object = array();
	$x = 0;
	foreach ($conn->query($sql) as $row) {
  	  $object[$x]['avatar'] = "{$row['avatar']}";
	}
  }

// Send the data back to the caller
$myObj = json_encode($object);
echo $myObj;
$conn = null;

?>