<?php

header('Content-Type: application/json;charset=utf-8');

include ("php_inc/inc_db_qp4.php");
   if ($conn !== FALSE) {
     $user_id = $_GET['id'];
     $table = "post";
     $sql = "SELECT u_id, p_id, avatar, thumbnail, title, photo_url, video_url, video_mp4, image, video, external, file_size, likes, comments, photo_cred, entry, created_at, user_name, display, mime_type FROM $table WHERE display = 'true' && u_id = $user_id";
     $object = array();
     $x = 0;
     foreach ($conn->query($sql) as $row) {
        $object[$x]['u_id'] = "{$row['u_id']}";
        $object[$x]['p_id'] = "{$row['p_id']}";
        $object[$x]['avatar'] = "{$row['avatar']}";
        $object[$x]['thumbnail'] = "{$row['thumbnail']}";
        $object[$x]['display'] = "{$row['display']}";
        $object[$x]['mime_type'] = "{$row['mime_type']}";
        $object[$x]['user_name'] = "{$row['user_name']}";
        $object[$x]['title'] = "{$row['title']}";
        $object[$x]['photo_url'] = "{$row['photo_url']}";
        $object[$x]['video_url'] = "{$row['video_url']}";
        $object[$x]['video_mp4'] = "{$row['video_mp4']}";
        $object[$x]['external_url'] = "{$row['external_url']}";
        $object[$x]['image'] = "{$row['image']}";
        $object[$x]['video'] = "{$row['video']}";
        $object[$x]['external'] = "{$row['external']}";
        $object[$x]['file_size'] = "{$row['file_size']}";
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
$conn = null;

?>


