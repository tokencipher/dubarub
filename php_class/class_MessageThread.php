<?php

/**
 * Whenever you send a new message or reply to a message, that creates a message thread
 * A message thread consists of message_id (FOREIGN_KEY), 
 */
class MessageThread {
  private $thread_id;
  private $message_id;
  private $sender_id;
  private $recipient_id;
  private $avatar;
  private $sender;
  private $recipient;
  private $body;
  private $created_at;
 
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
 
  public function createThread($message_id, $sender_id, $recipient_id, $avatar, $sender, $recipient, $body) {
	$table = "thread";
	$sql = "INSERT INTO $table (message_id, sender_id, recipient_id, avatar, sender, recipient, body) VALUES (:message_id, :sender_id, :recipient_id, :avatar, :sender, :recipient, :body)";
	$stmt = $this->db->prepare($sql);

	$stmt->bindParam(':message_id', $message_id);
	$stmt->bindParam(':sender_id', $sender_id);
	$stmt->bindParam(':recipient_id', $recipient_id);
	$stmt->bindParam(':avatar', $avatar);
	$stmt->bindParam(':sender', $sender);
	$stmt->bindParam(':recipient', $recipient);
	$stmt->bindParam(':body', $body);
	$stmt->execute();
  }
}
?>
