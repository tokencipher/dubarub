<?php
/**
 * Start the session.
 */
session_start();
header('Content-Type: application/json;charset=utf-8');
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache'); // recommended to prevent caching of event data
include ('php_inc/inc_db_qp4.php');

$lastId = 0;

$tableName = 'status';
$id = $_SESSION['id'];
  
// Retrieve the data
$sql = "SELECT u_id, status_id, status_text, created_at FROM $tableName WHERE u_id = $id && display = 'true' ";
  
$object = array();
$x = 0;
foreach ($conn->query($sql) as $row) {
  $object['u_id'] = "{$row['u_id']}";
  $object['status_id'] = "{$row['status_id']}";
  $object['status_text'] = "{$row['status_text']}";
  $object['created_at'] = "{$row['created_at']}";
  // $lastUpdate = "{$row['status_text']}";
  // $timestamp = "{$row['created_at']}";
  // Omit this line of code if we want to get the most recent status update.
  // ++$x; 
}

// Send the data back to the caller
$myObj = json_encode($object);
  


//$select = $conn->query($sql);
//$select->execute();
//$count = $select->rowCount();
//echo ('Rows retrieved: ' . $count);

/** 
 * Constructs the SSE data format and flushes that data to the client.
 * 
 * @param string $msg Line of text that should be transmitted.
 */
function sendMsg($id, $msg) {
  /*
  echo "id: $id" . PHP_EOL;
  echo "data: {\n";
  echo "data: \"msg\": \"$msg\", \n";
  echo "data: \"id\": $id\n";
  echo "data: }\n";
  */
  echo "id: $id" . PHP_EOL;
  echo "data: $msg" . PHP_EOL;
  echo PHP_EOL;
  ob_flush();
  flush();
}

$startedAt = time();

do {
  // Cap connections at 10 seconds. The browser will reopen the connection on close
  
  
  if ((time() - $startedAt) > 10) {
    die();
  }
  
  
  sendMsg($startedAt, $myObj);
  sleep(5);
  
  // If we didn't use a while loop, the browser would essentially do polling 
  // every ~3seconds. Using the while, we keep the connection open and only make 
  // one request.
} while(true);

/*
event: message\n
data: {\n
data: "msg": ""
data: }\n\n
//echo $myObj;
//$conn = null;
*/

?>