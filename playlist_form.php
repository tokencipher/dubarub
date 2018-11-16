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
  $table = "track";
  $sql = "SELECT track_id, u_id, title, artist, genre, album, mp3_path, ogg_path, art FROM $table ORDER BY artist ASC";
  $object = array();
  $x = 0;
  foreach ($conn->query($sql) as $row) {
    $object[$x]['track_id'] = "{$row['track_id']}";
    $object[$x]['u_id'] = "{$row['u_id']}";
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

if (isset($_POST['upload'])) {

  /*
   * track_selections + playlist_count yields the total playlist count
   * if total_playlist_count > 12 then subtract total_playlist_count by playlist_max
   * total_playlist_count - playlist_max = number of songs user must delete from playlist
   * in order to add their desired songs to playlist
   */
  
  $selections = count($_POST['track_list']);
  $track = new Track();
  $track_info = array();
  $playlist = new Playlist();
  
  if ($selections > 12) {
    $flag = "exceeded playlist max";
    ++$error_count;
  }
  
  for ($x = 0; $x < $selections; $x++ ) {
  
    $track_info = $track->getTrackInfo($_POST['track_list'][$x]);
    $playlist->addTrack(
      $_POST['track_list'][$x],
      $user_id,
      $track_info['title'],
      $track_info['artist'],
      $track_info['genre'],
      $track_info['album'],
      $track_info['duration'],
      $track_info['mp3_path'],
      $track_info['ogg_path'],
      $track_info['art'],
      $track_info['bpm']
    );
  }
  
  /*
  echo $selections . "<br>";
  echo var_dump($_POST['track_list']);  
  */
  
}

if ($error_count > 0) {
  switch($flag) {
    case "exceeded playlist max":
      echo "You have exceeded the maxmimum amount of songs you may add to your playlist.";
      break;
  }
}

?>

<!-- Author: Bryan Thomas -->
<!-- Last modified: 10/11/2018 -->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<title>Create Playlist</title>
<?php include_once('php_inc/inc_user_nav.php'); ?>
</head>
<body>  

<div class="w3-center"><br>
  <form action="playlist_form.php" method="POST" enctype="multipart/form-data" class="w3-container" >
    <div class="form-group">
      <div class="w3-center">
        <h2>Add track(s) to playlist</h2>
        <i class="w3-xxlarge fa fa-music" style="color:#337ab7;"></i>
      </div>
      <br>
      
      <?php
        $input = "";
        if ($track_count == 0) {
          echo '<p style="color:red;">No tracks have been uploaded to the server as of yet...</p>';
        } else {
          for ($x = 0; $x < $track_count; $x++) {
		    $input .= '<input type="checkbox" name="track_list[]" value="' . $object[$x]['track_id'] . '"required/> ';
		    $input .= $object[$x]['artist'] . ' - ' . $object[$x]['title'] . '<br>';
	      }
	      echo $input;
	    }
	  ?>

      <br>
    </div>
     
    <button type="submit" name="upload" class="btn btn-primary w3-margin-bottom">Upload</button>
     
  </form>
</div>

<?php include_once('php_inc/inc_user_footer.php'); ?>