<?php
/**
 * Start the session
 */
session_start();

header('Content-Type: application/json;charset=utf-8');

include ("php_inc/inc_db_qp4.php");
   if ($conn !== FALSE) {
     $user_id = $_SESSION['id'];
     $table = "tags";
     $sql = "SELECT p_id, tag, timestamp FROM $table WHERE u_id = $user_id";
     $object = array();
     $x = 0;
     foreach ($conn->query($sql) as $row) {
       $object[$x]['tag'] = "{$row['tag']}";
       $object[$x]['p_id'] = "{$row['p_id']}";
       ++$x;
     }
   }

$myObj = json_encode($object);
echo $myObj;

?>