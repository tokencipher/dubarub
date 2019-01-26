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
$user_id = $_SESSION['user_id'];

  if (isset($_POST['user_action'])) {
     
    switch ( $_POST['user_action'] ) {
    
      case 'Upvote Post':
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
        
        break;
     
      case 'Upvote Comment':
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
        
        break;
        
      case 'Flag Comment':
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
        
        break;
        
      case 'Remove Comment':
        $comment_id = $_POST['comment_id'];
        $commentObj = new PostComment();
        $commentObj->deleteComment($comment_id);
          
        $myObj = array();
        $myObj['isCommentRemoved'] = "true";
        
        break;
        
      case 'Remove Post':
        $post_id = $_POST['post_id'];
        $postObj = new Post();
        $postObj->deletePost($post_id);
          
        $myObj = array();
        $myObj['isPostRemoved'] = "true";
        
        break;
        
      case 'Remove Status':
        $status_id = $_POST['status_id'];
        $status = new Status();
        $status->deleteStatus($status_id);
          
        $myObj = array();
        $myObj['isStatusRemoved'] = "true";
        
        break;
        
      case 'Follow':
        $user_name = $_SESSION['user_name'];
        $following = $_SESSION['user'];
          
        $id = $_SESSION['id'];
        $user = $_SESSION['user'];
        $follower = $user_name;
          
          
        $followObj = new Follow();
        $follow_flag = $followObj->getFollowFlag($user_id, $user);
          
        if (!$follow_flag) {
          $followObj->follow($user_id, $user_name, $following);
          $followObj->addFollower($id, $user, $follower);
          $myObj = array();
          $myObj['nowFollowing'] = "true";
        } else {
          $myObj = array();
          $myObj['alreadyFollowing'] = "true";
        }
    
        break;
        
      default:
        echo "No action taken";
    }
  }   
  
$myObj = json_encode($myObj);
echo $myObj;

?>