<?php
 
class Postmaster {
  /**
    private $db;
    Mailbox fields 
	private $message_id; 
	private $sender_id; 
	private $avatar;
	private $sender;
	private $recipient;
	private $body;
	private $unread;
	private $created_at; 
	*/
	private $user_name; 
	
  
  function __construct() {
    require ("php_inc/inc_db_qp4.php");
    if ($conn !== FALSE) {
      $this->db = $conn;
    }
  }
  
  public function setUsername($user_name) {
    $this->user_name = $user_name;
  }
  
  public function createMailbox() {
    $table = $this->user_name . "_mailbox";
    $sql = "CREATE TABLE $table (
    message_id VARCHAR(255) PRIMARY KEY, 
    sender_id INT(10) UNSIGNED NOT NULL, 
    avatar VARCHAR(255) DEFAULT 'images/dubarub.jpg', 
    sender VARCHAR(70) NOT NULL, 
    recipient VARCHAR(70) NOT NULL, 
    body VARCHAR(2020) NOT NULL, 
    unread VARCHAR(5) DEFAULT 'true', 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    display VARCHAR(5) DEFAULT 'true', 
    FOREIGN KEY (sender_id) REFERENCES user(u_id));";
    
    $this->db->exec($sql);
           
  }
  
  public function deliverMessage($mailbox, $message_id, $sender_id, $avatar, $sender, $recipient, $body) {
    $table = $mailbox;
    $sql = "INSERT INTO $table (
      message_id, 
      sender_id, 
      avatar, 
      sender,
      recipient,
      body
    ) VALUES (
      :message_id, 
      :sender_id, 
      :avatar, 
      :sender,
      :recipient,
      :body)";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':message_id', $message_id);
    $stmt->bindParam(':sender_id', $sender_id);
    $stmt->bindParam(':avatar', $avatar);
    $stmt->bindParam(':sender', $sender);
    $stmt->bindParam(':recipient', $recipient);
    $stmt->bindParam(':body', $body);
    $stmt->execute();
  }
  
  public function retrieveMailbox($user_name) {
    $table = $user_name . "_mailbox";
    $sql = "SELECT message_id, sender_id, avatar, sender, recipient, body, unread, created_at FROM $table";
    $object = array();
    $x = 0;
    foreach ($this->db->query($sql) as $row) {
      $object[$x]['message_id'] = "{$row['message_id']}";
      $object[$x]['sender_id'] = "{$row['sender_id']}";
      $object[$x]['avatar'] = "{$row['avatar']}";
      $object[$x]['sender'] = "{$row['sender']}";
      $object[$x]['recipient'] = "{$row['recipient']}";
      $object[$x]['body'] = "{$row['body']}";
      $object[$x]['unread'] = "{$row['unread']}";
      $object[$x]['created_at'] = "{$row['created_at']}";
      ++$x;
    }
    return $object;
  }
  
}


?>