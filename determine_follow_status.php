<?php session_start(); 
if (!isset($_SESSION['user_id']) && !isset($_SESSION['logged_in'])) {
  // User not logged in. Redirect them back to the login.php page.
  header('Location: index.php');
  exit;
}
?>
<?php require_once ('php_class/class_Follow.php'); ?>
<?php

  $loggedInUserId = $_POST['logged_in_user_id'];
  $renderedUserId = $_POST['rendered_user_id'];
  $avatar = $_POST['avatar'];
  $renderedUserName = $_POST['rendered_user_name'];
  $bio = $_POST['bio'];
  
  // Identify if user profile being viewed is being followed by current logged in user
  $followObj = new Follow();
  $following = $followObj->getFollowFlag($_SESSION['user_id'], $renderedUserId);
 
  $followStatus = array();
  $followStatus['follow_status'] = $following;
  $followStatus['rendered_user_id'] = $renderedUserId; 
  $followStatus['avatar'] = $avatar;
  $followStatus['rendered_user_name'] = $renderedUserName;
  $followStatus['bio'] = $bio;  

  echo json_encode($followStatus);
?>