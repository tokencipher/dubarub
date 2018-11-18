<?php
session_start();
header('Content-Type: application/json;charset=utf-8');

include ("php_inc/inc_db_qp4.php");
   if ($conn !== FALSE) {
     $table = "status";
     $user_id = $_SESSION['user_id'];
     $sql = "SELECT status_id, status_text, created_at, display FROM $table WHERE display = 'true' && u_id = $user_id";
     $object = array();
     $x = 0;
     foreach ($conn->query($sql) as $row) {
       $object[$x]['status_id'] = "{$row['status_id']}";
       $object[$x]['status_text'] = "{$row['status_text']}";
       $object[$x]['created_at'] = "{$row['created_at']}";
       $object[$x]['display'] = "{$row['display']}";
       
       ++$x;
     }
   }

$myObj = json_encode($object);
echo $myObj;
$conn = null;

?>