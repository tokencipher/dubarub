<?php
session_start();

header('Content-Type: application/json;charset=utf-8');

include ("php_inc/inc_db_qp4.php");
   if ($conn != FALSE) {
     $table = "user";
     $sql = "SELECT u_id, first_name, last_name, user_name, logged_in, registration_date, language, bio, avatar FROM $table";
     $object = array();
     $x = 0;
     foreach ($conn->query($sql) as $row) {
        $object[$x]['u_id'] = "{$row['u_id']}";
        $object[$x]['first_name'] = "{$row['first_name']}";
        $object[$x]['last_name'] = "{$row['last_name']}";
        $object[$x]['user_name'] = "{$row['user_name']}";
        $object[$x]['logged_in'] = "{$row['logged_in']}";
        $object[$x]['registration_date'] = "{$row['registration_date']}";
        $object[$x]['language'] = "{$row['language']}";
        $object[$x]['bio'] = "{$row['bio']}";
        $object[$x]['avatar'] = "{$row['avatar']}";
       ++$x; 
	 }
   }

$myObj = json_encode($object);
echo $myObj;
$conn = null;

?>