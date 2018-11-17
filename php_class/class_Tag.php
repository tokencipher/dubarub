<?php

/**
 * Tags class for adding and retrieving post related tags from MySQL
 */
 
class Tag {
  private $db;
  private $user_id;
  private $post_id;
  private $tags;
  private $timestamp;

  public function __construct() {
    include ("../php_inc/inc_db_qp4.php");
    if ($conn !== FALSE) {
      $this->db = $conn;
    }
  }
  
  public function __destruct() {
    $this->db = null;
  }
  
  // specify which data members of the class to serialize
  public function __sleep() {}
  
  // initialize any data members that were not saved with the serialization 
  public function __wakeup() {}
  
  public function dbClose() {
    $this->db = null;
  }
  
  public function getUserId() {
    return $this->user_id;
  }
  
  public function getPostId() {
    return $this->post_id;
  }
  
  public function getTags() {
    $table = "tags";
    $sql = "SELECT tag FROM $table;";
    $x = 0;
    $db_tags = array();
    foreach ($this->db->query($sql) as $row) {
      $db_tags[$x] = $row['tag'];
      ++$x;
    }
    return $db_tags;
  }
  
  public function getTimestamp() {} 

  public function setUserId($u_id) {
    $this->user_id = $u_id;
  }
  
  public function setPostId($p_id) {
    $this->post_id = $p_id;
  }
  
  public function setTags($tags) {
    $this->tags = $tags;
  }
  
  public function insertTags() {
    $table = "tags";
    foreach ($this->tags as $tag) {
      $sql = "INSERT INTO $table(u_id, p_id, tag) VALUES(:user, :post, :tag)";
      
      $stmt = $this->db->prepare($sql);
    
      $stmt->bindParam(':user', $this->user_id);
      $stmt->bindParam(':post', $this->post_id);
      $stmt->bindParam(':tag', $tag);
      $stmt->execute(); 
    }
    return;
  }


}
 
  
 
?>