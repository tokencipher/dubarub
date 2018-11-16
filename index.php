<!-- Author: Bryan Thomas -->
<!-- Last modified: 10/29/2018 -->

<?php

/**
 * Start the session
*/
session_start();

/**
 * Check if the user is logged in.
 */
if (isset($_SESSION['user_id']) && isset($_SESSION['logged_in'])) {
  // User is logged in. Redirect them back to the user_template page.
  header('Location: home.php');
  exit;
}

?>

<?php require("php_inc/inc_header.php"); ?>
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

  <div id="unauth_container class="w3-center">
    <div id="unauth_message" class="w3-center">
      <p>
        Only alpha testers are allowed beyond this point at this time.<br> 
      </p>
    </div>  
  </div>

  <form name="loginForm" method="post" action="verify_login.php">
    <div class="form-group">
      <label for="emailLogin">Email address</label>
      <input type="email" name="email" class="form-control" id="emailLogin" 
        aria-describedby="emailHelp" placeholder="Enter email">
      <!--<?php echo "<p class='text-danger'>$errEmail</p>"; ?>-->
      <small id="emailHelp" class="form-text text-muted">We'll never share your email with
        anyone else.</small>
    </div>
    
    <div class="form-group">
      <label for="passwordLogin">Password</label>
      <input type="password" name="password" class="form-control" id="passwordLogin"
        placeholder="Password">
      <!--<?php echo "<p class='text-danger'>$errPassword</p>"; ?>-->
    </div>
    
    <div class="form-check">
      <label class="form-check-label" for="rememberMe">Remember me</label>
      <input type="checkbox" class="form-check-input" id="rememberMe">
    </div>
    
    <button type="submit" name="submit" class="btn btn-primary">Login</button>    
  </form>
  
  <script>
  
    if ('serviceWorker' in navigator) {
      window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js').then(function(registration) {
          // Registration was successful
          console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function(err) {
          // registration failed 
          console.log('ServiceWorker registration failed: ', err);
        });
      });
    }
  
  </script>
  
<?php include("php_inc/inc_footer.php"); ?>


