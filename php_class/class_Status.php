<?php

/** 
 * This class is meant to be open in the form handler file for status form
 */

class Status {
  private $db; 
  private $status_id;
  private $user_id;
  private $status_text;
  private $status_date;
  private $status_time;
  private $timestamp;
  
  public function __construct() {
    include ("php_inc/inc_db_qp4.php");
    if ($conn !== FALSE) {
      $this->db = $conn;
    }
  }
  
  // specify which data members of the class to serialize
  public function __sleep() {}
  
  // initialize any data members that were not saved with the serialization process
  public function __wakeup() {}
  
  public function dbClose() {
    $this->db = null;
  }

  public function deleteStatus($status_id) {
    $table = "status";
    $sql = "UPDATE $table SET display = 'false' WHERE status_id = :status_id";
    
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':status_id', $status_id);
    
    return $stmt->execute();
  } 

  public function setText() {}
    
  public function getText($user_id) {
    $tableName = "status";
    $sql = "SELECT status_text FROM $tableName WHERE display = 'true' && u_id = $user_id;";
    foreach ($this->db->query($sql) as $row) {
      $lastUpdate = "{$row['status_text']}";
    }
    if (!isset($lastUpdate)) {
      return '';
    } else {
      return $lastUpdate;
    }
  }
  
  public function getDate() {}
  
  public function getTime() {}
  
  public function getTimestamp($user_id) {
    $tableName = "status";
    $sql = "SELECT created_at FROM $tableName WHERE display = 'true' && u_id=$user_id;";
    foreach ($this->db->query($sql) as $row) {
      $timestamp = "{$row['created_at']}";
    }
    if (!isset($timestamp)) {
      return '';
    } else {
      return $timestamp;
    }
  }
  
  public function getUserId() {}

}

?>