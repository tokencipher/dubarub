<?php
  
class Session {
  private $db;
  private $u_id;
  private $user_name;
  private $logged_in;
  
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
  
  public function logUser($u_id, $user_name) {
    $table = "sessions";
    
    $sql = "INSERT INTO $table(u_id, user_name) VALUES(:user_id, :user_name)";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':user_id', $u_id);
    $stmt->bindParam(':user_name', $user_name);
    $stmt->execute(); 
  }
  
  public function setOnlineFlag($user_id, $flag) {
    $table = "sessions";
    
    $sql = "UPDATE $table SET online = :flag WHERE u_id = :user_id";
	$stmt = $this->db->prepare($sql);
	
	$stmt->bindParam(':user_id', $user_id);
	$stmt->bindParam(':flag', $flag);
	$stmt->execute();
  }
  
  public function getOnlineFlag($user_id) {
    $table = "sessions";
    
    $sql = "SELECT online FROM $table WHERE u_id = :user_id";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':user_id', $user_id);
    
    return $stmt->execute();
  }

  
  
}





?>