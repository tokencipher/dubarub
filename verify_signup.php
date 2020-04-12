<?php

/** 
  * Include our database connection.
  */
require("php_inc/inc_db_qp4.php");

require_once("php_class/class_Session.php");
require_once("php_class/class_User.php");
require_once("php_class/class_Postmaster.php");

$errorCount = 0;
$username = "";
$email = "";
$password = "";
$verifiedPassword = "";
$errEmail = "";
$errPassword = "";
$errVerifyPassword = "";
$errUsername = "";
$errAvatar = "";
$user_id = "";
$result = "";

function redisplayForm($username, $email, $password, $verifiedPassword, $errUsername, $errEmail, $errPassword, $errVerifyPassword, $errAvatar) {
?>
  
  <div style="position:relative;margin:auto" id="logo_container" class="w3-center">
    <img src="img/dubarub.jpg" alt="dubarub" id="place_logo" height="80" width="80" />   
  </div>
  
  <div class="w3-center" style="font-size:18px;">Let's get started!</p></div>

  <form action="verify_signup.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <input type="text" maxlength="30" name="username" class="form-control" 
        id="username" aria-describedby="username_help" placeholder="username" 
        value="<?php echo $username; ?>"  required>
      <small id="username_help" class="form-text text-muted">username cannot be longer than 30 characters.</small>
      <?php echo "<p class='text-danger'>$errUsername</p>"; ?>
    </div>
    <div class="form-group">
      <input type="email" maxlength="50" name="email" class="form-control" 
      id="email" aria-describedby="email_help" placeholder="email address" 
      value="<?php echo $email; ?>"required>
      <small id="email_help" class="form-text text-muted">email address will be used for login.</small>
      <?php echo "<p class='text-danger'>$errEmail</p>"; ?>
    </div>
    <div class="form-group">
      <input type="password" maxlength="70" name="password" class="form-control" 
      id="password" aria-describedby="password_help" placeholder="password" 
      value="<?php echo $password; ?>" required>
      <input type="checkbox" onclick="showPassword(document.getElementById('password'))">
      <?php echo "<p class='text-danger'>$errPassword</p>"; ?>
    </div>
    <div class="form-group">
      <input type="password" maxlength="70" name="verify_password" class="form-control" 
      id="verify_password" aria-describedby="password_help" placeholder="verify password" 
      value="<?php echo $verifiedPassword; ?>" required>
      <input type="checkbox" onclick="showPassword(document.getElementById('verify_password'))">
      <?php echo "<p class='text-danger'>$errVerifyPassword</p>"; ?>
    </div>
    <ul>
      <li>Minimum length is 8 characters, maximum is 70.</li>
      <li>Must contain at least one number.</li>
      <li>Must contain at least one special character (e.g. !, @, #, $ etc.) -- spaces do not count.</li>
      <li>Cannot contain your username.</li>
    </ul>
    <div class="form-group">
      <label for="avatar">Choose an avatar</label>
      <!--<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />-->
      <!--<input type="hidden" name="user_id" value="1" />-->
      <input type="file" name="avatar" class="form-control-file" id="avatar" aria-describedby="avatar_help">
      <!--<small id="multimedia_help" class="form_text text-muted"><?php echo $errAvatar; ?></small>-->
      <?php echo "<p class='text-danger'>$errAvatar</p>"; ?>
    </div>
  
    <button type="submit" name="submit" class="btn btn-primary">Sign up!</button>
  </form>
  
  <div style="position:relative;top:10px;left:450px">
    Already a user? <a href="index.php" style="text-decoration:underline">Log in</a>
  </div>
<?php
}

if (isset($_POST['submit'])) {
  $temp_file = $_FILES['avatar']['tmp_name'];
  $file_size = $_FILES['avatar']['size'];
  
  if (!empty($temp_file)) {
    $file_info_mime = new finfo(FILEINFO_MIME);
    $all_ext_mimeType = $file_info_mime->buffer(file_get_contents($temp_file));
    $mimeType_array = explode(";", $all_ext_mimeType);
  }
  
  if ($file_size !== 0) {
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
      
      default:
        $errAvatar = "sorry, only image files are allowed.";
        ++$errorCount;
    }
  }

  if (empty($_POST['username'])) {
    ++$errorCount;
    $errUsername = "username field cannot be empty";
    $username = "";
  } else {
    $username = trim($_POST['username']);
    $username = preg_replace('/\s+/', '', $username);
    $username = strtolower($username);
    $username = stripslashes($username);
    $username = htmlspecialchars($username);
    $errUsername = "";
  }
  
  if (empty($_POST['email'])) {
    ++$errorCount;
    $errEmail = "email field cannot be empty";
    $email = "";
  } else {
    $email = trim($_POST['email']);
    $email = strtolower($email);
    $email = stripslashes($email);
    $email = htmlspecialchars($email);
    $errEmail = "";
  }
  
  if (empty($_POST['password'])) {
    ++$errorCount;
    $errPassword = "password field cannot be empty<br />";
    $password = "";
  } else {
    $password = trim($_POST['password']);
    $password = stripslashes($password);
    $password = htmlspecialchars($password);
    $errPassword = "";
  }
  
  if (empty($_POST['verify_password'])) {
    ++$errorCount;
    $errVerifyPassword = "verify password field cannot be empty<br />";
    $verifiedPassword = "";
  } else {
    $verifiedPassword = trim($_POST['verify_password']);
    $verifiedPassword = stripslashes($verifiedPassword);
    $verifiedPassword = htmlspecialchars($verifiedPassword);
    $errVerifyPassword = "";
  }
  
  // invalidate username against special characters 
  $special_chars = $username;
  $special_chars = preg_match('/[@#$%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', $username);
  if ($special_chars) {
    ++$errorCount;
    $errUsername = "username cannot contain any special characters";
  } 
  
  
  if( strlen($password) < 8 ) {
    ++$errorCount;
    $errPassword .= "password too short<br />";
  }
 
  if( strlen($password) > 70 ) {
    ++$errorCount;
    $errPassword .= "password too long<br />";
  }
  
  if ( preg_match("/$username/i", $password ) ) {
    ++$errorCount;
    $errPassword .= "password cannot contain username<br />";
  }
 
  if( !preg_match("#[0-9]+#", $password ) ) {
    ++$errorCount;
    $errPassword .= "password must include at least one number<br />";
  }
 
  if( !preg_match("#[a-z]+#", $password ) ) {
    ++$errorCount;
    $errPassword .= "password must include at least one letter<br />";
  }
  
  if ($password !== $verifiedPassword) {
    ++$errorCount;
    $errVerifyPassword .= "passwords must match<br />";
  } else {
    $errVerifyPassword = "";
  }
  
}

if ($conn !== FALSE) {
  // $tableName = "user";
  $sql = 'SELECT user_name FROM user WHERE user_name = :username';
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':username', $username);
  $stmt->execute();
  if (!$stmt->rowCount() > 0) {
    $unique_user = true;
  } else {
    ++$errorCount;
    $unique_user = false;
    $errUsername = "this username has been taken.";
  }

  $sql = 'SELECT email FROM user WHERE email = :email';
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':email', $email);
  $stmt->execute();
  if (!$stmt->rowCount() > 0) {
    $unique_email = true;
  } else {
    ++$errorCount;
    $unique_email = false;
    $errEmail = "this email is already in use.";
  }
}

if ($errorCount == 0) {

  // Set password to md5 hash
  $password = md5($password);

  $avatar = isset($_POST['avatar']) ? $_POST['avatar'] : 'img/dubarub-01.png';

  // Create new user
  $User = new User();
  $User->setUsername($username);
  $User->setEmail($email);
  $User->setPassword($password);
  $User->setAvatar($avatar);
  $User->createUser();
  $Mailbox = new Postmaster();
  $Mailbox->setUsername($username);
  $Mailbox->createMailbox();
  
  header('Location: index.php');

}

if ($errorCount > 0) {
  redisplayForm($username, $email, $password, $verifiedPassword, $errUsername, $errEmail, $errPassword, $errVerifyPassword, $errAvatar);
}

  
?>
<!-- Author: Bryan Thomas -->
<!-- Last modified: 04/09/2020 -->
<?php require_once('php_inc/inc_header.php'); ?>
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
  
  * unvisited link */
  a:link {
    color: blue;
  }

  /* visited link */
  a:visited {
    color: green;
  }

  /* mouse over link */
  a:hover {
    color: green;
  }

  /* selected link */
  a:active {
    color: yellow;
  }
</style>
<script>
function showPassword(elem) {
  var x = elem;
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
} 
</script>
</head>
<body> 
<div id="result" class="w3-center" style="position:relative;top:100px;margin:auto;color:green;">
  <?php echo $result; ?>
</div>
</body>
<?php include_once('php_inc/inc_footer.php'); ?>



