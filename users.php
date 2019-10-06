<?php require_once('php_inc/inc_header.php'); ?>
  <style>
    .flex_container {
      display:flex;
      flex-wrap:wrap;
      justify-content:center;
      background-color:DodgerBlue;
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
    #bio_avi {
      position:relative;
      padding:4px;
      width:80px;
      height:80px;
      background:;
    }
    #avatar {
      position:relative;
      top:-4px;
      left:3px;
    }
    #bio_username_container {
      position:relative;
      top:-80px;
      left:88px;
      font-size:12px;
      width:100px;
      height:25px;
      line-height:24px;
      overflow:scroll;
      background:;
    }
    #bio_username {
      text-align:left;
    }
    #bio_action_container {
      position:relative;
      top:-55px;
      left:88px;
      width:28px;
      line-height:13px;
      height:25px;
      padding:2px;
      background:;
    }
    #direct_message {
      position:relative;
      float:left;
      font-size:20px;
      color:green;
      cursor:pointer;
    }
    #bio_text_container {
      position:relative;
      line-height:20px;
      top:-40px;
      height:55px;
      font-size:10px;
      padding:1px;
      text-align:justify;
      word-wrap:break-word;
      overflow:auto;
      background:;
    }
    #follow_button_container {
      position:relative;
      top:-40px;
      padding:10px;
    }
    #unfollow_button_container {
      position:relative;
      top:-40px;
      padding:10px;
      display:none;
    }
  </style>
</head>
<body class="flex_container">
<script>
  
  function loadUsers() {
    var usersRequest = new XMLHttpRequest();
    usersRequest.onreadystatechange = function() {
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
	  
		  var user = $('<div id="user' + obj[x].u_id + '" ' + 
		  'class="flex_item"><div id="bio_avi"><img id="avatar" src="' + obj[x].avatar +
		  '" width="65" height="65"></img></div><div id="bio_username_container">' + 
		  '<div id="bio_username">' + obj[x].user_name + '</div></div>' + 
		  '<div id="bio_action_container">' +
		  '<i id="direct_message" class="fa fa-paper-plane action_items" ' + 
		  'aria-hidden="true"></i></div><div id="bio_text_container">' + 
		  '<p id="bio_text">' + obj[x].bio + '</p></div>' + 
		  '<div id="follow_button_container"><a href="#" ' + 
		  'onclick="follow()" class="btn btn-primary btn-sm btn-block active" ' +
		  'role="button" aria-pressed="true">Follow</a></div> ' + 
		  '<div id="unfollow_button_container"><a href="#" onclick="unfollow()" ' +
		  'class="btn btn-primary btn-sm btn-block active" role="button" ' + 
		  'aria-pressed="true">Unfollow</a></div></div>'  
		  );
	  
		  var flexContainer = $('.flex_container');
		  user.appendTo(flexContainer);   
		  
	    }	   
	  }  
    };
    usersRequest.open("GET", "users_controller.php", true);
    usersRequest.send();

  }

  $(document).ready(function() {
    loadUsers();
  });
</script>
    
</body>
</html>