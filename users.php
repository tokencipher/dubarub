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
              logged_in_user_id: 1, 
              rendered_user_id: u_id,
              avatar: avatar,
              user_name: user_name,
              bio: bio
            }  
          }).done(function ( data ) {
            console.log('follow status determination made...');
            console.log('rendered user id: ' + data.rendered_user_id);
            var follow_status = Boolean(data.follow_status);
            
            console.log('javascript converted follow status to: ' + follow_status);
            
            if (follow_status) {
              var user = $('<div id="user' + data.rendered_user_id + '" ' + 
		      'class="flex_item"><div id="bio_avi"><img id="avatar" src="' + data.avatar +
		      '" width="65" height="65"></img></div><div id="bio_username_container">' + 
		      '<div id="bio_username">' + data.user_name + '</div></div>' + 
		      '<div id="bio_action_container">' +
		      '<i id="direct_message" class="fa fa-paper-plane action_items" ' + 
		      'aria-hidden="true"></i></div><div id="bio_text_container">' + 
		      '<p id="bio_text">' + data.bio + '</p></div>' + 
		      '<div id="unfollow_button_container"><a href="#" onclick="unfollow()" ' +
		      'class="btn btn-primary btn-sm btn-block active" role="button" ' + 
		      'aria-pressed="true">Unfollow</a></div></div>');
            } else {
              var user = $('<div id="user' + data.rendered_user_id + '" ' + 
		      'class="flex_item"><div id="bio_avi"><img id="avatar" src="' + data.avatar +
		      '" width="65" height="65"></img></div><div id="bio_username_container">' + 
		      '<div id="bio_username">' + data.user_name + '</div></div>' + 
		      '<div id="bio_action_container">' +
		      '<i id="direct_message" class="fa fa-paper-plane action_items" ' + 
		      'aria-hidden="true"></i></div><div id="bio_text_container">' + 
		      '<p id="bio_text">' + data.bio + '</p></div>' + 
		      '<div id="follow_button_container"><a href="#" onclick="follow()" ' + 
		      'class="btn btn-primary btn-sm btn-block active" role="button" ' +
		      'aria-pressed="true">Follow</a></div></div>');
            }
	  
		    var flexContainer = $('.flex_container');
		    user.appendTo(flexContainer);
		    
          }).fail(function ( xhr, textStatus) {
            console.log('xhr status: ' + textStatus.statusText);
          });  
               
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