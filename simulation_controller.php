<?php

header('Content-Type: application/json;charset=utf-8');
include ('php_inc/inc_db_qp4.php');

if (isset($_POST['update'])) {
  if (!empty($_POST['update'])) {
    $status = $_POST['update'];
    $user_id = $_POST['u_id'];
    
    /* If you uncomment this section escaped status text will display to user from DB */
    //$status = addslashes($status);
    
    if ($conn !== FALSE) {
      $tableName = "status";
      $sql = "INSERT INTO $tableName (u_id, status_text) VALUES (:user_id, :status)";
      $stmt = $conn->prepare($sql);
    
      $stmt->bindParam(':user_id', $user_id);
      $stmt->bindParam(':status', $status);
      $stmt->execute();
    }
  }
}

// Retrieve the data
$sql = "SELECT u_id, status_id, status_text, created_at FROM $tableName WHERE display = 'true' && u_id = $user_id";
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

?>