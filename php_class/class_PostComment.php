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
  
  public function reportComment($c_id) {}  
  
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
  
}

?>