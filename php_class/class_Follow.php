<?php

class Follow {
  private $db;
  private $u_id;
  private $user_name;
  private $follower;
  private $following;
  
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
  
  public function follow($u_id, $user_name, $following) {
    $table = "following";
    $sql = "INSERT INTO $table (u_id, user_name, following) VALUES (:u_id, :user_name, :following)";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':u_id', $u_id);
    $stmt->bindParam(':user_name', $user_name);
    $stmt->bindParam(':following', $following);
    $stmt->execute();
  }
  
  public function unfollow($u_id, $following) {
    $table = "following";
    $sql = "DELETE FROM $table WHERE u_id = :u_id && following = :following";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':u_id', $u_id);
    $stmt->bindParam(':following', $following);
    $stmt->execute();
  }
  
  public function addFollower($u_id, $user_name, $follower) {
    $table = "followers";
    $sql = "INSERT INTO $table (u_id, user_name, follower) VALUES (:u_id, :user_name, :follower)";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':u_id', $u_id);
    $stmt->bindParam(':user_name', $user_name);
    $stmt->bindParam(':follower', $follower);
    $stmt->execute();
  }


}







?>