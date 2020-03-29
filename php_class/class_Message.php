<?php 

class Message {
  private $db;
  private $message_id; // PRIMARY KEY
  private $sender_id; // FOREIGN KEY 
  private $avatar; // VARCHAR(255)
  private $recipient; // VARCHAR(70) 
  private $body; // VARCHAR(2020)
  private $unread; // VARCHAR(5)
  private $created_at; // TIMESTAMP DEFAULT CURRENT_TIMESTAMP

  public function __construct() {
    include("php_inc/inc_db_qp4.php");
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
  
  public function createMessage($message_id, $sender_id, $sender_avatar, $sender_username, $recipient, $message) {
    $table = "message";
    $sql = "INSERT INTO $table (message_id, sender_id, avatar, sender, recipient, body) VALUES (:message_id, :sender_id, :sender_avatar, :sender_username, :recipient, :message)";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':message_id', $message_id);
    $stmt->bindParam(':sender_id', $sender_id);
    $stmt->bindParam(':sender_avatar', $sender_avatar);
    $stmt->bindParam(':sender_username', $sender_username);
    $stmt->bindParam(':recipient', $recipient);
    $stmt->bindParam(':message', $message);
    $stmt->execute();
  }
  
  public function deleteMessage($message_id, $mailbox) {
    $table = $mailbox;
    $sql = "DELETE FROM $table WHERE message_id = :message_id";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':message_id', $message_id);
    $stmt->execute();
  }
  
  public function setOpened($message_id, $mailbox) {
    $table = $mailbox;
    $sql = "UPDATE $table SET unread = 'false' WHERE message_id = :message_id";
    
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':message_id', $message_id);
    $stmt->execute();
  }
  
}

?>
