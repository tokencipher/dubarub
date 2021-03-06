<?php

/**
 * Start the session.
 */
session_start();

/**
 * Check if the user is logged in.
 */
 
if (!isset($_SESSION['user_id']) && !isset($_SESSION['logged_in'])) {
  // User not logged in. Redirect them back to the login.php page.
  header('Location: login.php');
  exit;
}
?>

<?php

header('Content-Type: application/json;charset=utf-8');

include ("php_inc/inc_db_qp4.php");
   if ($conn !== FALSE) {
     // $user_id = $_SESSION['user_id'];
     $table = "post";
     $sql = "SELECT u_id, p_id, title, photo_url, video_url, external_url, image, video, external, file_size, likes, comments, photo_cred, entry, created_at, user_name, mime_type, display FROM $table WHERE u_id=1";
     $object = array();
     $x = 0;
     foreach ($conn->query($sql) as $row) {
        $object[$x]['u_id'] = "{$row['u_id']}";
        $object[$x]['p_id'] = "{$row['p_id']}";
        $object[$x]['display'] = "{$row['display']}";
        $object[$x]['user_name'] = "{$row['user_name']}";
        $object[$x]['title'] = "{$row['title']}";
        $object[$x]['photo_url'] = "{$row['photo_url']}";
        $object[$x]['video_url'] = "{$row['video_url']}";
        $object[$x]['external_url'] = "{$row['external_url']}";
        $object[$x]['image'] = "{$row['image']}";
        $object[$x]['video'] = "{$row['video']}";
        $object[$x]['external'] = "{$row['external']}";
        $object[$x]['file_size'] = "{$row['file_size']}";
        $object[$x]['mime_type'] = "{$row['mime_type']}";
        $object[$x]['likes'] = "{$row['likes']}";
        $object[$x]['comments'] = "{$row['comments']}";
        $object[$x]['photo_cred'] = "{$row['photo_cred']}";
        $object[$x]['entry'] = "{$row['entry']}";
        $object[$x]['created_at'] = "{$row['created_at']}";
       //$lastUpdate = "{$row['status_text']}";
       // $timestamp = "{$row['created_at']}";
       // Omit this line of code if we want to get the most recent status update.
       ++$x; 
	 }
   }

$myObj = json_encode($object);
echo $myObj;

?>


