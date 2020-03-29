<?php
header('Content-Type: application/json;charset=utf-8');

// Data sent from nodejs server uses Axios and Axios puts http post params in body 
$body = file_get_contents('php://input');

// Parse payload body into json
$json = json_decode($body, true);

// Save message details to vars 
$message_id = $json['message_id'];
$sender = $json['sender'];
$recipient = $json['recipient'];
$message = $json['body'];

// Sanitize message
$message = stripslashes($message);
$message = htmlspecialchars($message);

require('php_class/class_User.php');
require('php_class/class_Message.php');
require('php_class/class_Postmaster.php');
require('php_class/class_MessageThread.php');

// Retrieve sender user ID to complete message details
$user = new User();
$sender_id = $user->retrieveUserID($sender);

// Retrieve sender avatar to complete message details
$user->setUserId($sender_id);
$avatar = $user->getAvatar();

// Persist message details to db 
$message_object = new Message();
$message_object->createMessage($message_id, $sender_id, $avatar, $sender, $recipient, $message);

// Persist initial message thread to db 
$user = new User();
$recipient_id = $user->retrieveUserID($recipient);
$messageThread = new MessageThread();
$messageThread->createThread($message_id, $sender_id, $recipient_id, $avatar, $sender, $recipient, $message);

$recipient_mailbox = $recipient . '_mailbox';
$post_master = new Postmaster();
$post_master->deliverMessage($recipient_mailbox, $message_id, $sender_id, $avatar, $sender, $recipient, $message);

$sender_mailbox = $sender . '_mailbox';
$post_master->deliverMessage($sender_mailbox, $message_id, $sender_id, $avatar, $sender, $recipient, $message);

$myObj = array(
  "messageID"=>$message_id, 
  "senderID"=>$sender_id, 
  "recipientID"=>$recipient_id,
  "avatar"=>$avatar, 
  "sender"=>$sender,
  "senderMailbox"=>$sender_mailbox,
  "recipient"=>$recipient,
  "recipientMailbox"=>$recipient_mailbox, 
  "message"=>$message
);


echo json_encode($myObj);
?>
