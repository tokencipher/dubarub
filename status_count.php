<?php
/**
 * Start the session.
 */
session_start();

header('Content-Type: application/json;charset=utf-8');
include ('php_inc/inc_db_qp4.php');
  if ($conn !== FALSE) {
    $lastId = 0;
	$table = "status";
	$user_id = $_SESSION['id'];

	$sql = "SELECT u_id, status_id, status_text, created_at FROM $table WHERE u_id = $user_id AND display = 'true'";
	$object = array();
	$x = 0;
	foreach ($conn->query($sql) as $row) {
  	  $object['u_id'] = "{$row['u_id']}";
  	  $object['status_id'] = "{$row['status_id']}";
  	  $object['status_text'] = "{$row['status_text']}";
  	  $object['created_at'] = "{$row['created_at']}";
  	  // $lastUpdate = "{$row['status_text']}";
  	  // $timestamp = "{$row['created_at']}";
  	  // Omit this line of code if we want to get the most recent status update.
  	  // ++$x; 
  	}
  }

// Send the data back to the caller
$myObj = json_encode($object);
echo $myObj
//$count = $conn->query("SELECT count(*) FROM $tableName WHERE u_id=1")->fetchColumn();
//echo $count;
// Send the data back to the caller
//$myObj = json_encode($object);

?>