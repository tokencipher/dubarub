<?php

class Follow {
  private $db;
  private $u_id;
  private $u_id_following;
  private $u_id_follower;
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
  
  public function follow($u_id, $user_name, $following, $u_id_following) {
    $table = "following";
    $sql = "INSERT INTO $table (u_id, user_name, following, u_id_following) VALUES (:u_id, :user_name, :following, :u_id_following)";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':u_id', $u_id);
    $stmt->bindParam(':user_name', $user_name);
    $stmt->bindParam(':following', $following);
    $stmt->bindParam(':u_id_following', $u_id_following);
    $stmt->execute();
  }
  
  public function unfollow($u_id, $u_id_following) {
    $table = "following";
    $sql = "DELETE FROM $table WHERE u_id = :u_id && u_id_following = :u_id_following";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':u_id', $u_id);
    $stmt->bindParam(':u_id_following', $u_id_following);
    $stmt->execute();
  }
  
  public function addFollower($u_id, $user_name, $follower, $u_id_follower) {
    $table = "followers";
    $sql = "INSERT INTO $table (u_id, user_name, follower, u_id_follower) VALUES (:u_id, :user_name, :follower, :u_id_follower)";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':u_id', $u_id);
    $stmt->bindParam(':user_name', $user_name);
    $stmt->bindParam(':follower', $follower);
    $stmt->bindParam(':u_id_follower', $u_id_follower);
    $stmt->execute();
  }
  
  public function removeFollower($u_id, $u_id_follower) {
    $table = "followers";
    $sql = "DELETE FROM $table WHERE u_id = :u_id && u_id_follower = :u_id_follower";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':u_id', $u_id);
    $stmt->bindParam(':u_id_follower', $u_id_follower);
    $stmt->execute();
  }
  
  public function getFollowerCount($u_id) {
    $table = "followers";
    $sql = "SELECT follower FROM $table WHERE u_id = $u_id;";
    
    $x = 0;
    foreach ($this->db->query($sql) as $row) {
      $object[$x]['follower'] = "{$row['follower']}";
      ++$x; 
	}
	return $x;
  }
  
  public function getFollowingCount($u_id) {
    $table = "following";
    $sql = "SELECT following FROM $table WHERE u_id = $u_id;";
    
    $x = 0;
    foreach ($this->db->query($sql) as $row) {
      $object[$x]['following'] = "{$row['following']}";
      ++$x; 
	}
	return $x;  
  }
  
  public function getFollowFlag($u_id, $u_id_following) {
    $table = "following";
    $sql = "SELECT user_name FROM $table WHERE u_id = :u_id && u_id_following = :u_id_following";
    
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':u_id', $u_id);
    $stmt->bindParam(':u_id_following', $u_id_following);
    $stmt->execute();
    if (!$stmt->rowCount() > 0) {
      return false;
    } else {
      return true;
    }
  }
  

}







?>