<?php
session_start();

header('Content-Type: application/json;charset=utf-8');

$loggedInUserId = $_SESSION['user_id'];

include ("php_inc/inc_db_qp4.php");
   if ($conn != FALSE) {
     $sql = "SELECT a.u_id, a.p_id, a.avatar, a.thumbnail, a.display, a.mime_type, a.user_name, a.title, a.photo_url, a.video_url, a.external_url, a.video_mp4, a.external, a.image, a.video, a.file_size, a.upvote, a.comments, a.photo_cred, a.entry, a.created_at from post a JOIN following b on a.u_id=b.u_id_following where b.u_id=$loggedInUserId AND a.display = 'TRUE';";
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
       $object[$x]['external'] = "{$row['external']}";
       $object[$x]['external_url'] = "{$row['external_url']}";
       $object[$x]['image'] = "{$row['image']}";
       $object[$x]['video'] = "{$row['video']}";
       $object[$x]['file_size'] = "{$row['file_size']}";
       $object[$x]['upvote'] = "{$row['upvote']}";
       $object[$x]['comments'] = "{$row['comments']}";
       $object[$x]['photo_cred'] = "{$row['photo_cred']}";
       $object[$x]['entry'] = "{$row['entry']}";
       $object[$x]['created_at'] = "{$row['created_at']}";
       ++$x;; 
	 }
   }

$myObj = json_encode($object);
echo $myObj;
$conn = null;

?>