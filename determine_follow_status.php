<?php require_once ('php_class/class_Follow.php'); ?>
<?php
  $loggedInUserId = $_POST['logged_in_user_id'];
  $renderedUserId = $_POST['rendered_user_id'];
  $avatar = $_POST['avatar'];
  $userName = $_POST['user_name'];
  $bio = $_POST['bio'];
  // Identify if user profile being viewed is being followed by current logged in user
  $followObj = new Follow();
  $following = $followObj->getFollowFlag($loggedInUserId, $renderedUserId);
 
  $followStatus = array();
  $followStatus['follow_status'] = $following;
  $followStatus['rendered_user_id'] = $renderedUserId; 
  $followStatus['avatar'] = $avatar;
  $followStatus['user_name'] = $userName;
  $followStatus['bio'] = $bio;  

  echo json_encode($followStatus);
?>