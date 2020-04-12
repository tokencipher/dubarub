<?php 

class User {
  private $db;
  private $user_id;
  private $first_name;
  private $last_name;
  private $birth_date;
  private $age;
  private $user_name;
  private $avatar;
  private $email;
  private $password;
  private $bio;
  
  function __construct() {
    include ("php_inc/inc_db_qp4.php");
    if ($conn !== FALSE) {
      $this->db = $conn;
    }
  }
  
  public function createUser() {
    $table = "user";
    $sql = "INSERT INTO $table(user_name, email, password, avatar) VALUES(:user_name, :email, :password, :avatar)";
    
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':user_name', $this->user_name);
    $stmt->bindParam(':email', $this->email);
    $stmt->bindParam(':password', $this->password);
    $stmt->bindParam(':avatar', $this->avatar);
    
    return $stmt->execute();
  }
  
  public function setName($name) {
    $this->name = $name;
  }
  
  public function setAge($age) {
    $this->age = $age;
  }
  
  // Used for getting avatar
  public function setUserId($uid) {
    $this->user_id = $uid;
  }

  public function setUsername($uname) {
    $this->user_name = $uname;
  }
  
  public function setEmail($email) {
    $this->email = $email;
  }
  
  public function setPassword($password) {
    $this->password = $password;
  }
  
  public function setAvatar($path) {
    $this->avatar = $path;
  }
  
  public function setBio($bio) {
    $this->bio = $bio;
  }
  
  public function updateBio($user_id, $bio) {
    $table = "user";
    $sql = "UPDATE $table SET bio = :bio WHERE u_id = :user_id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':bio', $bio);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
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
    $sql = "SELECT avatar FROM $table WHERE u_id = $user_id";
    /*
    $sql = "SELECT avatar FROM $table WHERE u_id = :user_id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $avatar = $stmt->fetch();
    return $avatar;
    */
    
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

  public function retrieveUserID($user_name) {
    $table = "user";
    $sql = "SELECT u_id FROM $table WHERE user_name = '$user_name'";
    /*
    $sql = "SELECT u_id FROM $table WHERE user_name = :user_name";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':user_name', $user_name);
    $stmt->execute();
    $user_id = $stmt->fetch();
    return $user_id;
    */
    foreach ($this->db->query($sql) as $row) {
      $userID = "{$row['u_id']}";
    }
    return $userID;
    
  }
  
  public function getUsername($user_id) {
    $tableName = "user";
    $sql = "SELECT user_name FROM $tableName WHERE u_id=$user_id";
    foreach ($this->db->query($sql) as $row) {
      $user_name = "{$row['user_name']}";
    }
    return $user_name;
  }
  
  public function getFollowers($user_id) {
    $tableName = "followers";
    $sql = "SELECT follower, u_id_follower FROM $tableName WHERE u_id=$user_id";
    $followers = array();
    $x = 0;
    foreach ($this->db->query($sql) as $row) {
      $followers[$x]['follower'] = "{$row['follower']}"; 
      $followers[$x]['follower_id'] = "{$row['u_id_follower']}";
      ++$x;
    }
    return $followers;
  }
  
  public function getFollowing($user_id) {
    $tableName = "following";
    $sql = "SELECT following, u_id_following FROM $tableName WHERE u_id=$user_id";
    $following = array();
    $x = 0;
    foreach ($this->db->query($sql) as $row) {
      $following[$x]['following'] = "{$row['following']}";
      $following[$x]['following_id'] = "{$row['u_id_following']}";
      ++$x;
    }
    return $following;
  }
}
  
?>
