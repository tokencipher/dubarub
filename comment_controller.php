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

require_once('php_class/class_User.php');
require_once('php_class/class_Post.php');
require_once('php_class/class_PostComment.php');
require_once('php_inc/inc_db_qp4.php');


if (isset($_POST['comment_text'])) {  
  $comment = $_POST['comment_text'];
  $post_id = $_POST['post_id'];
  
  if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];  
    $post_owner = $_SESSION['id'];

    $user = new User();
    $user->setUserId($user_id);
    $avatar = $user->getAvatar();
    $user_name = $user->getUsername($user_id);

    $commentObj = new PostComment();
    $commentObj->createComment($user_id, $user_name, $post_id, $avatar, $comment, $post_owner);
  
    $post = new Post();
    $post->updateCommentCount($p_id);
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