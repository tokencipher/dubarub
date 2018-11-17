<?php

header('Content-Type: application/json;charset=utf-8');
include('php_inc/inc_db_qp4.php');

if (isset($_POST['update_bio'])) {
  if (!empty($_POST['update_bio'])) {
    $bio = $_POST['update_bio'];
    $bio = addslashes($bio);
    $user_id = $_POST['u_id'];
    if ($conn !== FALSE) {
      $table = "user";
      $sql = "UPDATE $table SET bio = :bio WHERE u_id = :user_id";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':bio', $bio);
      $stmt->bindParam(':user_id', $user_id);
      $stmt->execute();
    }
  }
}

// Retrieve the data
$sql = "SELECT u_id, bio FROM $table WHERE u_id=$user_id";
$object = array(); 
$x = 0;
foreach ($conn->query($sql) as $row) {
  $object[$x]['u_id'] = "{$row['u_id']}";
  $object[$x]['bio'] = "{$row['bio']}";
}

// Send the data back to the caller
$myObj = json_encode($object);
echo $myObj;
$conn = null;

?>
