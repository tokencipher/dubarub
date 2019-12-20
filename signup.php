<?php include('php_inc/inc_header.php'); ?>
<style>
  form {
    position:relative;
    top:10px;
    margin:auto;
    width:50%;
  }
  #register{
    margin-bottom:40px;
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

<div style="position:relative;margin:auto" id="logo_container" class="w3-center">
  <img src="img/dubarub.jpg" alt="dubarub" id="place_logo" height="80" width="80" />   
</div>

<div class="w3-center" style="font-size:18px;">Let's get started!</p></div>


<form action="verify_signup.php" method="POST" enctype="multipart/form-data">
  <div class="form-group">
    <input type="text" maxlength="30" name="username" class="form-control" id="username" aria-describedby="username_help" placeholder="username" required>
    <small id="username_help" class="form-text text-muted">Username cannot be longer than 30 characters.</small>
  </div>
   <div class="form-group">
    <input type="email" maxlength="50" name="email" class="form-control" id="email" aria-describedby="email_help" placeholder="email address" required>
    <small id="email_help" class="form-text text-muted">email address will be used for login.</small>
  </div>
   <div class="form-group">
    <input type="password" maxlength="70" name="password" class="form-control" id="password" aria-describedby="password_help" placeholder="password" required>
    <!-- An element to toggle between password visibility -->
    <input type="checkbox" onclick="showPassword(document.getElementById('password'))">Show Password
  </div>
   <div class="form-group">
    <input type="password" maxlength="70" name="verify_password" class="form-control" id="verify_password" aria-describedby="password_help" placeholder="verify password" required>
    <!-- An element to toggle between password visibility -->
    <input type="checkbox" onclick="showPassword(document.getElementById('verify_password'))">Show Password
  </div>
  <ul>
    <li> Minimum length is 8 characters, maximum is 70.</li>
    <li> Must contain at least one number.</li>
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
  <button id="submit" type="submit" name="submit" class="btn btn-primary">Sign up</button>
</form>

<div style="position:relative;margin-top:10px;left:450px">
  Already a user? <a href="index.php" style="text-decoration:underline">Log in</a>
</div>

<?php include("php_inc/inc_footer.php"); ?>