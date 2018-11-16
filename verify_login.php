<?php
/**
 * Start the session
 */
session_start();

/** 
  * Include our database connection.
  */
require ("php_inc/inc_db_qp4.php");

$errorCount = 0;
$email = "";
$password = "";
$errEmail = "";
$errPassword = "";
$user_id = "";
$username = "";
$result = "";

function redisplayForm($email, $errEmail, $errPassword) {
?>
  <div id="unauth_message" class="w3-center">
    <p> 
      Only Adminb is allowed beyond this point at this time.<br>
      Please leave this page.
    </p>
  </div>
  
  <form name="loginForm" method="post" action="verify_login.php">
    <div class="form-group">
      <label for="emailLogin">Email address</label>
      <input type="email" name="email" class="form-control" id="emailLogin"
        aria-describedby="emailHelp" value="<?php echo $email; ?>" placeholder="email">
      <?php echo "<p class='text-danger'>$errEmail</p>"; ?>
      <small id="emailHelp" class="form-text text-muted">We'll never share your email with
        anyone else.
      </small>
    </div>          
  
    <div class="form-group">
      <label for="passwordLogin">Password</label>
      <input type="password" name="password" class="form-control" id="passwordLogin"
        placeholder="">
      <?php echo "<p class='text-danger'>$errPassword</p>"; ?>
    </div>
  
    <div class="form-check">
      <input type="checkbox" class="form-check-input" id="rememberMe">
      <label class="form-check-label" for="rememberMe">Remember me</label>
    </div>
  
    <button type="submit" name="submit" class="btn btn-primary">Login</button>
  </form>
<?php
}

if (isset($_POST['submit'])) {
  
  if (empty($_POST['email'])) {
    ++$errorCount;
    $errEmail = "email field cannot be empty";
    $email = "";
  } else {
    $email = trim($_POST['email']);
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
  
}

if ($errorCount > 0) {
  redisplayForm($email, $errEmail, $errPassword);
}
  
if ($errorCount == 0) {
  if ($conn !== FALSE) {
    // $tableName = "user";
    $sql = 'SELECT u_id, user_name FROM user WHERE email = :email AND password = :password';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    if (!$stmt->rowCount() > 0) {
      // inform user of invalid credentials and redisplay form
      // echo "<p>no results</p>";
      $result = "<p>Invalid email/password combination</p>";
      redisplayForm($email, $errEmail, $errPassword);
      // header("Location: Login.php");
    } else {
      while ($row = $stmt->fetch()) {
        $user_id = "{$row['u_id']}";
        $username = "{$row['user_name']}";
        
        // Add username to localstorage
        /*
         * $user = new User();
         * $user->setUsername($username);
         * $myJSON = json_encode($username);
        */
     
        
        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $username;
        $_SESSION['logged_in'] = time();
        header("Location: home.php");
        exit;
      }
    }
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
<!-- Last modified: 01/28/2018 -->
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
<title>Login</title>
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
<?php include("php_inc/inc_footer.php"); ?>



