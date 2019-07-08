<?php
/**
 * Start the session
 */
//session_start();

/** 
  * Include our database connection.
  */
require ("php_inc/inc_db_qp4.php");
require_once("php_class/class_Session.php");

$errorCount = 0;
$username = "";
$email = "";
$password = "";
$errEmail = "";
$errPassword = "";
$errUsername = "";
$user_id = "";
$result = "";

function redisplayForm($username, $email, $errUsername, $errEmail, $errPassword) {
?>
  
  <div style="position:relative;margin:auto" id="logo_container" class="w3-center">
    <img src="img/dubarub.jpg" alt="dubarub" id="place_logo" height="80" width="80" />   
  </div>
  
  <h2 class="w3-center" style="color:green;">Welcome to the Public BETA!</h2>

  <form action="verify_signup.php" method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <input type="text" maxlength="30" name="username" class="form-control" 
        id="username" aria-describedby="username_help" placeholder="username" 
        value="<?php echo $username; ?>"  required>
      <small id="username_help" class="form-text text-muted">Username cannot be longer than 30 characters.</small>
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
      value="<?php echo $username; ?>" required>
      <?php echo "<p class='text-danger'>$errPassword</p>"; ?>
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
      <!--<small id="multimedia_help" class="form_text text-muted">File cannot be any larger than 10MB</small>-->
    </div>
  
    <button type="submit" name="submit" class="btn btn-primary">Sign up!</button>
  </form>
<?php
}

if (isset($_POST['submit'])) {

  if (empty($_POST['username'])) {
    ++$errorCount;
    $errUsername = "username field cannot be empty";
    $username = "";
  } else {
    $username = trim($_POST['username']);
    $username = strtolower($username);
    $username = stripslashes($username);
    $username = htmlspecialchars($username);
    // $_SESSION['email'] = $email;
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
    // $_SESSION['email'] = $email;
    $errEmail = "";
  }
  
  if (empty($_POST['password'])) {
    ++$errorCount;
    $errPassword = "password field cannot be empty";
    $password = "";
  } else {
    $password = trim($_POST['password']);
    $password = stripslashes($password);
    $password = htmlspecialchars($password);
    $password = md5($password);
    // $_SESSSION['password'] = $password;
    $errPassword = "";
  }
  
  $username = preg_match('/[@#$%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', $_POST['username']);
  if ($username) {
    ++$errorCount;
    $errUsername = "Username cannot contain any special characters";
  }
  
  if( strlen($_POST['password']) < 8 ) {
    ++$errorCount;
    $errPassword .= "Password too short!";
  }
 
  if( strlen($_POST['password'] ) > 70 ) {
    ++$errorCount;
    $errPassword .= "Password too long!";
  }
 
  if( !preg_match("#[0-9]+#", $password ) ) {
    ++$errorCount;
    $errPassword .= "Password must include at least one number!";
  }
 
  if( !preg_match("#[a-z]+#", $password ) ) {
    ++$errorCount;
    $errPassword .= "Password must include at least one letter!";
  }
  
}

if ($errorCount > 0) {
  redisplayForm($username, $email, $errUsername, $errEmail, $errPassword);
}
  
if ($errorCount == 0) {
  if ($conn !== FALSE) {
    // $tableName = "user";
    $sql = 'SELECT user_name FROM user WHERE username = :username';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    if (!$stmt->rowCount() > 0) {
      $unique_user = true;
    } else {
      ++$errorCount;
      $unique_user = false;
      $errUsername = "That username has been taken.";
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
      $errEmail = "That email is already in use.";
    }
  }
  
  if ($unique_user && $unique_email) {
    
    // Set arguments
    $username = trim($_POST['username']);
    $username = strtolower($username);
    $username = stripslashes($username);
    $username = htmlspecialchars($username);
    
    $email = trim($_POST['email']);
    $email = strtolower($email);
    $email = stripslashes($email);
    $email = htmlspecialchars($email);
    
    $password = trim($_POST['password']);
    $password = stripslashes($password);
    $password = htmlspecialchars($password);
    $password = md5($password);
    
    // Create new user
    $User = new User();
    $User->setUsername($username);
    $User->setEmail($email);
    $User->setPassword($password);
    $User->setAvatar($avatar);
    $User->createUser();
  }  
}

/*
echo "<p>$user_id</p>";
echo "<p>$username</p";
echo "<p>$errEmail</p>";
echo "<p>$errPassword</p>";
echo "<p>$email</p>";
echo "<p>$password</p>";
*/
  
?>
<!-- Author: Bryan Thomas -->
<!-- Last modified: 07/07/2019 -->
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
</style>
</head>
<body> 
<div id="result" class="w3-center" style="position:relative;top:100px;margin:auto;color:red;">
  <?php echo $result; ?>
</div>
</body>
<?php include_once('php_inc/inc_footer.php'); ?>



