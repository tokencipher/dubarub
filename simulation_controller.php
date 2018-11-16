<?php
/**
 * Start the session.
 */
session_start();
header('Content-Type: application/json;charset=utf-8');
include ('php_inc/inc_db_qp4.php');

if (isset($_POST['update'])) {
  if (!empty($_POST['update'])) {
    $verb = $_POST['update'];
    $user_id = $_SESSION['user_id'];
    $verb = addslashes($verb);
    if ($conn !== FALSE) {
      $tableName = "status";
      $sql = "INSERT INTO $tableName (u_id, status_text) VALUES ('$user_id', '$verb')";
      $conn->query($sql);
    }
  }
}

// Retrieve the data
$sql = "SELECT u_id, status_id, status_text, created_at FROM $tableName WHERE u_id=$user_id AND display!='false'";
$object = array();
$x = 0;
foreach ($conn->query($sql) as $row) {
  $object[$x]['u_id'] = "{$row['u_id']}";
  $object[$x]['status_id'] = "{$row['status_id']}";
  $object[$x]['status_text'] = "{$row['status_text']}";
  $object[$x]['created_at'] = "{$row['created_at']}";
  //$lastUpdate = "{$row['status_text']}";
  // $timestamp = "{$row['created_at']}";
  // Omit this line of code if we want to get the most recent status update.
  // ++$x; 
}

// Send the data back to the caller
$myObj = json_encode($object);
echo $myObj;
//$conn = null;


?>