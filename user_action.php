<?php
/**
 * Start the session
 */
session_start();

require_once('php_class/class_Post.php');
require_once('php_class/class_PostComment.php');
require_once('php_class/class_Status.php');
require_once('php_class/class_Follow.php');

header('Content-Type: application/json;charset=utf-8');

  if (isset($_POST['user_action'])) {
     
    switch ( $_POST['action'] ) {
    
      case 'Upvote Post':
        if (isset($_SESSION['user_id'])) {
          $user_id = $_SESSION['user_id'];
          $post_id = $_POST['post_id'];
          $postObj = new Post();
          $upvote_flag = $commentObj->getUpvoteFlag($u_id, $p_id);
        
          if ($upvote_flag == "true") {
            return;
          }
          
          $postObj->upvote($post_id);
          $postObj->setUpvoteFlag($user_id, $post_id, "true");
          
          $myObj = array();
          $trophy_count = $postObj->getUpvote($post_id);
          $myObj['trophy_count'] = $trophy_count;
        }
        
        break;
     
      case 'Upvote Comment':
        if (isset($_SESSION['user_id'])) {
          $user_id = $_SESSION['user_id'];
          $comment_id = $_POST['comment_id'];
          $commentObj = new PostComment();
          $upvote_flag = $commentObj->getUpvoteFlag($user_id, $comment_id);
        
          if ($upvote_flag == "true") {
            return;
          }
          
          $commentObj->upvote($comment_id);
          $commentObj->setUpvoteFlag($user_id, $comment_id, "true");
          
          $myObj = array();
          $trophy_count = $commentObj->getUpvote($comment_id);
          $myObj['trophy_count'] = $trophy_count;
        }
        
        break;
        
      case 'Flag Comment':
        if (isset($_SESSION['user_id'])) {
          $user_id = $_SESSION['user_id'];
          $comment_id = $_POST['comment_id'];
          $commentObj = new PostComment();
          $report_flag = $commentObj->getReportFlag($user_id, $comment_id);
          
          if ($report_flag == "true") {
            return;
          }
          
          $commentObj->report($comment_id);
          $commentObj->setReportFlag($user_id, $comment_id, "true");
          
          $myObj = array();
          $myObj['isCommentFlagged'] = "true";
        }
        break;
        
      case 'Remove Comment':
        if (isset($_SESSION['user_id'])) {
          $comment_id = $_POST['comment_id'];
          $commentObj = new PostComment();
          $commentObj->deleteComment($comment_id);
          
          $myObj = array();
          $myObj['isCommentRemoved'] = "true";
        }
        break;
        
      case 'Remove Post':
        if (isset($_SESSION['user_id'])) {
          $post_id = $_POST['post_id'];
          $postObj = new Post();
          $postObj->deletePost($post_id);
          
          $myObj = array();
          $myObj['isPostRemoved'] = "true";
        }
        break;
        
      case 'Remove Status':
        if (isset($_SESSION['user_id'])) {
          $stat_id = $_POST['stat_id'];
          $status = new Status();
          $status->deleteStatus($stat_id);
          
          $myObj = array();
          $myObj['isStatusRemoved'] = "true";
        }
        break;
        
      case 'Follow':
        if (isset($_SESSION['user_id'])) {
          $user_id = $_SESSION['user_id'];
          $user_name = $_SESSION['user_name'];
          $following = $_SESSION['user'];
          
          $followObj = new Follow();
          $followObj->follow($user_id, $user_name, $following);
          $followObj->addFollower($id, $user, $user_name);
          
          $myObj = array();
          $myObj['isFollowed'] = "true";
        }
        break;
    }
  }   
  
$myObj = json_encode($myObj);
echo $myObj;

?>