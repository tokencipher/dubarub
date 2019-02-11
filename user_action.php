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

// The user id of currently logged in user
$user_id = $_SESSION['user_id'];

// The user name of currently logged in user
$user_name = $_SESSION['user_name'];

// The user id of user whose profile is currently being viewed
$id = $_SESSION['id'];

// The user name of user whose profile is currently being viewed
$user = $_SESSION['user'];

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
          $followers = $followObj->getFollowerCount($_SESSION['id']);
          $myObj = array();
          $myObj['nowFollowing'] = "true";
          $myObj['followerCount'] = $followers;
        } else {
          $myObj = array();
          $myObj['alreadyFollowing'] = "true";
        }
        break;
        
      case 'Unfollow':
        // Used to specify the name of user profile to be unfollowed 
        $following = $user;
        
        // Used to specify the name of user who is following current user profile viewed
        $follower = $user_name;
        
        // Instansiate Follow object
        $followObj = new Follow();
        
        // Determine if user profile viewed is being followed by current user
        $follow_flag = $followObj->getFollowFlag($user_id, $following);
        
        // If user profile currently viewed is being followed by current user 
        // then unfollow user at logged in users' request
        if ($follow_flag) {
        
          // Implement unfollow function from Follow class by passing in the 
          // user_id of the currently logged in user and the name of the user
          // wished to be unfollowed. These two values act as a distinct key.
          $followObj->unfollow($user_id, $following);
          
          // Implement removeFollower function from Follow class by passing in 
          // the user id of the profile being viewed -- identified as a php session
          // variable (id) and the user name of the currently logged in user.
          // These two values act a distinct key. 
          $followObj->removeFollower($id, $follower);
          
          // Get updated follower count of user profile being viewed
          $followers = $followObj->getFollowerCount($id);
          
          // Create user array to hold pertinent details about unfollow transaction
          $myObj = array();
          
          // nowUnfollowing key holds true/false string to confirm success of unfollow transaction
          $myObj['nowUnfollowing'] = "true";
          
          // followerCount key holds updated follower count
          $myObj['followerCount'] = $followers;
        } else {
        
          // Create user array to hold pertinent details about unfollow transaction
          $myObj = array();
          
          // If the follow flag is equal to false then user profile viewed is not
          // followed by current logged in user 
          $myObj['alreadyUnfollowed'] = "true";
        }
        break;
        
      default:
        echo "No action taken";
    }
  }   
  
$myObj = json_encode($myObj);
echo $myObj;

?>