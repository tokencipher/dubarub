<?php

class PostComment {
  private $db;
  private $comment_id;
  private $post_id;
  private $post_owner;
  private $commenter;
  private $username;
  private $avatar;
  private $timestamp;
  private $comment;
  private $report;
  private $upvote;
  
  public function __construct() {
    include ("php_inc/inc_db_qp4.php");
    if ($conn !== FALSE) {
      $this->db = $conn;
    }
  }
  
  public function __destruct() {
    $this->db = null;
  }
  
  // specify which data members of the class to serialize
  public function __sleep() {}
  
  // initialize any data members that were not saved with the serialization process
  public function __wakeup() {}
  
  public function dbClose() {
    $this->db = null;
  }
  
  public function setPostID($p_id) {
    $this->post_id = $p_id;
  }
  
  public function setUserID($u_id) {
    $this->user_id = $u_id;
  }
  
  public function setAvatar($avatar) {
    $this->avatar = $avatar;
  }
  
  public function setComment($comment){
    $this->comment = $comment;
  }  
  
  public function getPostID() {}
  
  public function getUserID() {}
  
  public function getAvatar() {}
  
  public function getComment() {}
  
  public function report($c_id) {
    // Get report count so we can increment it and send to DB
    $count = getReport($c_id);
    
    // Increment retrieved upvote
    $count += 1;
  
    $table = "post_comment";
    $sql = "UPDATE $table SET report = :inc WHERE c_id = :c_id";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':inc', $count);
    $stmt->bindParam(':c_id', $c_id);
    $stmt->execute();
  }  
  
  public function upvote($c_id) {
    // Get upvote count so we can increment it and send to DB
    $count = getUpvote($c_id);
    
    // Increment retrieved upvote
    $count += 1;
  
    $table = "post_comment";
    $sql = "UPDATE $table SET upvote = :inc WHERE c_id = :c_id";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':inc', $count);
    $stmt->bindParam(':c_id', $c_id);
    return $stmt->execute();
    
  }
  
  public function setUpvoteFlag($u_id, $c_id, $flag) {  
    $table = "comment_upvote";
    $sql = "INSERT INTO $table (u_id, c_id, upvote) VALUES (:u_id, :c_id, :upvote)";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':u_id', $u_id);
    $stmt->bindParam(':c_id', $c_id);
    $stmt->bindParam(':upvote', $flag);
    $stmt->execute();
  }
  
  public function getUpvote($comment_id) {
    $table = "post_comment";
    $sql = "SELECT upvote FROM $table WHERE c_id = :c_id";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':c_id', $comment_id);
    return $stmt->execute();
  }
  
  public function getUpvoteFlag($user_id, $comment_id) {
    $table = "comment_upvote";
    $sql = "SELECT upvote FROM $table WHERE u_id = :u_id && c_id = :c_id";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':u_id', $user_id);
    $stmt->bindParam(':c_id', $comment_id);
    return $stmt->execute();
  }
  
  public function setReportFlag($u_id, $c_id, $flag) {  
    $table = "comment_reported";
    $sql = "INSERT INTO $table (u_id, c_id, report) VALUES (:u_id, :c_id, :report)";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':u_id', $u_id);
    $stmt->bindParam(':c_id', $c_id);
    $stmt->bindParam(':report', $flag);
    $stmt->execute();
  }
  
  public function getReportFlag($user_id, $comment_id) {
    $table = "comment_reported";
    $sql = "SELECT report FROM $table WHERE u_id = :u_id && c_id = :c_id";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':u_id', $user_id);
    $stmt->bindParam(':c_id', $comment_id);
    return $stmt->execute();
  }
  
  public function createComment($u_id, $user_name, $p_id, $avatar, $comment, $post_owner) {
    $table = "post_comment";
    $sql = "INSERT INTO $table (u_id, user_name, p_id, avatar, comment, post_owner) VALUES (:user_id, :user_name, :post_id, :avatar, :comment, :post_owner)";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':user_id', $u_id);
    $stmt->bindParam(':user_name', $user_name);
    $stmt->bindParam(':post_id', $p_id);
    $stmt->bindParam(':avatar', $avatar);
    $stmt->bindParam(':comment', $comment);
    $stmt->bindParam(':post_owner', $post_owner);
    $stmt->execute();
  }
  
  public function deleteComment($comment_id) {
    $table = "post_comment";
    $sql = "UPDATE $table SET display = 'false' WHERE c_id = :comment_id";
    
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':comment_id', $comment_id);
    
    $stmt->execute();
  } 
  
}

?>