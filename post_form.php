<?php

// home.php

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

<?php include('php_inc/inc_header.php'); ?>
<title>dubarub | Create a post</title>
<style>
  form {
    position:relative;
    top:30px;
    margin:auto;
    width:50%;
  }
  #upload{
    margin-bottom:40px;
  }
</style>
</head>

<body>

<?php include("php_inc/inc_user_nav.php"); ?>

<script>

  //const fileInput = document.getElementById('post_multimedia');
  /*
  document.getElementById("post_multimedia").addEventListener("change", function(e) {
    alert('changed');
    /*
     if (e.target.files[0].type.match(/^image\//)) {
      var file = URL.createObjectURL(files[0]);
      alert('image'); 
    
  });
  */
// Example starter JavaScript for disabling form submissions if there are invalid fields
/*
(function() {
  'use strict';
  window.addEventListener('load', function() {
    form.addEventListener('submit', function(event) {
      if (form.checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
      }
    });
  });
})();
*/

</script>

<form action="post_upload.php" method="POST" enctype="multipart/form-data">
  <div class="form-group">
    <label for="post_title">Enter caption of new post</label>
    <input type="text" maxlength="80" name="post_title" class="form-control" id="post_title" aria-describedby="title_help" placeholder="Title" required>
    <small id="title_help" class="form-text text-muted">Title cannot be any longer than 80 characters.</small>
  </div>
  <div class="form-group">
    <label for="post_multimedia">Add image/video</label>
    <!--<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />-->
    <!--<input type="hidden" name="user_id" value="1" />-->
    <input type="file" name="new_file" class="form-control-file" id="post_multimedia" aria-describedby="multimedia_help">
    <!--<small id="multimedia_help" class="form_text text-muted">File cannot be any larger than 10MB</small>-->
  </div>
  <div>-Or-</div><br>
  <div class="form-group">
    <label for="photo_credit">Paste external URL</label>
    <input type="text" maxlength="200" name="external_url" class="form-control" id="external_url" aria-describedby="url_help" placeholder="External URL">
    <small id="url_help" class="form-text text-muted">Example youtube url: https://youtube.com/watch?v=dsu4XY4QNB0</small>
  </div>
  <div class="form-group">
    <label for="photo_credit">Enter photo credit for image (optional)</label>
    <input type="text" maxlength="50" name="photo_cred" class="form-control" id="photo_credit" aria-describedby="title_help" placeholder="Photo credit">
    <small id="photo_credit_help" class="form-text text-muted">Photo credit cannot be any longer than 50 characters.</small>
  </div>
  <div class="form-group">
    <label for="post_text">Now, let your mind flow freely</label>
    <textarea id="post_text" name="post_text" class="form-control" rows="3" maxlength="" required></textarea>
    <small id="post_text_help" class="form-text text-muted">Post text content cannot be any longer than 5000 characters.</small>
  </div>
  <div class="form-group">
    <label for="post_tags">Tags</label>
    <input type="text" maxlength="255" name="post_tags" class="form-control" id="post_tags" aria-describedby="tags_help" placeholder="Tags">
    <small id="tags_help" class="form-text text-muted"></small>
  </div>
  <br>
  <button id="upload" type="submit" name="upload" class="btn btn-primary">Add post</button>
</form>

<?php include("php_inc/inc_footer.php"); ?>
