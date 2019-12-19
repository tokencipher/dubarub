<?php
/**
 * Start the session. 
 */
 session_start();
 
/**
 * Check if the user is logged in. 
 */
 
if (!isset($user_id) && !isset($_SESSION['logged_in'])) {
  // User not logged in. Redirect them back to the login.php page.
  header('Location: login.php');
  exit; 
}
?>

<?php 


ini_set( 'display_errors', 1 ); 
error_reporting( E_ALL );



/**
 * Production version 
 */

require("php_class/class_Track.php");
require ("php_inc/inc_db_qp4.php");
require("getID3-1.9.15/getid3/getid3.php");

$audio_dir = "dub_priv_user_files/audio";
$target_art_path = "";
$error_count = 0; 
$audio = false;
$data_format = "";
$bpm = "";
$message = "";
$art_message = "";
$cover_art = false;
$image = false;
$flag = "";
$art_flag = "";
$art_file_size = "";

function autoRotateImage($image) {
  $orientation = $image->getImageOrientation();
  
  switch ($orientation) {
    case imagick::ORIENTATION_BOTTOMRIGHT;
      $image->rotateimage("#000", 180); // rotate 180 degrees
      break;
      
    case imagick::ORIENTATION_RIGHTTOP:
      $image->rotateimage("#000", 90); // rotate 90 degrees CW
      break;
      
    case imagick::ORIENTATION_LEFTBOTTOM:
      $image->rotateimage("000", -90); // rotate 90 degrees CCW
      break;
  }
  
  // Now that it's auto-rotated, make sure the EXIF data is correct in case the EXIF gets
  // saved with the image
  $image->setImageOrientation(imagick::ORIENTATION_TOPLEFT);
  
}

if (isset($_POST['upload'])) {
  $temp_file = $_FILES['track']['tmp_name'];
  if (!empty($temp_file)) {
    $target_mp3_path = $audio_dir . "/mp3/" . $_FILES['track']['name'];
    //$target_ogg_path = $audio_dir . "/ogg/" . $_FILES['track']['name'];
    //$fileInfo_array = getimagesize($temp_file);
    //$mimeType = $fileInfo_array['mime']; // Not being used, really
    $file_info_mime = new finfo(FILEINFO_MIME); // object oriented approach!
    $all_ext_mimeType = $file_info_mime->buffer(file_get_contents($temp_file));  // e.g. gives "image/jpeg"
    $mimeType_array = explode(";", $all_ext_mimeType);
    $mediaType = strtolower(pathinfo($temp_file,PATHINFO_EXTENSION));
    $file_size = $_FILES['track']['size'];
    $cover_art_size = $_FILES['cover_art']['size'];
  } else {
    exit("File is empty!");
  }
  
  $temp_art_file = $_FILES['cover_art']['tmp_name'];
  $target_art_path = $audio_dir . "/cover_art/" . $_FILES['cover_art']['name'];
  
  if (!empty($temp_art_file)) {
    
    $art_fileInfo_array = getimagesize($temp_art_file);
    $art_all_ext_mimeType = $file_info_mime->buffer(file_get_contents($temp_art_file));  // e.g. gives "image/jpeg"
    $art_mimeType_array = explode(";", $art_all_ext_mimeType);
    $art_mediaType = strtolower(pathinfo($temp_file,PATHINFO_EXTENSION));
    $art_file_size = $_FILES['cover_art']['size'];
    
  }
  
  //$art_mimeType = $art_fileInfo_array['mime']; // Not being used, really
  //$art_file_info_mime = new finfo(FILEINFO_MIME); // object oriented approach!
 
  
  /***Audio extentions and mime types***/
  /*
   * mp3	audio/mpeg	RFC 3003
   * mp4    audio	audio/mp4	 
   * aif	audio/x-aiff	 
   * aifc	audio/x-aiff	 
   * aiff	audio/x-aiff	 
   * m3u	audio/x-mpegurl	 
   * ra	    audio/vnd.rn-realaudio	 
   * ram	audio/vnd.rn-realaudio	 
   * Ogg    Vorbis	audio/ogg	RFC 5334
   * Vorbis	audio/vorbis
   */
  
  // Check if a file has been uploaded    
  if($file_size > 0) {

    switch ($mimeType_array[0]) {
    
      case "audio/mpeg":
        $audio = true;
        $getID3 = new getID3;
        $tag = $getID3->analyze($temp_file);
        $html_artist = isset($tag['tags_html']['id3v2']['artist'][0]) ? $tag['tags_html']['id3v2']['artist'][0] : ''; 
    	$html_album = isset($tag['tags_html']['id3v2']['album'][0]) ? $tag['tags_html']['id3v2']['album'][0] : '';
    	
    	if ((empty($html_artist)) && (empty($html_album))) {
    	  $flag = "invalid metadata";
          ++$error_count;
        }
    	break;
    	
      case "audio/mp4": 
        $audio = true;
        $getID3 = new getID3;
        $tag = $getID3->analyze($temp_file);
        $html_artist = isset($tag['tags_html']['id3v2']['artist'][0]) ? $tag['tags_html']['id3v2']['artist'][0] : ''; 
    	$html_album = isset($tag['tags_html']['id3v2']['album'][0]) ? $tag['tags_html']['id3v2']['album'][0] : '';
    	
    	if ((empty($html_artist)) && (empty($html_album))) {
    	  $flag = "invalid metadata";
  		  ++$error_count;
		}
        break;
        
        default:
          $flag = "invalid data format";
          ++$error_count;
          break;
    }
    	   
  } else {
    $audio = false;
    ++$error_count;
  }
   
  // Check if a file has been uploaded
  if ($art_file_size > 0) {
    
    switch ($art_mimeType_array[0]) {
      
      /* begin image types */
      
      case "image/gif":
        $cover_art = true;
        $art_mime_type = $art_mimeType_array[0];
        break;
        
      case "image/png":
        $cover_art = true;
        $art_mime_type = $art_mimeType_array[0];
        break;
        
      case "image/jpeg":
        $cover_art = true;
        $art_mime_type = $art_mimeType_array[0];
        break;
        
      case "image/jpg":
        $cover_art = true;
        $art_mime_type = $art_mimeType_array[0];
        break;
        
      case "image/bmp":
        $cover_art = true;
        $art_mime_type = $art_mimeType_array[0];
    	break;
    
      case "image/webp":
        $cover_art = true;
        $art_mime_type = $art_mimeType_array[0];
        break;
      
      default:
        $art_flag = 'invalid image format';
        ++$error_count;   
    }
    
    $image = true;
    
  } else {
    
    $image = false;
    $cover_art = false;
    $art_flag = 'no image uploaded';
    ++$error_count;
  }

} // end of upload set check //


if ($cover_art) {
  
  // Get image dimensions
  list($width, $height) = getimagesize($temp_art_file);
      
  // Check if the file is really an image
  if (($width == null) && ($height == null)) {
    exit("Sorry, your file was not uploaded. It is not a valid image<br />");
  }
      
  // resize if necessary
  if ($width <= 5000 && $height > 900) {
    $max_width = $width / 4.5;
    $img = new Imagick($temp_art_file);
    $img->thumbnailImage($max_width, 0);
        
    // Correct image orientation
    autoRotateImage($img);
        
    $img->writeImage($temp_art_file);
  }  
      
  if ($width >= 3000 && $height > 900) {
    $max_width = $width / 3.5;
    $img = new Imagick($temp_art_file);
    $img->thumbnailImage($max_width, 0);
        
    // Correct image orientation
    autoRotateImage($img);
        
    $img->writeImage($temp_art_file);
  }
      
  if ($width >= 2000 && $height > 900) {
    $max_width = $width / 2.5;
    $img = new Imagick($temp_art_file);
    $img->thumbnailImage($max_width, 0);
        
    // Correct image orientation 
    autoRotateImage($img);
        
    $img->writeImage($temp_art_file);
  }
      
  if ($width >= 900 && $height >= 900) {
    $max_width = $width / 1.2;
    $img = new Imagick($temp_art_file);
    $img->thumbnailImage($max_width, 0);
        
    // Correct image orientation
    autoRotateImage($img);
        
    $img->writeImage($temp_art_file);
  }
  
}

if ($conn !== FALSE) {
  $table = "track";
  $sql = "SELECT track_id FROM $table WHERE mp3_path = :mp3_path";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':mp3_path', $target_mp3_path);
  $stmt->execute();
  if ($stmt->rowCount() > 0) {
    // The track already exists!
    $flag = "track exists";
    ++$error_count;
  }
}
    

// Check if $uploadOk is set to 0 by an error
if ($error_count > 0) {

  switch ($flag) {
    case "invalid metadata":
       $message = "Please check your audio metadata for valid id3v1/id3v2 artist and album tags.";
       break;
    
    case "track exists":
       $message = "That track already exists.";
       break;
       
    case "invalid data format":
       $message =  "Please upload a valid mp3 or mp4 audio file.";
       break;
       
    default:
       $message = "Sorry, your file was not uploaded.";
       break;     
  }
  
  switch ($art_flag) {
    
    case "invalid image format":
      $art_message = "Please upload a valid image file type (jpg, jpeg, png, bmp, webp).";
      break;
      
    case "no image uploaded":
      $art_message = "Please upload a valid image file as your cover art.";
      break;
    
    default:
      $art_message = "Sorry, your file was not uploaded.";
      
  }
  
}

if ($error_count == 0) {

  if ($audio && $image) {
  
    if ((move_uploaded_file($temp_file, $target_mp3_path)) && (move_uploaded_file($temp_art_file, $target_art_path)) == TRUE) {
        
        chmod($target_mp3_path, 0644);
        chmod($target_art_path, 0644);
        
        echo "<p class=\"trackUpload\" style=\"color:green;\">" . "File \"" . htmlentities($_FILES['track']['name']) . "\" successfully 
        uploaded." . "</p>";
        
        echo "<p class=\"trackUpload\" style=\"color:green;\">" . "File \"" . htmlentities($_FILES['cover_art']['name']) . "\" successfully 
        uploaded." . "</p>";
    
        // Remember to get user id from SESSION variable in production code
        $user_id = $_SESSION['user_id']; 
        $user_name = $_SESSION['user_name'];
        
        $audio_size = isset($tag['filesize']) ? $tag['filesize'] : ''; // echo "Filesize: {$tag['filesize']}<br>";
        //$audio_path = $tag['filepath']; // echo "Filepath: {$tag['filepath']}<br>";
    	//$audio_name_path = $tag['filenamepath']; // echo "Filename path: {$tag['filenamepath']}<br>";
    	//$file_format = $tag['fileformat']; // echo "Fileformat: {$tag['fileformat']}<br>";
    	//$data_format = $tag['dataformat']; // echo "Dataformat: {$tag['audio']['dataformat']}<br>";
    	//$channels = $tag['audio']['channels']; // echo "Channels: {$tag['audio']['channels']}<br>";
    	//$bitrate = $tag['audio']['bitrate']; // echo "Sample rate: {$tag['audio']['bitrate']}<br>";
    	//$channel_mode = $tag['audio']['channelmode']; // echo "Channel mode: {$tag['audio']['channelmode']}<br>";
    	//$bitrate_mode = $tag['audio']['bitrate_mode']; // echo "Bitrate mode: {$tag['audio']['bitrate_mode']}<br>";
    	$html_title = isset($tag['tags_html']['id3v2']['title'][0]) ? $tag['tags_html']['id3v2']['title'][0] : 'Unkown'; // echo "Tags html title: {$tag['tags_html']['id3v2']['title'][0]}<br>";
    	$html_artist = isset($tag['tags_html']['id3v2']['artist'][0]) ? $tag['tags_html']['id3v2']['artist'][0] : 'Unknown'; // echo "Tags html artist: {$tag['tags_html']['id3v2']['artist'][0]}<br>";
    	$html_album = isset($tag['tags_html']['id3v2']['album'][0]) ? $tag['tags_html']['id3v2']['album'][0] : 'Unknown'; // echo "Tags html album: {$tag['tags_html']['id3v2']['album'][0]}<br>";
    	$html_year = isset($tag['tags_html']['id3v2']['year'][0]) ? $tag['tags_html']['id3v2']['year'][0] : 'Unknown'; // echo "Tags html year: {$tag['tags_html']['id3v2']['year'][0]}<br>";
    	$html_genre = isset($tag['tags_html']['id3v2']['genre'][0]) ? $tag['tags_html']['id3v2']['genre'][0] : 'Unknown'; // echo "Tags html genre: {$tag['tags_html']['id3v2']['genre'][0]}<br>";
    	$html_playtime = isset($tag['playtime_string']) ? $tag['playtime_string'] : ''; // echo "Tags html playtime string: {$tag['playtime_string']}<br>";
    	$bpm = isset($tag['tags']['id3v2']['bpm'][0]) ? $tag['tags']['id3v2']['bpm'][0] : 'Unknown';           
    
        // Declare and initialize required values to be added to post object
		$audio = new Track();
		$audio->setUserId($user_id);
		$audio->setArtist($html_artist);
		$audio->setAlbum($html_album);
		$audio->setTitle($html_title);
		$audio->setYear($html_year);
		$audio->setGenre($html_genre);
		$audio->setDuration($html_playtime);
		$audio->setFormat($data_format);
		$audio->setFileSize($audio_size);
		$audio->setMp3Path($target_mp3_path);
		$audio->setBPM($bpm);
		$audio->setCoverArt($target_art_path);
		
		$audio->createTrack();
		
	} else {
	
	   // Remember to get user id from SESSION variable in production code
        $user_id = $_SESSION['user_id']; 
        $user_name = $_SESSION['user_name'];
        
        $audio_size = isset($tag['filesize']) ? $tag['filesize'] : ''; // echo "Filesize: {$tag['filesize']}<br>";
        //$audio_path = $tag['filepath']; // echo "Filepath: {$tag['filepath']}<br>";
    	//$audio_name_path = $tag['filenamepath']; // echo "Filename path: {$tag['filenamepath']}<br>";
    	//$file_format = $tag['fileformat']; // echo "Fileformat: {$tag['fileformat']}<br>";
    	//$data_format = $tag['dataformat']; // echo "Dataformat: {$tag['audio']['dataformat']}<br>";
    	//$channels = $tag['audio']['channels']; // echo "Channels: {$tag['audio']['channels']}<br>";
    	//$bitrate = $tag['audio']['bitrate']; // echo "Sample rate: {$tag['audio']['bitrate']}<br>";
    	//$channel_mode = $tag['audio']['channelmode']; // echo "Channel mode: {$tag['audio']['channelmode']}<br>";
    	//$bitrate_mode = $tag['audio']['bitrate_mode']; // echo "Bitrate mode: {$tag['audio']['bitrate_mode']}<br>";
    	$html_title = isset($tag['tags_html']['id3v2']['title'][0]) ? $tag['tags_html']['id3v2']['title'][0] : 'Unkown'; // echo "Tags html title: {$tag['tags_html']['id3v2']['title'][0]}<br>";
    	$html_artist = isset($tag['tags_html']['id3v2']['artist'][0]) ? $tag['tags_html']['id3v2']['artist'][0] : 'Unknown'; // echo "Tags html artist: {$tag['tags_html']['id3v2']['artist'][0]}<br>";
    	$html_album = isset($tag['tags_html']['id3v2']['album'][0]) ? $tag['tags_html']['id3v2']['album'][0] : 'Unknown'; // echo "Tags html album: {$tag['tags_html']['id3v2']['album'][0]}<br>";
    	$html_year = isset($tag['tags_html']['id3v2']['year'][0]) ? $tag['tags_html']['id3v2']['year'][0] : 'Unknown'; // echo "Tags html year: {$tag['tags_html']['id3v2']['year'][0]}<br>";
    	$html_genre = isset($tag['tags_html']['id3v2']['genre'][0]) ? $tag['tags_html']['id3v2']['genre'][0] : 'Unknown'; // echo "Tags html genre: {$tag['tags_html']['id3v2']['genre'][0]}<br>";
    	$html_playtime = isset($tag['playtime_string']) ? $tag['playtime_string'] : ''; // echo "Tags html playtime string: {$tag['playtime_string']}<br>";
    	$bpm = isset($tag['tags']['id3v2']['bpm'][0]) ? $tag['tags']['id3v2']['bpm'][0] : 'Unknown';           
    
        // Declare and initialize required values to be added to post object
		$audio = new Track();
		$audio->setUserId($user_id);
		$audio->setArtist($html_artist);
		$audio->setAlbum($html_album);
		$audio->setTitle($html_title);
		$audio->setYear($html_year);
		$audio->setGenre($html_genre);
		$audio->setDuration($html_playtime);
		$audio->setFormat($data_format);
		$audio->setFileSize($audio_size);
		$audio->setMp3Path($target_mp3_path);
		$audio->setBPM($bpm);
		
		$audio->createTrack();
	
	}
  
  }
  
}
    

?>

<!-- Author: Bryan Thomas -->
<!-- Last modified: 12/19/2019 -->
<?php require_once('php_inc/inc_header.php'); ?>
<title>Upload track(s)</title>
<?php include_once('php_inc/inc_user_nav.php'); ?>
</head>
<body>  

<div class="w3-center">
  <h2>Upload track(s)</h2>
  <i class="w3-xxlarge fa fa-music" style="color:#337ab7;"></i>
</div>

<div class="w3-center"><br>
  <form action="music_form.php" method="POST" enctype="multipart/form-data" class="w3-container" >
    <div class="form-group">
      <label for="track_file">Choose track(s)</label>
      <input style="margin:auto;" type="file" id="track_file" name="track" class="form-control-file" required></input>
      <br>
      <label for="cover_art">Choose cover art</label>
      <input style="margin:auto;" type="file" id="art_file" name="cover_art" class="form-control-file"></input>
    </div>
     
    <button type="submit" name="upload" class="btn btn-primary w3-margin-bottom">Upload</button>
     
  </form>
</div>

<div class="w3-center" id="message" style="position:relative;margin:auto;color:red;"><?php echo $message; ?></div><br>
<div class="w3-center" id="art_message" style="position:relative;margin:auto;color:red;"><?php echo $art_message; ?></div>

<?php include_once('php_inc/inc_user_footer.php'); ?>