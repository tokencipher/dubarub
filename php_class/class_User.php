<?php 

class User {
  private $db;
  private $name;
  private $age;
  private $user_id;
  private $username;
  private $avatar;
  private $bio;
  
  function __construct() {
    include ("php_inc/inc_db_qp4.php");
    if ($conn !== FALSE) {
      $this->db = $conn;
    }
  }
  
  public function setName($name) {
    $this->name = $name;
  }
  
  public function setAge($age) {
    $this->age = $age;
  }
  
  public function setUserId($uid) {
    $this->user_id = $uid;
  }

  public function setUsername($uname) {
    $this->username = $uname;
  }
  
  public function setAvatar($path) {
    $this->avatar = $path;
  }
  
  public function updateAvatar() {
    $table = "user";
    $user_id = $this->user_id;
    $sql = "UPDATE $table SET avatar = :avatar WHERE u_id = :user_id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':avatar', $this->avatar);
    $stmt->bindParam(':user_id', $this->user_id);
    $stmt->execute();
  }
  
  public function setBio($bio) {
    $this->bio = $bio;
  }
  
  public function getBio() {
    $tableName = "user";
    $user_id = $this->user_id;
    $sql = "SELECT bio FROM $tableName WHERE u_id=$user_id";
    foreach ($this->db->query($sql) as $row) {
      $bio = "{$row['bio']}";
    }
    
    if (!isset($bio)) {
      return '';
    } else {
      return $bio;
    }
    
  }
  
  public function getAvatar() {
    $table = "user";
    $user_id = $this->user_id; 
    $sql = "SELECT avatar FROM $table WHERE u_id = $user_id;";
    foreach ($this->db->query($sql) as $row) {
      $avatar = "{$row['avatar']}";
    }
    return $avatar;
  } 
  
  public function getName() {
    return $this->name;
  }
  
  public function getAge() {
    return $this->age;
  }
  
  public function getUserId() {
    return $this->user_id;
  }
  
  public function getUsername($user_id) {
    $tableName = "user";
    $sql = "SELECT user_name FROM $tableName WHERE u_id=$user_id";
    foreach ($this->db->query($sql) as $row) {
      $user_name = "{$row['user_name']}";
    }
    return $user_name;
  }
  
  
}
  
?>