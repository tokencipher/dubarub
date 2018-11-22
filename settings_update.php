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
  header('Location: index.php');
  exit; 
}


ini_set( 'display_errors', 1 ); 
error_reporting( E_ALL );


?>

<?php require("php_class/class_User.php"); ?>
<?php require ("php_class/class_Post.php"); ?>

<?php

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

$dir = "dub_priv_user_files/image/avatar/";
$error_count = 0;
$media = null;
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

if (isset($_POST['settings_submit'])) {
  $temp_file = $_FILES['avatar_path']['tmp_name'];
  $target_avi_dir = $dir . $_FILES['avatar_path']['name'];
  $file_info_mime = new finfo(FILEINFO_MIME); // object-oriented approach!
  $all_ext_mimeType = $file_info_mime->buffer(file_get_contents($temp_file));
  $mimeType_array = explode(";", $all_ext_mimeType);
  $file_size = $_FILES['avatar_path']['size'];
  
  // Check if a file has been uploaded
  if ($file_size !== 0) {
    
    switch ($mimeType_array[0]) {
      
      /* begin image types */
      
      case "image/gif":
        $image = TRUE;
        $mime_type = $mimeType_array[0];
        break;
        
      case "image/png":
        $image = TRUE;
        $mime_type = $mimeType_array[0];
        break;
        
      case "image/jpeg":
        $image = TRUE;
        $mime_type = $mimeType_array[0];
        break;
        
      case "image/jpg":
        $image = TRUE;
        $mime_type = $mimeType_array[0];
        break;
        
      case "image/bmp":
        $image = TRUE;
        $mime_type = $mimeType_array[0];
        break;
    
      case "image/webp":
        $image = TRUE;
        $mime_type = $mimeType_array[0];
        break;
      
      default:
        echo '<span style="color:red;">Please upload a valid image file.</span>';
        ++$error_count;  
        break;      
    }
    
    $media = TRUE;
    
  } else {
    $media = FALSE;
  }
  
}

if ($error_count == 0) {

  if ($media) {
  
    if ($image) {
    
      // Get image dimensions
      list($width, $height) = getimagesize($temp_file);
      
      // Check if the file is really an image
      if (($width == null) && ($height == null)) {
        echo "Sorry, your file was not uploaded. It is not a valid image" . "<br />";
        return;
      }
      
      // resize if necessary
      if ($width <= 5000 && $height > 900) {
        $max_width = $width / 4.5;
        $img = new Imagick($temp_file);
        $img->thumbnailImage($max_width, 0);
        
        // Correct image orientation
        autoRotateImage($img);
        
        $img->writeImage($temp_file);
      } 
      
      if ($width >= 3000 && $height > 900) {
        $max_width = $width / 3.5;
        $img = new Imagick($temp_file);
        $img->thumbnailImage($max_width, 0);
        
        // Correct image orientation
        autoRotateImage($img);
        
        $img->writeImage($temp_file);
      }
      
      if ($width >= 2000 && $height > 900) {
        $max_width = $width / 2.5;
        $img = new Imagick($temp_file);
        $img->thumbnailImage($max_width, 0);
        
        // Correct image orientation 
        autoRotateImage($img);
        
        $img->writeImage($temp_file);
      }
      
      if ($width >= 900 && $height >= 900) {
        $max_width = $width / 1.2;
        $img = new Imagick($temp_file);
        $img->thumbnailImage($max_width, 0);
        
        // Correct image orientation
        autoRotateImage($img);
        
        $img->writeImage($temp_file);
      }
    
      if (move_uploaded_file($temp_file, $target_avi_dir) == TRUE) {
        chmod($target_avi_dir, 0644);
        
        // Create user object
        $avatar = new User();
        $avatar->setUserId($user_id);
        $avatar->setAvatar($target_avi_dir);
        $avatar->updateAvatar();
        
        $postAvi = new Post();
        $postAvi->updateAvatar($user_id, $target_avi_dir);
        
        header('location: home.php');
        exit;
    
      }
    
    }
    
  }
  
}

?>
<!-- Author: Bryan Thomas -->
<!-- Last modified: 11/17/2018 -->

<?php require_once('php_inc/inc_header.php'); ?>
<title>Update Settings</title>
<?php include('php_inc/inc_user_nav.php'); ?>
</head>
<body> 

<div class="w3-center">
  <h2>Update Settings</h2>
  <i class="w3-xxlarge fa fa-cog" style="color:#cc6600"></i>
</div>

<div class="w3-center"><br>
  <form action="settings_update.php" method="POST" enctype="multipart/form-data" class="w3-container" >
    <div class="form-group">
      <label for="avatar_file">Choose avatar</label>
      <input style="margin:auto;" type="file" id="avatar_file" name="avatar_path" class="form-control-file" required></input>
      <br>
    </div>
     
    <button type="submit" name="settings_submit" class="btn btn-primary w3-margin-bottom">Update</button>
     
  </form>
</div>

<?php include("php_inc/inc_footer.php"); ?>
