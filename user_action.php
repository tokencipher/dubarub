<?php
/**
 * Start the session
 */
session_start();

header('Content-Type: application/json;charset=utf-8');

  if (isset($_POST['user_action'])) {
     
    switch ( $_POST['action'] ) {
     
      case 'Upvote':
        if (isset($_SESSION['user_id'])) {
          $u_id = $_SESSION['user_id'];
          $c_id = $_POST['comment_id'];
          $commentObj = new PostComment();
          $upvote_flag = $commentObj->getUpvoteFlag($u_id, $c_id);
        
          if ($upvote_flag == "true") {
            return;
          }
          
          $commentObj->upvote($c_id);
          $commentObj->setUpvoteFlag($u_id, $c_id, "true");
          
          $myObj = array();
          $trophy_count = $commentObj->getUpvote($c_id);
          $myObj['trophy_count'] = $trophy_count;
        }
        
        break;
        
      case 'Flag Comment':
        if (isset($_SESSION['user_id'])) {
          $u_id = $_SESSION['user_id'];
          $c_id = $_POST['comment_id'];
          $commentObj = new PostComment();
          $report_flag = $commentObj->getReportFlag($u_id, $c_id);
          
          if ($report_flag == "true") {
            return;
          }
          
          $commentObj->report($c_id);
          $commentObj->setReportFlag($u_id, $c_id, "true");
          
          $myObj = array();
          $myObj['isFlagged'] = "true";
        }
        break;
        
      case 'Delete Comment':
        $c_id = $_GET['commid'];
        $commentObj = new PostComment();
        $commentObj->deleteComment($c_id);
        break;
    }
  }   
  
$myObj = json_encode($myObj);
echo $myObj;

?>