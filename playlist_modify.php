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
  header('Location: index.php');
  exit;
}
?>

<?php

$error_count = 0;
$flag = false;

//header('Content-Type: application/json;charset=utf-8');

require ("php_inc/inc_db_qp4.php");
require ("php_class/class_Track.php");
require ("php_class/class_Playlist.php");
if ($conn !== FALSE) {
  $user_id = $_SESSION['user_id'];
  $table = "playlist";
  $sql = "SELECT track_id, title, artist, genre, album, mp3_path, ogg_path, art FROM $table WHERE u_id = $user_id ORDER BY artist ASC";
  $object = array();
  $x = 0;
  foreach ($conn->query($sql) as $row) {
    $object[$x]['track_id'] = "{$row['track_id']}";
    $object[$x]['title'] = "{$row['title']}";
    $object[$x]['artist'] = "{$row['artist']}";
    $object[$x]['genre'] = "{$row['genre']}";
    $object[$x]['album'] = "{$row['album']}";
    $object[$x]['mp3_path'] = "{$row['mp3_path']}";
    $object[$x]['ogg_path'] = "{$row['ogg_path']}";
    $object[$x]['art'] = "{$row['art']}";
    ++$x; 
  }
}

// Retrieve number of total tracks
$track_count = count($object);

if (isset($_POST['delete'])) {

  if (isset($_POST['track_list'])) {
  
    $selections = count($_POST['track_list']);
    $playlist = new Playlist();
  
    for ($x = 0; $x < $selections; $x++ ) {
      $track_id = $_POST['track_list'][$x]; 
      $playlist->deleteTrack($track_id);
    }
    
  }
  
}

?>

<!-- Author: Bryan Thomas -->
<!-- Last modified: 11/17/2018 -->

<?php require_once('php_inc/inc_header.php'); ?>
<title>Delete track(s)</title>
<?php include_once('php_inc/inc_user_nav.php'); ?>
</head>
<body>  

<div class="w3-center">
  <h2>Delete track(s)</h2>
  <i class="w3-xxlarge fa fa-music" style="color:#337ab7;"></i>
</div>

<div class="w3-center"><br>
  <form action="playlist_modify.php" method="POST" enctype="multipart/form-data" class="w3-container" >
    <div class="form-group">

      
      <?php
        $input = "";
        if ($track_count == 0) {
          echo '<p style="color:red;">No tracks to delete...</p>';
        } else {
          for ($x = 0; $x < $track_count; $x++) {
		    $input .= '<input type="checkbox" name="track_list[]" value="' . $object[$x]['track_id'] . '"/> ';
		    $input .= $object[$x]['artist'] . ' - ' . $object[$x]['title'] . '<br>';
	      }
	      echo $input;
	    }
	  ?>

      <br>
    </div>
     
    <button type="submit" name="delete" class="btn btn-primary w3-margin-bottom">Delete</button>
     
  </form>
</div>

<?php include_once('php_inc/inc_user_footer.php'); ?>