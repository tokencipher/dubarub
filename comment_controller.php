<?php

/**
 * Start the session
 */
session_start();

/**
 * Check if the user is logged in.
 */
if (!isset($_SESSION['user_id']) && !isset($_SESSION['logged_in'])) {
  // User is not logged in. Redirect them back to the login page.
  header('Location: index.php');
  exit;
}

header('Content-Type: application/json;charset=utf-8');

require_once('php_class/class_User.php');
require_once('php_class/class_Post.php');
require_once('php_class/class_PostComment.php');
require_once('php_inc/inc_db_qp4.php');


if (isset($_POST['comment_text'])) {  
  $comment = $_POST['comment_text'];
  $post_id = $_POST['post_id'];
  
  if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id']; 
    
    if ($_POST['post_owner'] == 'true') {
      // Owner of post is making a comment
      $post_owner = $user_id;
      $user = new User();
      $user->setUserId($user_id);
      $avatar = $user->getAvatar();
      
      // If the session variable of 'user_id' is set then so is the 'user_name' session var
      $user_name = $_SESSION['user_name'];
      
      // Prepare to persist comment to db 
      $commentObj = new PostComment();
      $lastId = $commentObj->createComment($user_id, $user_name, $post_id, $avatar, $comment, $post_owner);
      
      // Update comment count on post via post id
      $post = new Post();
      $post->updateCommentCount($post_id, "increment");
      
      $newCount = $post->getCommentCount($post_id);
      
      $object = array();
      $object['last_id'] = $lastId;
      $object['comment_count'] = $newCount;
      echo json_encode($object);
      
    } else {
      // A user has commented on someone else's post other than their own 
      if (isset($_SESSION['id'])) {
		$post_owner = $_SESSION['id'];
		$user = new User();
		$user->setUserId($user_id);
		$avatar = $user->getAvatar();
		$user_name = $_SESSION['user_name'];
		
		//$user_name = $user->getUsername($user_id);

		$commentObj = new PostComment();
		$lastId = $commentObj->createComment($user_id, $user_name, $post_id, $avatar, $comment, $post_owner);
  
		$post = new Post();
		$post->updateCommentCount($post_id, "increment");
		
		$newCount = $post->getCommentCount($post_id);
		
		$object = array();
		$object['last_id'] = $lastId;
		$object['comment_count'] = $newCount;
		$object['user_name'] = $user_name;
		$object['avatar'] = $avatar; 
		echo json_encode($object);
		
	  } else {
	    // Session variable has expired, return user to home.php 
	    header('Location: home.php');
	  }
    }
  }
}


/*
if ($conn !== FALSE) {
  $comment = $_POST['comment_text'];
  $post_id = $_POST['post_id'];
  $user_id = $_SESSION['user_id'];
  $post_owner = $_SESSION['id'];

  $user = new User();
  $user->setUserId($user_id);
  $avatar = $user->getAvatar();
  $user_name = $user->getUsername($user_id);

  $table = "post_comment";
  $sql = "INSERT INTO $table (u_id, user_name, p_id, avatar, comment, post_owner) VALUES (:user_id, :user_name, :post_id, :avatar, :comment, :post_owner)";
  $stmt = $conn->prepare($sql);

  $stmt->bindParam(':user_id', $user_id);
  $stmt->bindParam(':user_name', $user_name);
  $stmt->bindParam(':post_id', $post_id);
  $stmt->bindParam(':avatar', $avatar);
  $stmt->bindParam(':comment', $comment);
  $stmt->bindParam(':post_owner', $post_owner);
  $stmt->execute();
}
*/

?>