<?php
header('Content-Type: application/json;charset=utf-8');

// Data sent from nodejs server uses Axios and Axios puts http post params in body
$body = file_get_contents('php://input');

// Parse payload body  
$json = json_decode($body, true);

// Save user ID to var
$message_id = $json['message_id'];

require('php_class/class_MessageThread.php');

$message_thread = new MessageThread();
$threads = $message_thread->retrieveThreads($message_id);


echo json_encode($threads);
?>