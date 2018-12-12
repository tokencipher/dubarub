<?php
/**
 * Start the session
 */
session_start();

header('Content-Type: application/json;charset=utf-8');
require_once('php_inc/inc_db_qp4.php');
  if ($conn !== FALSE) {
  
    $table = "post_comment";
    $user_id = $_SESSION['user_id'];
    // Retrieve the data
    $sql = "SELECT p_id, c_id, post_owner, user_name, avatar, timestamp, report, upvote, comment FROM $table WHERE display = 'true' && post_owner = $user_id";
    $object = array();
    $x = 0;
    foreach ($conn->query($sql) as $row) {
      $object[$x]['p_id'] = "{$row['p_id']}";
      $object[$x]['c_id'] = "{$row['c_id']}";
      $object[$x]['post_owner'] = "{$row['post_owner']}";
      $object[$x]['user_name'] = "{$row['user_name']}";
      $object[$x]['avatar'] = "{$row['avatar']}";
      $object[$x]['timestamp'] = "{$row['timestamp']}";
      $object[$x]['report'] = "{$row['report']}";
      $object[$x]['upvote'] = "{$row['upvote']}";
      $object[$x]['comment'] = "{$row['comment']}";
      ++$x; 
    }

  }

// Send the data back to the caller
$myObj = json_encode($object);
echo $myObj;
$conn = null;
?>