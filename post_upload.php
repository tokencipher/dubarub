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

/*
ini_set( 'display_errors', 1 ); 
error_reporting( E_ALL );
*/

/**
 * Production version 
 */

require("php_class/class_Post.php");
require("php_class/class_User.php");
require("php_class/class_Tag.php");

$dir = "dub_priv_user_files";
$error_count = 0; 

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

if (isset($_POST['upload'])) {
  $target_dir = "";
  $temp_file = $_FILES['new_file']['tmp_name'];
  $target_img_dir = $dir . "/image/" . $_FILES['new_file']['name'];
  $target_vid_dir = $dir . "/video/" . $_FILES['new_file']['name'];
  $fileInfo_array = getimagesize($temp_file);
  $mimeType = $fileInfo_array['mime']; // Not being used, really
  $file_info_mime = new finfo(FILEINFO_MIME); // object oriented approach!
  $all_ext_mimeType = $file_info_mime->buffer(file_get_contents($temp_file));  // e.g. gives "image/jpeg"
  $mimeType_array = explode(";", $all_ext_mimeType);
  $mediaType = strtolower(pathinfo($target_dir,PATHINFO_EXTENSION));
  $file_size = $_FILES['new_file']['size'];
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
        $image = TRUE;
        $video = FALSE;
        $mime_type = $mimeType_array[0];
        break;
    
      case "image/png":
        $image = TRUE;
        $video = FALSE;
        $mime_type = $mimeType_array[0];
        break;
    
      case "image/jpeg":
        $image = TRUE;
        $video = FALSE;
        $mime_type = $mimeType_array[0];
        break;
        
      case "image/jpg":
        $image = TRUE;
        $video = FALSE;
        $mime_type = $mimeType_array[0];
        break;
        
      case "image/bmp":
        $image = TRUE;
        $video = FALSE;
        $mime_type = $mimeType_array[0];
        break;
        
      case "image/webp":
        $image = TRUE;
        $video = FALSE;
        $mime_type = $mimeType_array[0];
        break;
        
    /* end image types */
    
    /* begin video types */
    
      case "video/webm":
        $video = TRUE;
        $image = FALSE;
        break;
        
      case "video/ogg":
        $video = TRUE;
        $image = FALSE;
        break;
        
      case "video/mp4":
        $video = TRUE;
        $image = FALSE;
        break;
        
      case "video/m4v":
        $video = TRUE;
        $image = FALSE;
        break;
        
      case "video/quicktime":
        $video = TRUE;
        $image = FALSE;
        
        $quicktime = TRUE;
        
        break;
        
      case "video/wav":
        $video = TRUE;
        $image = FALSE;
        break;
        
      case "video/x-flv":
        $video = TRUE;
        $image = FALSE;
        break;
    
      case "video/MP2T":
        $video = TRUE;
        $image = FALSE;
        break;
        
      case "video/3gpp":
        $video = TRUE;
        $image = FALSE;
        break;
        
      case "video/x-msvideo":
        $video = TRUE;
        $image = FALSE;
        break;
     
      case "video/x-ms-wmv":
        $video = TRUE;
        $image = FALSE; 
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
    
    $media = TRUE;
    
  } else {
    $media = FALSE;
  }
   
  /*  

  } else if ($filesize > 10000000) {
      echo "Sorry, your media cannot be larger than 10MB. Please re-size then try again.";
      $post_title = $_POST['post_title'];
      $post_entry = $_POST['post_text'];
      ++$errorCount;
  } else {
      $media = FALSE;
  }
  
  
  
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
     
      if (move_uploaded_file($temp_file, $target_img_dir) == TRUE) {
        chmod($target_img_dir, 0644);
        echo "File \"" . htmlentities($_FILES['new_file']['name']) . "\"successfully 
        uploaded.<br />\n";
    
        // Remember to get user id from SESSION variable in production code
        $user_id = $_SESSION['user_id']; 
        $user_name = $_SESSION['user_name'];      
    
        // Declare and initialize required values to be added to post object
        $display = "true";
        $post_title = $_POST['post_title'];
        $image_url = $dir . "/image/" . $_FILES['new_file']['name'];
        $image_size = $_FILES['new_file']['size'];
        $post_entry = $_POST['post_text']; 
        $image_flag = "true";
        $photo_credit = empty($_POST['photo_cred']) ? "Photo credit: Unknown" : "Photo credit: " . $_POST['photo_cred'];
        
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
        $Post->setPhotoUrl($image_url);
        $Post->setPhotoCredit($photo_credit);
        $Post->setImageFlagString($image_flag);
        $Post->setSize($image_size);
        $Post->setEntry($post_entry);
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
     
      if (move_uploaded_file($temp_file, $target_vid_dir) == TRUE) {
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
        $post_title = $_POST['post_title'];
        $video_size = $_FILES['new_file']['size'];
        $post_entry = $_POST['post_text']; 
      
        
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
  } else {
    // Post created without an image. Call required object function.
    $display = "true";
    $user_id = $_SESSION['user_id']; 
    $user_name = $_SESSION['user_name'];
    $post_title = $_POST['post_title'];
    $post_entry = $_POST['post_text'];
    
    // Retrieve avatar from user object 
    $avatar = new User();
    $avatar->setUserId($user_id);
    $avatar_path = $avatar->getAvatar();
  
    $Post = new Post();
    $Post->insertPost($user_id, $user_name, $avatar, $post_title, $post_entry, $post_char_cnt, $post_word_cnt, $display);
  
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
<!-- Last modified: 08/02/2018 -->
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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