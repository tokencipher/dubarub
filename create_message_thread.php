<?php
header('Content-Type: application/json;charset=utf-8');

/*
// Save user ID to var
$message_id = $_POST['message_id'];
$sender = $_POST['sender'];
$sender_id = $_POST['sender_id'];
$recipient = $_POST['recipient'];
$recipient_id = $_POST['recipient_id'];
$thread = $_POST['body'];
*/

// Data sent from nodejs server uses Axios and Axios puts http post params in body
$body = file_get_contents('php://input');

// Parse payload body  
$json = json_decode($body, true);

// Save user ID to var
$message_id = $json['message_id'];
$sender = $json['sender'];
$sender_id = $json['sender_id'];
$recipient = $json['recipient'];
$recipient_id = $json['recipient_id'];
$thread = $json['body'];

require('php_class/class_MessageThread.php');

$message_thread = new MessageThread();
$message_thread->createThread($message_id, $sender_id, $recipient_id, $sender, $recipient, $thread);


echo json_encode(array("status"=>"Created new thread for message id: " . $message_id));
?>