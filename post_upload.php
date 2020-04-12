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


ini_set( 'display_errors', 1 ); 
error_reporting( E_ALL );
/*
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_input_time', 300);
ini_set('max_execution_time', 300);
*/


/**
 * Production version 
 */

require("php_class/class_Post.php");
require("php_class/class_User.php");
require("php_class/class_Tag.php");

$dir = "dub_priv_user_files";
$error_count = 0; 

$image = false;
$video = false;

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

function redisplayForm($post_title, $post_entry) {
?>
  
  <form action="post_upload.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label for="post_title">Enter title of new post</label>
      <input type="text" value="<?php echo $post_title; ?>" maxlength="80" name="post_title" class="form-control" id="post_title" aria-describedby="title_help" placeholder="Title" required>
      <small id="title_help" class="form-text text-muted">Title cannot be any longer than 80 characters.</small>
    </div>
    <div class="form-group">
      <label for="post_multimedia">Add image/video</label>
      <!--<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />-->
      <!--<input type="hidden" name="user_id" value="1" />-->
      <input type="file" name="new_file" class="form-control-file" id="post_multimedia" aria-describedby="multimedia_help">
      <small id="multimedia_help" class="form_text text-muted">File cannot be any larger than 10MB</small>
    </div>
    <div class="form-group">
      <label for="photo_credit">Enter photo credit for image (optional)</label>
      <input type="text" maxlength="50" name="photo_cred" class="form-control" id="photo_credit" aria-describedby="title_help" placeholder="Photo credit" required>
      <small id="title_help" class="form-text text-muted">Photo credit cannot be any longer than 50 characters.</small>
    </div>
    <div class="form-group">
      <label for="post_text">Now, let your mind flow freely</label>
      <textarea id="post_text" value="<?php echo $post_entry; ?>" name="post_text" class="form-control" rows="3" required></textarea>
    </div>
    <div class="form-group">
      <label for="post_tags">Tags</label>
      <input type="text" maxlength="255" name="post_tags" class="form-control" id="post_tags" aria-describedby="tags_help" placeholder="Tags">
      <small id="tags_help" class="form-text text-muted"></small>
    </div>
    <br>
    <button type="submit" name="upload" class="btn btn-primary">Add post</button>
  </form>


<?php
}
?>

<?php

function sanitize($str) {
  $new_str = trim($str);
  $new_str = stripslashes($str);
  $new_str = filter_var($new_str, FILTER_SANITIZE_STRING);
  return $new_str;
}

if (isset($_POST['upload'])) {
  $target_dir = "";
  $temp_file = $_FILES['new_file']['tmp_name'];
  $target_img_dir = $dir . "/image/" . $_FILES['new_file']['name'];
  $target_vid_dir = $dir . "/video/" . $_FILES['new_file']['name'];
  $file_size = $_FILES['new_file']['size'];

  
  if (!empty($temp_file)) {
    $file_info_mime = new finfo(FILEINFO_MIME);
    $all_ext_mimeType = $file_info_mime->buffer(file_get_contents($temp_file));
    $mimeType_array = explode(";", $all_ext_mimeType);
  }
  //$mimeType = $fileInfo_array['mime'];
  //$fileInfo_array = getimagesize($temp_file);
  //$all_ext_mimeType = $file_info_mime->buffer(file_get_contents($temp_file));  // e.g. gives "image/jpeg
  //$mediaType = strtolower(pathinfo($target_dir,PATHINFO_EXTENSION));
  
  // Post content char count
  $post_char_cnt = strlen($_POST['post_text']);
  
  //Post content word count
  $post_word_cnt = str_word_count($_POST['post_text']);
  
  // Prepare insertion of tags 
  $tags = $_POST['post_tags'];
    
  // Total character count of tags
  $tag_char_cnt = strlen($tags);
    
  // Total word count of tags
  $tag_word_cnt = str_word_count($tags);
  
  $tagStr = rtrim($tags, " ");
    
  // sanitize tag string
  $newTagStr = str_replace("#", "", $tagStr);  
    
  // Convert tag string to array
  $tag_array = explode(" ", $newTagStr);
  
  // Check if a file has been uploaded    
  if($file_size !== 0) {
    
    switch ($mimeType_array[0]) {
    
     /* begin image types */
    
      case "image/gif":
        $image = true;
        $mime_type = $mimeType_array[0];
        break;
    
      case "image/png":
        $image = true;
        $mime_type = $mimeType_array[0];
        break;
    
      case "image/jpeg":
        $image = true;
        $mime_type = $mimeType_array[0];
        break;
        
      case "image/jpg":
        $image = true;
        $mime_type = $mimeType_array[0];
        break;
        
      case "image/bmp":
        $image = true;
        $mime_type = $mimeType_array[0];
        break;
        
      case "image/webp":
        $image = true;
        $mime_type = $mimeType_array[0];
        break;
        
    /* end image types */
    
    /* begin video types */
    
      case "video/webm":
        $video = true;
        break;
        
      case "video/ogg":
        $video = true;
        break;
        
      case "video/mp4":
        $video = true;
        break;
        
      case "video/m4v":
        $video = true;
        break;
        
      case "video/quicktime":
        $video = true;
        
        $quicktime = true;
        
        break;
        
      case "video/wav":
        $video = true;
        break;
        
      case "video/x-flv":
        $video = true;
        break;
    
      case "video/MP2T":
        $video = true;
        break;
        
      case "video/3gpp":
        $video = true;
        break;
        
      case "video/x-msvideo":
        $video = true;
        break;
     
      case "video/x-ms-wmv":
        $video = true;
        break;
    
      default: 
        echo "Sorry, only image/video files are allowed.";
        ++$error_count;
        
        // redisplay post form
        $post_title = $_POST['post_title'];
        $post_entry = $_POST['post_text'];
        break;
        
    /* end video types */
    
    }
    
    $media = true;
    
  } else {
    $media = false;
  }
  /*
  if ($filesize > 10000000) {
    echo "Sorry, your media cannot be larger than 10MB. Please re-size then try again.";
    $post_title = $_POST['post_title'];
    $post_entry = $_POST['post_text'];
    ++$errorCount;
  } else {
    $media = false;
  }
  */
  
  if (isset($_POST['external_url'])) {
    $url = $_POST['external_url'];
    
    // Remove all illegal characters from url
    $url = filter_var($url, FILTER_SANITIZE_URL);
    
    // Validate url
    if (filter_var($url, FILTER_VALIDATE_URL)) {
      $external = true;
    } else {
      $external = false;
    }
  }  
  
  /*
  
  if (isset($_FILES['new_file'])) {
    // placeholder
  }
  
  */
}
  

// Check if $uploadOk is set to 0 by an error
if ($error_count > 0) {
  redisplayForm($post_title, $post_entry);
  echo "Sorry, your file was not uploaded." . "<br />";
// if everything is ok, try to upload file
}

if ($error_count == 0) {

  if ($media) {
    
    if ($image) {
    
      // Get image dimensions
      list($width, $height) = getimagesize($temp_file);
      
      // check if the file is really an image
      if (($width == null) && ($height == null)) {
        redisplayForm($post_title, $post_entry);
        echo "Sorry, your file was not uploaded. Is it not a valid image" . "<br />";
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
     
      if (move_uploaded_file($temp_file, $target_img_dir) == true) {
        chmod($target_img_dir, 0644);
        echo "File \"" . htmlentities($_FILES['new_file']['name']) . "\"successfully 
        uploaded.<br />\n";
    
        // Remember to get user id from SESSION variable in production code
        $user_id = $_SESSION['user_id']; 
        $user_name = $_SESSION['user_name'];      
    
        // Declare and initialize required values to be added to post object
        $display = "true";
        $post_title = sanitize($_POST['post_title']); // sanitize 
        $image_url = $dir . "/image/" . $_FILES['new_file']['name'];
        $image_size = $_FILES['new_file']['size'];
        $post_entry = sanitize($_POST['post_text']); // sanitize 
        $image_flag = "true";
        // sanitize 
        $photo_credit = empty($_POST['photo_cred']) ? "Photo credit: Unknown" : "Photo credit: " . sanitize($_POST['photo_cred']);
        
        // Retrieve avatar from user object 
        $avatar = new User();
        $avatar->setUserId($user_id);
        $avatar_path = $avatar->getAvatar();
    
        // Add required values to post object
        $Post = new Post();
        $Post->setUserId($user_id);
        $Post->setUsername($user_name);
        $Post->setTitle($post_title); // sanitize 
        $Post->setAvatar($avatar_path);
        $Post->setPhotoUrl($image_url); 
        $Post->setPhotoCredit($photo_credit); // sanitize 
        $Post->setImageFlagString($image_flag);
        $Post->setSize($image_size);
        $Post->setEntry($post_entry); // sanitize 
        $Post->setEntryCharCnt($post_char_cnt);
        $Post->setEntryWordCnt($post_word_cnt);
        $Post->setDisplay($display);
        $Post->setMimeType($mimeType_array[0]);
    
        // Insert user post values into db 
        $Post->createPostWImage(); 
    
        // Get post ID
        $post_id = $Post->getPostId();
    
        if (!(empty($_POST['post_tags']))) {
          // Add required values to tag object
          $Tag = new Tag();
          $Tag->setUserId($user_id);
          $Tag->setPostId($post_id);
          $Tag->setTags($tag_array);
          $Tag->insertTags();
        }    
        
        header('Location: home.php'); 
        exit;
    
      } 
    } else if ($video) {
     
      if (move_uploaded_file($temp_file, $target_vid_dir) == true) {
        chmod($target_vid_dir, 0644);
        /*
        echo "File \"" . htmlentities($_FILES['new_file']['name']) . "\"successfully 
        uploaded.<br />\n";
        */
        // Remember to get user id from SESSION variable in production code
        $user_id = $_SESSION['user_id']; 
        $user_name = $_SESSION['user_name'];
        
        /**
         * Check to see if uploaded video is quicktime format. 
         * if so, convert .mov to .mp4
         *
         * if ($quicktime) {
            // exec .mov to .mp4
            // $basename = explode(".", $_FILES['new_file']['name']);
            // $basename = $basename[0];
            // $video_url = $dir . "/video/" . $basename . ".mp4"
         } else {}
         */
         
         /**
         if ($quicktime) {
           $basename = explode(".", $_FILES['new_file']['name']);
           $basename = $basename[0];
           $video_url = $dir . "/video/" . $basename . ".mp4"
           $target = $target_vid_dir;
           $output = $video_url;
           header('Location: tryit.php?t=$target&o=$output');
           // shell_exec('cd qp4_priv_user_files/video; sudo /usr/bin/ffmpeg -i ' . $target_vid_dir . ' '  . $video_url . ' 2>&1');
         } else {
           
         }
         */
         
         //shell_exec('curl https://quarterpast4.com/tryit.php?target=somestuff');
    
    
        // Declare and initialize required values to be added to post object
        $display = "true";
        $video_flag = "true";
        $basename = explode(".", $_FILES['new_file']['name']);
        $basename = $basename[0];
        $video_url = $dir . "/video/" . $_FILES['new_file']['name'];
        $video_mp4 = $dir . "/video/" . $basename . ".mp4";
        $thumbnail = $dir . "/video/" . $basename . ".jpg";
        $post_title = sanitize($_POST['post_title']);
        $video_size = $_FILES['new_file']['size'];
        $post_entry = sanitize($_POST['post_text']); 
      
        
        // Convert video for browser support
        /*
        $m4v_path = pathinfo($_FILES['new_file']['name'], PATHINFO_FILENAME);
        $m4v_path .= '.m4v';
        $_SESSION['ogg'] = $ogg_conversion;
        $_SESSION['target'] = $video_url;
        //header('Location: tryit.php');
        // echo shell_exec('cd ' . $dir . '/video' . '; sudo /usr/local/bin/ffmpeg -i ' . $video_url . ' ' .  $ogg_conversion . ' 2>&1');
        echo shell_exec('curl https://quarterpast4.com/tryit.php?target=' . $video_url . '&convert=' . $m4v_path);
        */
        
        // Retrieve avatar from user object 
        $avatar = new User();
        $avatar->setUserId($user_id);
        $avatar_path = $avatar->getAvatar();
      
        // Add required values to post object
        $Post = new Post();
        $Post->setUserId($user_id);
        $Post->setUsername($user_name);
        $Post->setTitle($post_title);
        $Post->setAvatar($avatar_path);
        $Post->setVideoUrl($video_url);
        $Post->setVideoMp4($video_mp4);
        $Post->setVideoThumbnail($thumbnail);
        $Post->setVideoFlagString($video_flag);
        $Post->setSize($video_size);
        $Post->setEntry($post_entry);
        $Post->setEntryCharCnt($post_char_cnt);
        $Post->setEntryWordCnt($post_word_cnt);
        $Post->setDisplay($display);
        $Post->setMimeType($mimeType_array[0]);
    
        // Insert user post values into db 
        $Post->createPostWVideo(); 
    
        // Get post ID
        $post_id = $Post->getPostId();
    
        if (!(empty($_POST['post_tags']))) {
          // Add required values to tag object
          $Tag = new Tag();
          $Tag->setUserId($user_id);
          $Tag->setPostId($post_id);
          $Tag->setTags($tag_array);
          $Tag->insertTags();
        }
    
        header('Location: home.php'); 
        exit;
      } 
    } else {
      echo "There was an error uploading \"" . htmlentities($_FILES['new_file']['name']) .
      "\".<br />\n";
      redisplayForm($post_title, $post_entry);
    }
  } else if ($external) {
    // Post created with an external URL
    $display = "true";
    $user_id = $_SESSION['user_id']; 
    $user_name = $_SESSION['user_name'];
    $post_title = sanitize($_POST['post_title']);
    $post_entry = sanitize($_POST['post_text']);
    
    $external_url = str_replace("watch?v=", "embed/", $url);
    $external_url_flag = "true";
    
    // Retrieve avatar from user object 
    $avatar = new User();
    $avatar->setUserId($user_id);
    $avatar_path = $avatar->getAvatar();
  
    $Post = new Post();
    $Post->setUserId($user_id);
    $Post->setUsername($user_name);
    $Post->setAvatar($avatar_path);
    $Post->setTitle($post_title);
    $Post->setEntry($post_entry);
    $Post->setEntryCharCnt($post_char_cnt);
    $Post->setEntryWordCnt($post_word_cnt);
    $Post->setDisplay($display);
    $Post->setExternalUrl($external_url);
    $Post->setExternalUrlFlag($external_url_flag);
    $Post->createPostWUrl();
  
    // Get most recent post ID
    $post_id = $Post->getPostId();
  
    if (!(empty($_POST['post_tags']))) {
      // Add required values to tag object
      $Tag = new Tag();
      $Tag->setUserId($user_id);
      $Tag->setPostId($post_id);
      $Tag->setTags($tag_array);
      $Tag->insertTags();
    }
  
    header('Location: home.php');
    exit;
    
  } else {
    // Post created without an image. Call required object function.
    $display = "true";
    $user_id = $_SESSION['user_id']; 
    $user_name = $_SESSION['user_name'];
    $post_title = sanitize($_POST['post_title']);
    $post_entry = sanitize($_POST['post_text']);
    
    // Retrieve avatar from user object 
    $avatar = new User();
    $avatar->setUserId($user_id);
    $avatar_path = $avatar->getAvatar();
  
    $Post = new Post();
    $Post->setUserId($user_id);
    $Post->setUsername($user_name);
    $Post->setAvatar($avatar_path);
    $Post->setTitle($post_title);
    $Post->setEntry($post_entry);
    $Post->setEntryCharCnt($post_char_cnt);
    $Post->setEntryWordCnt($post_word_cnt);
    $Post->setDisplay($display);
    $Post->insertPost();
      
    // Get most recent post ID
    $post_id = $Post->getPostId();
  
    if (!(empty($_POST['post_tags']))) {
      // Add required values to tag object
      $Tag = new Tag();
      $Tag->setUserId($user_id);
      $Tag->setPostId($post_id);
      $Tag->setTags($tag_array);
      $Tag->insertTags();
    }
  
    header('Location: home.php');
    exit;
  
  }
  
}

?>
<!-- Author: Bryan Thomas -->
<!-- Last modified: 09/31/2019 -->

<?php require_once('php_inc/inc_header.php'); ?>
<title>Post Upload</title>
<style>
  .heading {
    position:relative;
  }
  form {
    position:relative;
    top:50px;
    width:50%;
    margin:auto;
  }
</style>
<?php include('php_inc/inc_user_nav.php'); ?>
</head>
<body> 
<div id="result" class="w3-center" style="position:relative;top:100px;margin:auto;color:red;">
  <?php echo $result; ?>
</div>
</body>
<?php include("php_inc/inc_footer.php"); ?>