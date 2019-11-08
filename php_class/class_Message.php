<?php 

class Message {
  private $db;
  private $message_id; // PRIMARY KEY
  private $sender_id; // FOREIGN KEY 
  private $recipient_id; // FOREIGN KEY
  private $message; // VARCHAR(1300) NOT NULL;
  private $timestamp; // TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  private $opened;  // VARCHAR(5) DEFAULT 'false'
  private $display; // VARCHAR(5) DEFAULT 'true'


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
  
  public function getMessages($recipient_id) {
    $table = "message";
    $sql = "SELECT * FROM message WHERE u_id = $recipient_id;";
    
    $x = 0; 
    foreach($this->db->query($sql) as $row) {
      $object[$x]['message_id'] = "{$row['message_id']}";
      $object[$x]['sender_id'] = "{$row['sender_id']}";
      $object[$x]['message'] = "{$row['message']}";
      $object[$x]['timestamp'] = "{$row['timestamp']}";
      $object[$x]['opened'] = "{$row['opened']}";
      ++$x;
    }
    return $x;
    
  }
  
  public sendMessage($sender_id, $message) {
    $table = "message";
    $sql = "INSERT INTO $table (sender_id, message) VALUES (:sender_id, :message)";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':sender_id', $sender_id);
    $stmt->bindParam(':message', $message);
    $stmt->execute();
  }
  
  public deleteMessage($message_id, $recipient_id) {
    $table = "message";
    $sql = "UPDATE $table SET display = 'false' WHERE message_id = :message_id && recipient_id = :recipient_id";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':message_id', $message_id);
    $stmt->bindParam(':recipient_id', $recipient_id);
    $stmt->execute();
  }
  
  public setOpened($message_id) {
    $table = "message";
    $sql = "UPDATE $table SET opened = 'true' WHERE message_id = :message_id";
    
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':message_id', $message_id);
    $stmt->execute();
  }
  
}

?>