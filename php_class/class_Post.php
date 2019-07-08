<?php 

/** 
 * Post class for saving and retrieving blobs from MySQL 
 */

class Post {
  private $db;
  private $user_id;
  private $user_name;
  private $post_id;
  private $display;
  private $title;
  private $avatar;
  private $photo_url;
  private $image_flag;
  private $video_url;
  private $video_mp4_url;
  private $video_webm_url;
  private $thumbnail;
  private $video_flag;
  private $file_size;
  private $entry;
  private $entry_char_count;
  private $entry_word_count;
  private $photo_blob;
  private $video_blob;
  private $mime_type;
  private $timestamp;
  private $likes;
  private $comments;
  private $photo_cred;
  private $video;
  private $image;
  private $external;
  private $external_url;
  

  
  public function __construct() {
    include ("php_inc/inc_db_qp4.php");
    if ($conn !== FALSE) {
      $this->db = $conn;
    }
  }
  
  public function __destruct() {
    $this->db = null;
  }
  
  // specify which data members of the class to serialize
  public function __sleep() {}
  
  // initialize any data members that were not saved with the serialization process
  public function __wakeup() {}
  
  public function dbClose() {
    $this->db = null;
  }
  
  public function getPostId() {
    $table = "post";
    $u_id = $this->user_id;
    $sql = "SELECT p_id FROM $table WHERE u_id=$u_id;";
    foreach ($this->db->query($sql) as $row) {
      $recent_post_id = "{$row['p_id']}";
    }
    return $recent_post_id;
  }
  
  public function deletePost($p_id) {
    $table = "post";
    $sql = "UPDATE $table SET display = 'false' WHERE p_id = :p_id";
    
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':p_id', $p_id);
    
    return $stmt->execute();
  } 
  
  /*
  public function convertVideo() {
     // Get video url
     $video_url = $this->video_url;
     
     // Send url to video processing and conversion file via AJAX 
     var convertRequest = new XMLHttpRequest();
     convertRequest.onreadystatechange = function() {
       if (this.readystate = 4 && this.status == 200) {
         var mysql = require('mysql');
         var obj = JSON.parse(this.responseText);
         var postCnt = obj.length;
       }
     } 
     convertRequest.open("POST", "https://quarterpast4.com:8080", true);
     convertRequest.send($video_url);
  }
  */
  
  public function getTitle() {
    return $this->title;
  }
  
  public function getPhotoUrl() {
    return $this->photo_url;
  }
  
  public function getVideoUrl() {
    return $this->video_url;
  }
  
  public function getSize() {
    return $this->file_size;
  }
  
  public function getPostDisplayCount() {
    $table = "post";
    $user_id = $this->user_id;
    $sql = "SELECT display FROM $table WHERE display = 'true' && u_id = $user_id;";
    $x = 0;
    foreach ($this->db->query($sql) as $row) {
      $object[$x]['display'] = "{$row['display']}";
      ++$x; 
	}
	return $x;
  }
  
  public function getEntry() {
    return $this->entry;
  }
  
  public function getPhotoBlob() {}
  
  public function getVideoBlob() {}
  
  public function getMimeType() {
    return $this->mime_type;
  }
  
  public function getPostIdDB($u_id, $title, $photo_url, $video_url, $p_id) {}
  
  public function getUserIdDB() {}
  
  public function getTitleDB() {}
  
  public function getPhotoUrlDB() {}
  
  public function getVideoUrlDB() {}
  
  public function getSizeDB() {}
  
  public function getEntryDB() {}
  
  public function getPhotoBlobDB() {}
  
  public function getVideoBlobDB() {}
  
  public function getMimeTypeDB() {}
  
  public function getTimestampDB() {}
  
  public function setUserId($u_id) {
    $this->user_id = $u_id;
  }
  
  public function setUsername($user_name) {
    $this->user_name = $user_name;
  }
  
  public function setTitle($title) {
    $this->title = $title;
  }
  
  public function setAvatar($avatar) {
    $this->avatar = $avatar;
  }
  
  public function updateAvatar($user_id, $avatar) {
    $table = "post";
    $sql = "UPDATE $table SET avatar = :avatar WHERE u_id = :user_id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':avatar', $avatar);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();  
  }
  
  public function setPhotoUrl($image_path) {
    $this->photo_url = $image_path;
  }
  
  public function setPhotoCredit($photo_cred) {
    $this->photo_cred = $photo_cred;
  }
  
  public function setImageFlagString($boolean_string) {
    $this->image_flag = $boolean_string;
  }
  
  public function setVideoFlagString($boolean_string) {
    $this->video_flag = $boolean_string;
  }
  
  public function setVideoUrl($video_path) {
    $this->video_url = $video_path;
  }
  
  public function setVideoMp4($video_mp4) {
    $this->video_mp4_url = $video_mp4;
  }
  
  public function setVideoThumbnail($thumbnail) {
    $this->thumbnail = $thumbnail;
  }
  
  public function setSize($file_size) {
    $this->file_size = $file_size;
  }
  
  public function setEntry($content) {
    $this->entry = $content;
  }
  
  public function setEntryCharCnt($numChar) {
    $this->entry_char_cnt = $numChar;
  }
  
  public function setDisplay($display) {
    $this->display = $display;
  }
  
  public function setEntryWordCnt($numWord) {
    $this->entry_word_cnt = $numWord;
  }
  
  public function setMimeType($mime) {
    $this->mime_type = $mime;
  }
  
  public function getUpvote($p_id) {
    $table = "post";
    $sql = "SELECT upvote FROM $table WHERE p_id = :p_id";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':p_id', $p_id);
    return $stmt->execute();
  }
  
  public function upvote($p_id) {
    // Get upvote count so we can increment it and send to DB
    $count = getUpvote($p_id);
    
    // Increment retrieved upvote
    $count += 1;
  
    $table = "post";
    $sql = "UPDATE $table SET upvote = :inc WHERE p_id = :p_id";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':inc', $count);
    $stmt->bindParam(':p_id', $p_id);
    return $stmt->execute();
     
  }
  
  public function setPostFlag($u_id, $p_id, $flag) {  
    $table = "post_upvote";
    $sql = "INSERT INTO $table (u_id, p_id, upvote) VALUES (:u_id, :p_id, :upvote)";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':u_id', $u_id);
    $stmt->bindParam(':p_id', $p_id);
    $stmt->bindParam(':upvote', $flag);
    $stmt->execute();
  }
  
  public function getUpvoteFlag($user_id, $post_id) {
    $table = "post_upvote";
    $sql = "SELECT upvote FROM $table WHERE u_id = :u_id && p_id = :p_id";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':u_id', $user_id);
    $stmt->bindParam(':p_id', $post_id);
    return $stmt->execute();
  }
  
  public function updateCommentCount($p_id) {
    // Get comment count so we can increment it and send to DB
    $count = getCommentCount($p_id);
    
    // Increment retrieved upvote
    $count += 1;
    
    $table = "post";
    $sql = "UPDATE $table SET comments = :inc WHERE p_id = :p_id";
    
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':inc', $count);
    $stmt->bindParam(':p_id', $p_id);
    
    $stmt->execute();
  }
  
  public function getCommentCount($p_id) {
    $table = "post";
    $sql = "SELECT comments FROM $table WHERE p_id = :p_id";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':p_id', $p_id);
    return $stmt->execute();
  }
  
  public function createPostWImage() {
    $table = "post";
    $sql = "INSERT INTO $table(u_id, user_name, title, avatar, photo_url, photo_cred, image, file_size, entry, display, mime_type) " . 
      "VALUES(:user_id, :user_name, :title, :avatar, :photo_url, :photo_cred, :image_flag, :file_size, :entry, :display, :mime)";
    
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':user_id', $this->user_id);
    $stmt->bindParam(':user_name', $this->user_name);
    $stmt->bindParam(':display', $this->display);
    $stmt->bindParam(':title', $this->title);
    $stmt->bindParam('avatar', $this->avatar);
    $stmt->bindParam(':photo_url', $this->photo_url);
    $stmt->bindParam(':photo_cred', $this->photo_cred);
    $stmt->bindParam(':image_flag', $this->image_flag);
    $stmt->bindParam(':file_size', $this->file_size);
    $stmt->bindParam(':entry', $this->entry);
    $stmt->bindParam(':mime', $this->mime_type);
    
    return $stmt->execute();
  }
  
  public function createPostWVideo() {
    $table = "post";
    $sql = "INSERT INTO $table(u_id, user_name, title, avatar, video_url, video_mp4, thumbnail, video, file_size, entry, display, mime_type) " . 
      "VALUES(:user_id, :user_name, :title, :avatar, :video_url, :video_mp4, :thumbnail, :video_flag, :file_size, :entry, :display, :mime)";
    
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':user_id', $this->user_id);
    $stmt->bindParam(':user_name', $this->user_name);
    $stmt->bindParam(':title', $this->title);
    $stmt->bindParam(':avatar', $this->avatar);
    $stmt->bindParam(':display', $this->display);
    $stmt->bindParam(':video_url', $this->video_url);
    $stmt->bindParam(':video_mp4', $this->video_mp4_url);
    $stmt->bindParam(':thumbnail', $this->thumbnail);
    $stmt->bindParam(':video_flag', $this->video_flag);
    $stmt->bindParam(':file_size', $this->file_size);
    $stmt->bindParam(':entry', $this->entry);
    $stmt->bindParam(':mime', $this->mime_type);
    
    return $stmt->execute();
  }
  
  public function createPost() {
    $table = "post";
    $sql = "INSERT INTO $table(u_id, title, entry) VALUES(:user, :title, :entry)";
    
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':user', $this->user_id);
    $stmt->bindParam(':title', $this->title);
    $stmt->bindParam(':entry', $this->entry);
    
    return $stmt->execute();
  }
  
  public function insertPostWImage($user_id, $post_title, $avatar, $image_url, $image_size, $post_entry, $mime_type, $char_count, $word_count) {
    $table = "post";
    $sql = "INSERT INTO $table(u_id, title, avatar, photo_url, file_size, entry, mime_type, entry_char_count, entry_word_count) " . 
      "VALUES(:user, :title, :avatar, :photo_url, :file_size, :entry, :mime, :entry_char_count, :entry_word_count)";
      
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':user', $user_id);
    $stmt->bindParam(':title', $post_title);
    $stmt->bindParam(':avatar', $avatar);
    $stmt->bindParam(':photo_url', $photo_url);
    $stmt->bindParam(':file_size', $image_size);
    $stmt->bindParam(':entry', $post_entry);
    $stmt->bindParam(':mime', $mime_type);
    $stmt->bindParam(':entry_char_count', $char_count);
    $stmt->bindParam(':entry_word_count', $word_count);
    
    return $stmt->execute();  
  }
  
  public function insertPostWurl($user_id, $user_name, $avatar, $post_title, $post_entry, $char_count, $word_count, $display, $external_url, $external_url_flag) {
    $table = "post";
    $sql = "INSERT INTO $table(u_id, user_name, avatar, title, entry, entry_char_count, entry_word_count, display, external_url, external) VALUES(:user, :user_name, :title, :entry, :char_count, :word_count, :display, :external_url, :external_url_flag)";
    
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':user', $user_id);
    $stmt->bindParam(':user_name', $user_name);
    $stmt->bindParam(':title', $post_title);
    $stmt->bindParam(':avatar', $avatar);
    $stmt->bindParam(':display', $display);
    $stmt->bindParam(':entry', $post_entry);
    $stmt->bindParam(':char_count', $char_count);
    $stmt->bindParam(':word_count', $word_count);
    $stmt->bindParam(':external_url', $external_url);
    $stmt->bindParam(':external_url_flag', $external_url_flag);
    
    
    return $stmt->execute();
  }
  
  public function insertPost($user_id, $user_name, $avatar, $post_title, $post_entry, $char_count, $word_count, $display) {
    $table = "post";
    $sql = "INSERT INTO $table(u_id, user_name, avatar, title, entry, entry_char_count, entry_word_count, display) VALUES(:user, :user_name, :title, :avatar, :entry, :char_count, :word_count, :display)";
    
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':user', $user_id);
    $stmt->bindParam(':user_name', $user_name);
    $stmt->bindParam(':title', $post_title);
    $stmt->bindParam(':avatar', $avatar);
    $stmt->bindParam(':display', $display);
    $stmt->bindParam(':entry', $post_entry);
    $stmt->bindParam(':char_count', $char_count);
    $stmt->bindParam(':word_count', $word_count);
    
    return $stmt->execute();
  }
  
  /**
   * insert blob into the post table
   * @param string $filePath
   * @param string $mime mimetype
   * @return bool
   * first, open the file for reading in binary mode
   * second, construct an INSERT statement
   * third, bind the file handle to the prepared statement using the 
   * bindParam() method and call the execute() method to execute the
   * query. Also, notice that the PDO::PARAM_LOB instructs the PDO to 
   * map the data as a stream.
   */
  
  public function insertPhotoBlob($filePath, $mime) {
    $blob = fopen($filePath, 'rb');
    
    $sql = "INSERT INTO post(mime_type, photo_url) VALUES(:mime, :data)";
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':mime', $mime);
    $stmt->bindParam(':data', $blob, PDO::PARAM_LOB);
    
    return $stmt->execute();
  }
  
  public function insertVideoBlob($filePath, $mime) {
    $blob = fopen($filePath, 'rb');
    
    $sql = "INSERT INTO post(mime_type, video_url) VALUES(:mime, :data)";
    $stmt = $this->db->prepare($sql);
    
    $stmt = bindParam(':mime', $mime);
    $stmt = bindParam(':data', $blob, PDO::PARAM_LOB);
    
    return $stmt->execute();
  }


}

?>