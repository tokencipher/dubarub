<?php session_start(); 
if (!isset($_SESSION['user_id']) && !isset($_SESSION['logged_in'])) {
  // User not logged in. Redirect them back to the login.php page.
  header('Location: index.php');
  exit;
}
?>
<?php require_once('php_inc/inc_header.php'); ?>
  <title>dubarub | Users</title>
  <style>
    .flex_container {
      display:flex;
      flex-wrap:wrap;
      justify-content:center;
    }
    .flex_item {
      background-color:#f1f1f1;
      height:200px;
      border-style:solid; 
      border-width:2px;
      width:200px;
      max-width:335px;
      margin:10px;
      text-align:center;
      line-height:75px;
      font-size:30px;
    }
    .bio_avi {
      position:relative;
      padding:4px;
      width:80px;
      height:80px;
      background:;
    }
    .avatar {
      position:relative;
      top:-4px;
      left:3px;
    }
    .bio_username_container {
      position:relative;
      top:-80px;
      left:88px;
      font-size:12px;
      width:100px;
      height:25px;
      line-height:24px;
      overflow:scroll;
    }
    .bio_username {
      text-align:left;
      font-size:12px;
    }
    .bio_action_container {
      position:relative;
      top:-67px;
      left:88px;
      width:28px;
      line-height:13px;
      height:25px;
      padding:2px;
      color:green;
      font-size:20px;
    }
    .direct_message {
      position:relative;
      top:5px;
      left:30px;
      font-size:20px;
      color:green;
      cursor:pointer;
      background:red;
    }
    .bio_text_container {
      position:relative;
      line-height:20px;
      top:-40px;
      height:55px;
      width:100%;
      font-size:10px;
      padding:1px;
      text-align:justify;
      word-wrap:break-word;
      overflow:auto;
      background:gold;
    }
    .follow_button_container {
      position:relative;
      top:-40px;
      padding:10px;
      cursor:pointer;
    }
    .unfollow_button_container {
      position:relative;
      top:-40px;
      padding:10px;
      cursor:pointer;
    }
  </style>
<?php require_once ('php_inc/inc_user_nav.php'); ?>
</head>
<body>
<script>
  var loggedInUserId = "<?php $_SESSION['user_id']; ?>";
  
  function loadFollowers() {
    var followersRequest = new XMLHttpRequest();
    followersRequest.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
	    var obj = JSON.parse(this.responseText);
	    var usersCnt = obj.length;
	
	    /*
	    if (usersCnt == 0) {
		  var noUsers = $('<p class="noUsers" class="w3-center">No users to retrieve...</p>');
		  var mostRecentStatus = $('#status_history_container').first();
		  noStatus.prependTo(mostRecentStatus);
		  return;
	    }
	    */
	
	    // Clear the loading message before populating
	    if (usersCnt > 0) {
		  //$('#status_history_container').empty();
	    }

	    for ( var x = 0; x < usersCnt; x++ ) {
	    
	      var u_id = obj[x].u_id;
	      var avatar = obj[x].avatar;
	      var user_name = obj[x].user_name;
	      var bio = obj[x].bio;
	      
	      /*
	      console.log('rendered user id: ' + u_id);
	      console.log('rendered user id is of type: ' + typeof u_id );
	      */
	    
	      $.ajax({
            async: true,
            cache: false,
            url: 'determine_follow_status.php',
            dataType: 'json',  
            type: 'POST',
            data: { 
              logged_in_user_id: loggedInUserId, 
              rendered_user_id: u_id,
              avatar: avatar,
              rendered_user_name: user_name,
              bio: bio
            }  
          }).done(function ( data ) {
            console.log('follow status determination made...');
            console.log('rendered user id: ' + data.rendered_user_id);
            var follow_status = Boolean(data.follow_status);
            
            console.log('javascript converted follow status to: ' + follow_status);
            
            if (follow_status) {
              var user = $('<div id="user' + data.rendered_user_id + '" ' + 
		      'class="flex_item"><div class="bio_avi"><a href="user.php?name=' + data.rendered_user_name + '"><img class="avatar" src="' + data.avatar +
		      '" width="65" height="65"></img></a></div><div class="bio_username_container">' + 
		      '<div class="bio_username"><b><a href="user.php?name=' + data.rendered_user_name + '">' + data.rendered_user_name + '</a></b></div></div>' + 
		      '<div class="bio_action_container">' +
		      '<i onclick="displayMessageModal()" class="fa fa-paper-plane action_items" ' + 
		      'aria-hidden="true"></i></div><div class="bio_text_container">' + 
		      '<p class="bio_text">' + data.bio + '</p></div>' + 
		      '<div class="unfollow_button_container"><a onclick="unfollow(this)" ' +
		      'class="btn btn-primary btn-sm btn-block active" role="button" ' + 
		      'aria-pressed="true" data-rendered-user-name="' + data.rendered_user_name + '" data-rendered-user-id="' + data.rendered_user_id + '">Unfollow</a></div></div>');
            } else {
              var user = $('<div id="user' + data.rendered_user_id + '" ' + 
		      'class="flex_item"><div class="bio_avi"><a href="user.php?name=' + data.rendered_user_name + '"><img class="avatar" src="' + data.avatar +
		      '" width="65" height="65"></a></img></div><div class="bio_username_container">' + 
		      '<div class="bio_username"><b><a href="user.php?name=' + data.rendered_user_name + '">' + data.rendered_user_name + '</a></b></div></div>' + 
		      '<div class="bio_action_container">' +
		      '<i onclick="displayMessageModal()" class="fa fa-paper-plane action_items" ' + 
		      'aria-hidden="true"></i></div><div class="bio_text_container">' + 
		      '<p class="bio_text">' + data.bio + '</p></div>' + 
		      '<div class="follow_button_container"><a onclick="follow(this)" ' + 
		      'class="btn btn-primary btn-sm btn-block active" role="button" ' +
		      'aria-pressed="true" data-rendered-user-name="' + data.rendered_user_name + '" data-rendered-user-id="' + data.rendered_user_id + '">Follow</a></div></div>');
            }
	  
		    var flexContainer = $('.flex_container');
		    user.appendTo(flexContainer);
		    
          }).fail(function ( xhr, textStatus) {
            console.log('xhr status: ' + textStatus.statusText);
          });  
               
	    }	   
	  }  
    };
    followersRequest.open("GET", "followers_controller.php", true);
    followersRequest.send();

  }
  
  function displayMessageModal() {
    document.getElementById('message_modal').style.display = 'block';
  }
  
  function follow(elem) {
    console.log("Follow button clicked");
    var logged_in = Boolean("<?php echo (isset($_SESSION['user_id']) ? true : false); ?>");
    	
    if (logged_in === true) {	
	  var action = "Follow";
	  var renderedUserId = $(elem).data("rendered-user-id");
	  var renderedUserName = $(elem).data("rendered-user-name");
	  
	  $(elem).text("Unfollow");
	  $(elem).parent().attr("class", "unfollow_button_container");
	  $(elem).attr("onclick", "unfollow(this)");	
	  
	
	  $.ajax({
        async: true,
      	cache: false,
        url: 'user_action.php',  
        type: 'POST',
        data: {
          user_action: action, 
          rendered_user_id: renderedUserId, 
          rendered_user_name: renderedUserName
        }  
      }).done(function (msg) {
        console.log('Follow action taken...');
        console.log(msg);
      }).fail(function (xhr, textStatus) {
        console.log(xhr.statusText);
      });   
    } else {
      if (confirm("You must be logged in to follow this user. Sign up/Login?")) {
  	    window.location.assign("https://dubarub.com");
	  } else {
  		return;
	  }
    }       
  }
  
  function unfollow(elem) {
    console.log("Unfollow button clicked");
    var logged_in = Boolean("<?php echo (isset($_SESSION['user_id']) ? true : false); ?>");
    
    if (logged_in === true) {
      var action = "Unfollow";
      var renderedUserId = $(elem).data("rendered-user-id");
      var renderedUserName = $(elem).data("rendered-user-name");
      
	  $(elem).text("Follow");
	  $(elem).parent().attr("class", "follow_button_container");
	  $(elem).attr("onclick", "follow(this)");
      
      
      $.ajax({
        async: true,
        cache: false,
        url: 'user_action.php',
        type: 'POST',
        data: {
          user_action: action, 
          rendered_user_id: renderedUserId,
          rendered_user_name: renderedUserName
        }
      }).done(function (msg) {
        console.log("Unfollow action taken");
        console.log(msg);
      }).fail(function (xhr, textStatus) {
        console.log(xhr.statusText);
	  });
    } else {
      if (confirm("You must be logged in to unfollow this user. Sign up/Login?")) {
        window.location.assign("https://dubarub.com");
      } else {
        return;
      }
    }
  }
  

  $(document).ready(function() {
    loadFollowers();
  });
</script>

<!-- direct message dialog -->
<div id="message_modal" class="w3-modal">
<div class="w3-modal-content w3-card-4 w3-anmiate-zoom">
  <header class="w3-container w3-blue">
	<span onclick="document.getElementById('message_modal').style.display = 'none'"
	class="w3-button w3-red w3-display-topright">&times;</span>
	<h2>Send message</h2>
  </header>

  <div class="w3-center"><br>
	<form class="w3-container" method="post">
	  <div class="form-group">
		<div><i class="w3-xxlarge fa fa-paper-plane" style="color:#339966;"></i></div>
		<!--<label for="post_title">Enter caption of new post</label>-->
		Subject: <input type="text" maxlength="140" name="message_subject" class="form-control" id="message_subject" aria-describedby="subject_help" placeholder="" />
		<small id="subject_help" class="form-text text-muted">Subject cannot be any longer than 140 characters.</small>
	  </div>
	  <div class="form-group">
		<!-- <label for="bio_edit">Edit bio</label> -->
		Body: <textarea id="message_body" name="message_body" class="form-control" placeholder="" rows="3" maxlength="" required></textarea>
		<br>
	  </div>
	</form>
	
	<div id="message_submit_container">
	  <button id="message_submit" class="btn btn-primary w3-margin-bottom">Send</button>
	</div>
  </div>
  
  <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
	<button onclick="document.getElementById('message_modal').style.display='none'"
	type="button" class="w3-button w3-left w3-border w3-red">Cancel</button>
  </div>
</div>
</div>
<!-- end direct message dialog -->

<div class="flex_container"></div>
    
<?php require_once("php_inc/inc_user_footer.php"); ?>