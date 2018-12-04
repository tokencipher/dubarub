<?php

// home.php

/**
 * Start the session.
 */
session_start();

/**
* for a 30 minute timeout, specified in seconds
*/

/** 
* Here we look for the user's LAST_ACTIVITY timestamp. If 
* it's set and indicates our $timeout_duration has passed,
* end the previous session .
*/

ini_set( 'display_errors', 1 ); 
error_reporting( E_ALL );

/**
 * Check if the user is logged in.
 */
 
if (!isset($_SESSION['user_id']) && !isset($_SESSION['logged_in'])) {
  // User not logged in. Redirect them back to the login.php page.
  header('Location: index.php');
  exit;
}
?>

<?php require_once ('php_inc/inc_header.php'); ?>
<?php require_once ('php_class/class_Status.php'); ?>
<?php require_once ('php_class/class_Playlist.php'); ?>
<?php require_once ('php_class/class_Post.php'); ?>
<?php require_once ('php_class/class_User.php'); ?>
<?php require_once ('php_inc/inc_db_qp4.php'); ?>
<title>dubarub | Home</title>
  <style>
    .music-tab {
      display:none;
    }
    .jp-gui {
	  position: fixed;
      left: 885px!important;
      bottom: 75px!important;
      width: 30%;
	  opacity: 0.6;
	  background: #f34927;
	  background: -moz-linear-gradient(top,  #f34927 0%, #dd3311 100%);
	  background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#f34927), color-stop(100%,#dd3311));
	  background: -webkit-linear-gradient(top,  #f34927 0%,#dd3311 100%);
	  background: -o-linear-gradient(top,  #f34927 0%,#dd3311 100%);
	  background: -ms-linear-gradient(top,  #f34927 0%,#dd3311 100%);
	  background: linear-gradient(to bottom,  #f34927 0%,#dd3311 100%);
	  filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f34927', endColorstr='#dd3311',GradientType=0 );
	  -webkit-box-shadow:  0px 1px 1px 0px rgba(0, 0, 0, .1);    
      box-shadow:  0px 1px 1px 0px rgba(0, 0, 0, .1);
      border-radius: 3px;
	  overflow: hidden;
	  margin-top: 10px;
    }
    .jp-title {
      position: fixed;
      text-align: center;
      font-size: 12px;
      margin-left: 55px;
      left: 930px;
      bottom: 125px;
      width:18%;
      text-align:center;
      color: #999;
    }
    #cover_art_container {
      position:fixed;
      border-style:solid;
      right:10px;
      height:290px;
      top:215px;
      width:335px;
    }
    #profile_bio_container {
      position:fixed;
      border-style:solid;
      left:10px;
      height:290px;
      top:81px;
      max-width:335px;
    }
    #m_profile_bio_container {
      position:relative;
      padding:10px;
      margin:auto;
      border-style:solid;
      width:95%;
      height:300px;
    }
    #new_status_container {
      position:fixed;
      overflow:scroll;
      border-style:dashed;
      padding:5px;
      width:330px;
      max-width:330px;
      height:115px;
      right:13px;
      top:80px;
      background-color:white;
    }
    #status_container {
      position:fixed;
      overflow:scroll;
      border-style:dashed;
      padding:5px;
      width:330px;
      max-width:330px;
      height:115px;
      left:13px;
      top: 383px;
      background-color:white;
    }
    #status_time {
      position:relative;
      left:-8px;
    }
    #m_status_container {
      position:relative;
      top:27px;
      margin:auto;
      left:1px;
      overflow:scroll;
      border-style:dashed;
      padding:5px;
      width:330px;
      max-width:330px;
      height:60px;
      background-color:white;
    }
    #m_status_date {
      position:relative;
      top:32px;
      left:185px;
    }
    #status_date {
      position:fixed;
      left:170px;
      top:500px;
      text-align:right;   
    }
    #flashback {
      position:relative;
      float:left;
      cursor:pointer;
      padding:4px;
      left:-37px!important;
      color:gold;
    }
    #m_flashback {
      position:relative;
      float:left;
      cursor:pointer;
      top:10px;
      padding:4px;
      left:150px!important;
      color:gold!important;
    }
    .m_action_items {
      position:relative;
      float:left;
      cursor:pointer;
      top:10px;
      padding:3px;
    }
    #m_bio_edit_icon {
      position:relative;
      left:8px;
      color:#ffcc66;
    }
    #m_direct_message {
      position:relative;
      left:21px;
      color:#339966;
    }
    #m_settings {
      position:relative;
      left:36px;
      color:#cc6600;
    }
    .action_items {
      position:relative;
      cursor:pointer;
      top:-4px;
      padding:3px;
    }
    #bio_edit_icon {
      position:relative;
      left:5px;
      color:#ffcc66;
    }
    #direct_message {
      position:relative;
      left:18px;
      color:#339966;
    }
    #settings {
      position:relative;
      left:33px;
      color:#cc6600;
    }
    #up_shortcut {
      position:fixed;
      top:375px;
      right:5px;
    }
    #close_flashback {
      display:none;
    }
    /* Phone portrait */
    @media (max-width: 481px) {
      #clear {display:none!important;}
    }
  </style>
<?php include_once ('php_inc/inc_user_home_nav.php'); ?>
</head>
<body>

<?php

  if (isset($_GET['action'])) {
     
    switch ( $_GET['action'] ) {
     
      case 'Delete Post':
        $p_id = $_GET['pid'];
        $post_delete = new Post();
        $post_delete->deletePost($p_id);
        break;
        
      case 'Delete Status':
        $stat_id = $_GET['statid'];
        $status_delete = new Status();
        $status_delete->deleteStatus($stat_id);
        break;
    }
  }   
?>

<script>

  function performAsync(message, callback) {
    return new Promise( (resolve, reject) => {
      setTimeout(
        () => {
          console.log(message);
          resolve();
        }, 100
      );
    });
  }
  
  function sequenceAsync() {
    performAsync("Loading avatar...").then( () => {
      loadAvatar();
      return performAsync("Loading status history...");
    }).then( () => {
      loadStatusHistory();
      return performAsync("Loading posts...");
    }).then( () => {
      loadPosts();
      return performAsync("Loading tags...");    
    }).then( () => {
      loadTags();
      return performAsync("Done...");
    });
      
    /*
      return performAsync("Loading playlist...");
    }).then( () => {
      loadPlaylist();
      return performAsync("Done!");
    });
    */
    
  }
  
  /*
  function loadPlaylist() {
    var trackRequest = new XMLHttpRequest();
    trackRequest.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var obj = JSON.parse(this.responseText);
        var trackCnt = obj.length;
        
        if (trackCnt == 0) {
          var noTrack = $('<p id="noTrack">No track(s) have been added to playlist</p>');
          var mostRecentTrack = $('#my_playlist').first();
          noTrack.prependTo(mostRecentTrack);  
          return;
        }
 		
        for ( var x = 0; x < trackCnt; x++ ) {
        
          var track = $('<li class="track" id="track' + obj[x].track_id + 
          '" data-title="' + obj[x].title + '" data-artist="' + obj[x].artist +
          '" data-genre="' + obj[x].genre + '" data-album="' + obj[x].album +
          '" data-mp3-path="' + obj[x].mp3_path + '" data-ogg-path="' + obj[x].ogg_path +
          '" data-cover-art="' + obj[x].art + '">' + obj[x].artist + " - " + obj[x].title + '</li>');
          
          var mostRecentTrack = $('#my_playlist').first();
          track.appendTo(mostRecentTrack);  
          
        } 	  
    
      }
    };
    trackRequest.open("GET", "user_playlist_controller.php", true);
    trackRequest.send();
  }
  */
  
  function loadAvatar() {
    var avatarRequest = new XMLHttpRequest();
    avatarRequest.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var avatar = JSON.parse(this.responseText);
        //alert(avatar[0].avatar);
        $('#avatar').attr("src", avatar[0].avatar);
        $('#m_avatar').attr("src", avatar[0].avatar);
      }
    };
    avatarRequest.open("GET", "get_avatar.php", true);
    avatarRequest.send();
  }
  
  function loadStatusHistory() {
    var statusRequest = new XMLHttpRequest();
    statusRequest.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var obj = JSON.parse(this.responseText);
        var statusCnt = obj.length;
        
        /*
        if (statusCnt == 0) {
          var noStatus = $('<p class="noStatus" class="w3-center">No status history to retrieve...</p>');
          var mostRecentStatus = $('#status_history_container').first();
          noStatus.prependTo(mostRecentStatus);
          return;
        }
        */
        
        // Clear the loading message before populating
        if (statusCnt > 0) {
          $('#status_history_container').empty();
        }

        for ( var x = 0; x < statusCnt; x++ ) {
          
          var status = $( '<div id="status' + obj[x].status_id + '"' + 
          '<p>' + obj[x].status_text + '</p>' + 
          '<p>' + obj[x].created_at + '</p>' +
          '<div id="status_options" style="position:relative;top:2px;padding:10px;">' +
          '<a id="deleteStatus" href="home.php?action=Delete%20Status&statid=' 
          + obj[x].status_id + '"><i class="fa fa-trash-o" style="float:right;color:red;">' + 
          '</i></a>' + '</div><hr></div>');
          
          var mostRecentStatus = $('#status_history_container').first();
          status.prependTo(mostRecentStatus);   
              
        }	   
      }
    };
    statusRequest.open("GET", "get_user_status_history.php", true);
    statusRequest.send();
  
  }
  
  function loadPosts() {
    var postRequest = new XMLHttpRequest();
    postRequest.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var obj = JSON.parse(this.responseText);
        var postCnt = obj.length;
        
        if (postCnt == 0) {
          var post = $('<p id="noPosts" style="position:relative;top:10px;"class="w3-center">No posts to show...</p>');
          var mostRecentPost = $('#post_container').first();
          post.prependTo(mostRecentPost);
          return;
        }
          
        for ( var x = 0; x < postCnt; x++ ) {
    
    	    if (obj[x].image == "true") {
    	      var post = $( '<div id="post' + obj[x].p_id + '" class="section w3-card-4">' +
    	      '<span style="float:left;"><img src="' + obj[x].avatar + '" alt="quarterpast4" id="qp4" height="40" width="47" class="w3-circle"/>' + 
    	      '</span><h1 class="title">' + obj[x].title +
              '</h1><img id="post" src="' + obj[x].photo_url + '" alt="" height="385" style="width:100%"></img>' +
              '<div class="metadata"><span class="credit">' + obj[x].photo_cred + '</span><br><br>' +
              '<p class="post_tags" style="margin-left:10px;">' +  
              '<br><i class="fa fa-user fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].user_name + 
              '<i class="fa fa-calendar-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + moment(obj[x].created_at, "YYYY-MM-DD kk:mm:ss").fromNow() + 
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>7,854' + 
              '<i class="fa fa-heart-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;color:red;"></i>944,578' + 
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div id="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<span style="text-align:left;color:blue;text-decoration:underline;"><a href="#">Show/Hide Comments</a></span>' +
              '<span style="float:right;color:red;text-decoration:underline;"><a id="deletePost" href="home.php?action=Delete%20Post&pid=' + obj[x].p_id + '">Delete Post</a></span>' +
			  '</div><hr><div id="post_comments"></div></div>');
    	    } else if (obj[x].video == "true") {
    		  var post = $( '<div id="post' + obj[x].p_id + '" class="section w3-card-4" style="height:385;">' + 
    		  '<span style="float:left;"><img src="' + obj[x].avatar + '" alt="quarterpast4" id="qp4" height="40" width="47" class="w3-circle"/>' + 
    		  '</span><h1 class="title">' + obj[x].title + '</h1>' +
              '<div id="video-container"><div id="video-contained" class="w3-container">' + 
              '<video width="100%" height="385" id="my-video" controls controlslist="nodownload" poster="' + obj[x].thumbnail + '" allowfullscreen>' +
	          '<source src="' + obj[x].video_mp4 + '" type="video/mp4">' +
              '</video></div></div>' + 
              '<div class="metadata"><span class="credit">' + obj[x].photo_cred + '</span><br><br>' +
              '<p class="post_tags" style="margin-left:10px;">' +
              '<br><i class="fa fa-user fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].user_name +
              '<i class="fa fa-calendar-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + moment(obj[x].created_at, "YYYY-MM-DD kk:mm:ss").fromNow() + 
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>98,384' + 
              '<i class="fa fa-heart-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;color:red;"></i>17,475,978' + 
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div id="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:3px;padding:10px;">' +
              '<span style="text-align:left;color:blue;text-decoration:underline;"><a href="#">Show/Hide Comments</a></span>' +
              '<span style="float:right;color:red;text-decoration:underline;"><a id="deletePost" href="home.php?action=Delete%20Post&pid=' + obj[x].p_id + '">Delete Post</a></span>' +
			  '</div></div>');
    	    } else if (obj[x].external == "true") {
    		  var post = $( '<div id="post' + obj[x].p_id + '" class="section w3-card-4">' + 
    		  '<span style="float:left;"><img src="' + obj[x].avatar + '" alt="quarterpast4" id="qp4" height="40" width="47" class="w3-circle"/>' + 
    		  '</span><h1 class="title">' + obj[x].title +
              '</h1><div style="position:relative;height:0px;padding-bottom:56.25%">' +
	          '<iframe src="" data-src="' + obj[x].external_url + '" frameborder="0"' +
	          'width="640" height="360" frameborder="0" style="position:absolute;' +
	          'width:100%;height:100%;left:0px;" allowfullscreen></iframe></div>' +
              '<p class="post_tags" style="margin-left:10px;">' +
              '<i class="fa fa-user fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].user_name +
              '<i class="fa fa-calendar-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].created_at + 
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>105,384' + 
              '<i class="fa fa-heart-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;color:red;"></i>20,195,578' + 
              '</p><hr><p class="entry">' + obj[x].entry + '</p></div>');
    	    } else {
    		  var post = $( '<div id="post' + obj[x].p_id + '" class="section w3-card-4">' + 
    		  '<span style="float:left;"><img src="' + obj[x].avatar + '" alt="quarterpast4" id="qp4" height="40" width="47" class="w3-circle"/>' + 
    		  '</span><h1 class="title">' + obj[x].title + '</h1>' +
    		  '<div class="metadata">' + 
              '<p class="post_tags" style="margin-left:10px;">' +
              '<br><i class="fa fa-user fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].user_name +
              '<i class="fa fa-calendar-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' +  moment(obj[x].created_at, "YYYY-MM-DD kk:mm:ss").fromNow() + 
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>7,965' +
              '<i class="fa fa-heart-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;color:red;"></i>834,578' + 
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div id="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:2px;padding:10px;">' +
              '<span style="text-align:left;color:blue;text-decoration:underline;"><a href="#">Show/Hide Comments</a></span>' +
              '<span style="float:right;color:red;text-decoration:underline;"><a id="deletePost" href="home.php?action=Delete%20Post&pid=' + obj[x].p_id + '">Delete Post</a></span>' +
			  '</div></div></div>');	  
    	    } 	  
              
            var mostRecentPost = $('#post_container').first();
            post.prependTo(mostRecentPost);   
              
        }  
      }
    };
    postRequest.open("GET", "get_user_posts.php", true);
    // xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    postRequest.send();
  
  }

  function loadTags() {
    var tagRequest = new XMLHttpRequest();
    tagRequest.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var tags = JSON.parse(this.responseText);
        var tag_array = [];
        var len = tags.length;

        if (len > 0) {
          for ( var i = 0; i < len; i++ ) {
            $('#post' + tags[i].p_id).find('.post_tags').prepend('<a style="color:blue;"href="#"><i class="fa fa-hashtag fa-lg" aria-hidden="true"></i>' + tags[i].tag + '</a>&nbsp;');
          }
        }
      }
    }; 
    tagRequest.open("GET", "get_user_tags.php", true);
    // xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    tagRequest.send();
  }
    
  function openTab(evt, tabName) {
      
    document.getElementsByClassName("tablink")[0].click();
  
    var i, x, tablinks;
    x = document.getElementsByClassName("music-tab");
    for (i = 0; i < x.length; i++) {
      x[i].style.display = "none";
    }
    
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < x.length; i++) {
      tablinks[i].classList.remove("w3-light-grey");
    }  
    
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.classList.add("w3-light-grey");

  }
  
  $(document).ready(function() {
    
    sequenceAsync();
    
    $('#clear').click(function() {
      $('#status').val('');
    });
    $('#submit').click(function() {
      $('#status').trigger('focusout'); // Required to make status updates push to server
      var message = $('#status').val();
      var user_id = "<?php echo $_SESSION['user_id']; ?>";
      $('#status').val('');
      
      $('.noStatus').css({display: "none"});
      
      $.ajax({
        async: true, 
        cache: false,
        dataType: 'json', 
        type: 'POST', 
        url: 'simulation_controller.php',
        data: { update: message, u_id: user_id }
      }).done(function ( msg ) {
        
        $('#m_status_update').text(msg[0].status_text);
        $('#m_status_time').text(msg[0].created_at);
        
        $('#status_update').text(msg[0].status_text);
        $('#status_time').text(msg[0].created_at);
        
        var status = $( '<div id="status' + msg[0].status_id + '"' + 
        '<p>' + msg[0].status_text + '</p>' + 
        '<p>' + msg[0].created_at + '</p>' +
        '<div id="status_options" style="position:relative;top:2px;padding:10px;">' +
        '<a id="deleteStatus" href="home.php?action=Delete%20Status&statid=' + msg[0].status_id + '"><i class="fa fa-trash-o" style="float:right;color:red;"></i></a>' +
		'</div><hr></div>');
          
        var mostRecentStatus = $('#status_history_container').first(); 
        status.prependTo(mostRecentStatus);   
        
      });
      
    });
    
    $('#bio_submit').click(function() {
      
      // Required to make status updates push to server
      $('#bio_edit').trigger('focusout');
    
      // Save updated bio to variable
      var bio = $('#bio_edit').val();
      
      var id = "<?php echo $_SESSION['user_id']; ?>";

      $('#bio_text').text(bio);
      $('#m_bio_text').text(bio);
      
      // Clear out bio_edit textarea input
      $('#bio_edit').val('');
          
      $.ajax({
        async: true,
        cache: false,
        url: 'bio_controller.php',  
        type: 'POST',
        data: { update_bio: bio, u_id: id }  
      }).done(function ( msg ) {
        console.log('data retrieved...');
      }).fail(function ( xhr, textStatus) {
        console.log(xhr.statusText);
      });
      
    });
    
    $('#settings_submit').click(function() {
      // Placeholder
    })
    
    $('#flashback').click(function() {
      // Placeholder  
    });  
    
    $('#bio_edit_icon').click(function() {
      document.getElementById('bio_modal').style.display = 'block';
    });
    
    $('#m_bio_edit_icon').click(function() {
      document.getElementById('bio_modal').style.display = 'block';
    })
    
    $('#settings').click(function() {
      window.location.assign('settings_update.php');
      //document.getElementById('settings_modal').style.display = 'block';
    })
    
    $('#m_settings').click(function() {
      window.location.assign('settings_update.php');
      //document.getElementById('settings_modal').style.display = 'block';
    })
    
    $('#flashback_dialog_x').click(function() {
      $('#close_flashback').css({
        display: "none"
      });
    });
    
    $('#flashback_dialog_close').click(function() {
      $('#close_flashback').css({
        display: "none" 
      });
    });
    
    $('#m_flashback').click(function() {
    
      $('#close_flashback').css({
        position: "fixed",
        top: "375px",
        right: "5px"
      });
      
      document.getElementById('close_flashback').style.display='block'; 
      
      $('#close_flashback').click(function() {
      
        $('#flashback-dialog').css('display', 'none');
        $('#close_flashback').css('display', 'none');  
      
      });
      
    });       
    
  });
</script>

<!-- Start of Dynamic Content Section -->

   <!-- Music Dialog -->
  <div id="music-dialog" class="w3-modal">
	<div class="w3-modal-content w3-card-4 w3-animate-zoom">
	  <header class="w3-container w3-blue">
		<span onclick="document.getElementById('music-dialog').style.display = 'none'"
		class="w3-button w3-hover-red w3-display-topright">&times;</span>
		<h2>Music</h2>
	  </header>

	  <div class="w3-bar w3-border-bottom">
		<button onclick="openTab(event, 'my_playlist')" class="tablink w3-bar-item 
		w3-button">Playlist</button>
		<button onclick="openTab(event, 'artist')" class="tablink w3-bar-item
		w3-button">Artists</button>
		<button onclick="openTab(event, 'album')" class="tablink w3-bar-item
		w3-button">Albums</button>
		<button onclick="openTab(event, 'genre')" class="tablink w3-bar-item
		w3-button">Genres</button>
		<button onclick="openTab(event, 'track')" class="tablink w3-bar-item
		w3-button">Tracks</button>
	  </div>

   
	  <div id="my_playlist" class="w3-container music-tab">
	    <?php
	    
	      $user_id = $_SESSION['user_id'];
	      $playlist = new Playlist();
	      $playlist->setUID($user_id);
	      $output = $playlist->getPlaylist();
	    
	      if (!($output)) {
	        echo "<p>No tracks added to playlist yet...</p>";
	      } else {
	        echo $output;
	      }
	    
	    ?>
	  </div>
	  
	  <div id="artist" class="w3-container music-tab">
		<h1>Artist</h1>
		<p>From artist</p>
	  </div>

	  <div id="album" class="w3-container music-tab">
		<h1>Album</h1>
		<p>From album</p>
	  </div>

	  <div id="genre" class="w3-container music-tab">
	   <h1>Genre</h1>
	   <p>From genre</p>
	  </div>

	  <div id="track" class="w3-container music-tab">
	   <h1>Tracks</h1>
	   <p>From Tracks</p>
	  </div>

	  <div class="w3-container w3-light-grey w3-padding">
		<button class="w3-button w3-left w3-hover-red w3-border"
		  onclick="$( '#music-dialog' ).css('display', 'none');">Close
		</button>
	  </div>

	</div>
  </div>


  <!-- status flashback dialog -->
  <div id="flashback-dialog" class="w3-modal">
	<div class="w3-modal-content w3-card-4 w3-animate-zoom">
	  <header class="w3-container w3-yellow">
		<span id="flashback_dialog_x" onclick="document.getElementById('flashback-dialog').style.display = 'none'"
		class="w3-button w3-hover-red w3-display-topright">&times;</span>
		<h2 style="color:white;">Status Flashback</h2>
	  </header>
	  
	  <div id="flashback_container" class="w3-container status-tab">
	    <div id="status_history_container">
	    <?php
	    
	      $user_id = $_SESSION['user_id'];
          $lastUpdate = new Status();
          echo $lastUpdate->getText($user_id);
          
          if (empty($lastUpdate)) {
            echo "<p>No status history to retrieve...</p>";
          } else {
            echo "<p>Loading...</p>";
          }
          
        ?>
	    </div>
	  </div>	
	  
      <div class="w3-container w3-light-grey w3-padding">
		<button id="flashback_dialog_close" class="w3-button w3-right w3-hover-red w3-border"
		  onclick="$( '#flashback-dialog' ).css('display', 'none');">Close
		</button>
	  </div>

	</div>
  </div>
  <!-- end status flashback dialog -->
  
  <!-- bio dialog -->
  <div id="bio_modal" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-anmiate-zoom">
      <header class="w3-container w3-blue">
		<span onclick="document.getElementById('bio_modal').style.display = 'none'"
		class="w3-button w3-hover-red w3-display-topright">&times;</span>
		<h2>Edit Bio</h2>
	  </header>
    
      <div class="w3-center"><br>
        <form class="w3-container" method="post">
          <div class="form-group">
            <!-- <label for="bio_edit">Edit bio</label> -->
            <div><i class="w3-xxlarge fa fa-pencil" style="color:#ffcc66;"></i></div>
            <textarea id="bio_edit" name="bio_edit" class="form-control" placeholder="215 character limit" rows="3" maxlength="215"></textarea>
            <br>
          </div>
        </form>
        
        <div id="bio_submit_container">
          <button id="bio_submit" class="btn btn-primary w3-margin-bottom">Update</button>
        </div>
      </div>
      
      <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
        <button onclick="document.getElementById('bio_modal').style.display='none'"
        type="button" class="w3-button w3-hover-red">Cancel</button>
      </div>
    </div>
  </div>
  <!-- end bio dialog -->
  
  <!-- start settings dialog -->
  <div id="settings_modal" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-animate-zoom">
      <header class="w3-container w3-blue">
        <span onclick="document.getElementById('settings_modal').style.display = 'none'"
        class="w3-button w3-hover-red w3-display-topright">&times;</span>
        <h2>Update settings</h2>
      </header>
      
      <div class="w3-center"><br>
        <form class="w3-container" method="post">
          <div class="form-group">
            <!--<label for="avatar_path">Update avatar</label>-->
            <div><i class="w3-xxlarge fa fa-cog" style="color:#cc6600"></i></div>
            <input type="file" id="avatar_path" name="avatar_path" class="form-control-file"></input>
            <br>
          </div>
        </form>
        
        <div id="settings_update_container">
          <button id="settings_submit" class="btn btn-primary w3-margin-bottom">Update</button>
        </div>
      </div>
        
      <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
        <button onclick="document.getElementById('settings_modal').style.display='none'"
        type="button" class="w3-button w3-hover-red">Cancel</button>
      </div>
    </div>
  </div>
  <!-- end settings dialog -->
      
    
            
  <div id="profile_bio_container" style="padding:10px;">  
    <div id="bio_avi" style="position:relative;padding:8px;width:100px;height:100px;">
      <img id="avatar" style="position:relative;" src="
      <?php 
        $id = $_SESSION['user_id'];
        $user = new User();
        $user->setUserId($id);
        echo $user->getAvatar();
      ?>" width="90" height="90"></img>
      
    </div>
    
    <!-- username character count cannot be greater than 24 -->
    <div id="bio_username" style="position:relative;top:-95px;left:110px;font-size:12px;">
      <?php 
        $user_id = $_SESSION['user_id'];
        $userName = new User();
        echo "<b>" . $userName->getUsername($user_id) . "</b>"; // must be output this way or else error    
      ?>
    </div>
    
    <!-- Hide display of tagline and replace with action items -->
    <div id="bio_tagline_container" style="position:relative;top:-75px;left:145px;display:none;">
       "Closed beta v1.0"
    </div>
    
    <div id="bio_action_container" style="position:relative;top:-56px;left:103px;width:113px;height:25px;padding:2px;">
      <i id="bio_edit_icon" class="fa fa-pencil action_items" aria-hidden="true"></i>
      <i id="direct_message" class="fa fa-envelope action_items" aria-hidden="true"></i>
      <i id="settings" class="fa fa-cog action_items" aria-hidden="true"></i>
    </div>
    
    <!--
    <div id="follow_button_container" style="position:relative;top:-122px;left:146px;padding:0px;">
      <a href="#" class="btn btn-primary btn-sm active" role="button" aria-pressed="true">Follow</a>
    </div>
    --> 
    
    
    <div id="profile_stats" style="position:relative;width:328px;left:1px;margin-left:2px;top:-24px;">
      <span id="profile_followers">444m followers</span>
      <span id="profile_following" style="margin-left:13px;">0 following</span>
      <span id="profile_posts" style="margin-left:13px;">
      <?php
      
        $id = $_SESSION['user_id'];
        $post = new Post();
        $post->setUserId($id);
        $postCount = $post->getPostDisplayCount();
        
        if ($postCount == 1) {
          echo "$postCount post";
        } else if ($postCount > 1) {
          echo "$postCount posts";
        } else {
          echo "0 posts";
        }   
      
      ?>
      </span>
    </div>
    
    <div id="bio_text" style="position:relative;top:-13px;">
      <!-- Display bio from db -->
      <?php
        $user_id = $_SESSION['user_id'];
        $bio = new User();
        $bio->setUserId($user_id);
        echo $bio->getBio();  
      ?>  

    </div>
    
  </div>
  
  <div id="status_container">
    <span id="status_update">
      <?php
        $user_id = $_SESSION['user_id'];
        $lastUpdate = new Status();
        echo htmlentities($lastUpdate->getText($user_id));
      ?>
    </span>
    <br>
    <div id="status_date">
      <span id="status_time">
        <?php
          $user_id = $_SESSION['user_id'];
          $timestamp = new Status();
          echo $timestamp->getTimestamp($user_id);
        ?>
      </span>
      <i id="flashback" onclick="document.getElementById('flashback-dialog').style.display='block'" class="fa fa-bolt fa-lg" aria-hidden="true"></i>
    </div>
  </div>

<!-- start mobile display -->
  
  <div id="m_profile_bio_container">
    
    <div id="m_bio_avi" style="position:relative;padding:8px;width:100px;height:100px;">
      <img id="m_avatar"style="position:relative;" src="" width="80" height="80"></img>
    </div>
    
    <!-- username character count cannot be greater than 24 -->
    <div id="m_bio_username" style="position:relative;top:-99px;left:110px;font-size:12px;">
      <?php 
        $user_id = $_SESSION['user_id'];
        $userName = new User();
        echo "<b>" . $userName->getUsername($user_id) . "</b>"; // must be output this way or else error 
      ?>
    </div>
    
    <div id="m_bio_tagline_container" style="position:relative;display:none;top:-75px;left:145px;">
       "Alpha Testing"
    </div>
    
    <div id="m_bio_action_container" style="position:relative;top:-56px;left:106px;width:100px;">
      <i id="m_bio_edit_icon" class="fa fa-pencil m_action_item" aria-hidden="true"></i>
      <i id="m_direct_message" class="fa fa-envelope m_action_item" aria-hidden="true"></i>
      <i id="m_settings" class="fa fa-cog m_action_item" aria-hidden="true"></i>
    </div>
    
    <!--
    <div id="m_follow_button_container" style="position:relative;top:-126px;left:112px;padding:0px;">
      <a href="#" class="btn btn-primary btn-sm active" role="button" aria-pressed="true">Follow</a>
    </div>
    -->
    
    <div id="m_profile_stats" class="w3-center" style="position:relative;width:328px;left:1px;top:-24px;">
      <span id="m_profile_followers">444m followers</span>
      <span id="m_profile_following" style="margin-left:13px;">0 following</span>
      <span id="m_profile_posts" style="margin-left:13px;">
      <?php
      
        $id = $_SESSION['user_id'];
        $post = new Post();
        $post->setUserId($id);
        $postCount = $post->getPostDisplayCount();
        
        if ($postCount == 1) {
          echo "$postCount post";
        } else if ($postCount > 1) {
          echo "$postCount posts";
        } else {
          echo "0 posts";
        }   
      
      ?>
      </span>
    </div>
    
    <div id="m_bio_text" style="position:relative;top:-13px;">
     <?php 
       $user_id = $_SESSION['user_id'];
       $bio = new User();
       $bio->setUserId($user_id);
       echo $bio->getBio();
     ?>
    </div>
    
  </div>
  
  <div id="form_container"> 
    <form id="status_form" method="post">
      <div class="form-group">
        <label for="status">Update status</label>
        <textarea id="status" name="status" class="form-control" rows="3"></textarea>
        <br>
      </div>
    </form>
  </div>
  
  <!-- clear button is hidden on mobile--> 
  <div id="button_clear_container">
    <button id="clear" class="btn btn-primary">Clear</button> 
  </div>
  
  <div id="button_submit_container">
    <button id="submit" class="btn btn-primary">Update</button>
  </div>

  <div id="m_status_container">
    <span id="m_status_update">
      <?php
        $user_id = $_SESSION['user_id'];
        $lastUpdate = new Status();
        echo $lastUpdate->getText($user_id);
      ?>
    </span>
  </div>
  <div id="m_status_date">
    <span id="m_status_time">
      <?php
        $user_id = $_SESSION['user_id'];
        $timestamp = new Status();
        echo $timestamp->getTimestamp($user_id);
      ?>
  </div>
  
  <i id="m_flashback" onclick="document.getElementById('flashback-dialog').style.display='block'" class="fa fa-bolt" aria-hidden="true"></i>
  
  <div id="up_shortcut" style="z-index:999999">
    <a href="#m_profile_bio_container" class="btn btn-primary btn-sm active" role="button" aria-pressed="true">
      <span class="glyphicon glyphicon-circle-arrow-up"></span>
    </a>
  </div>
  
  <div id="close_flashback" style="z-index:999999">
    <span class="glyphicon glyphicon-remove-sign btn btn-primary btn-sm active" role="button" aria-pressed="true"></span>
  </div>
  
  <!-- end mobile display -->

  <!-- post container must be last node in DOM or else it will overwrite content--> 
  <div id="post_container"></div>


<!-- End of Dynamic Content Section -->
  <?php include ('php_inc/inc_music_source_user.php')?>
<?php include_once ('php_inc/inc_user_footer.php'); ?> 