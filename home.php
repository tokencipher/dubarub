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

/*
ini_set( 'display_errors', 1 ); 
error_reporting( E_ALL );
*/
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
<?php require_once ('php_class/class_Follow.php'); ?>
<?php require_once ('php_class/class_User.php'); ?>
<?php require_once ('php_inc/inc_db_qp4.php'); ?>
<title>dubarub | Home</title>
<?php include_once ('php_inc/inc_user_home_nav.php'); ?>
</head>
<body>

<script>

    
  var userID = "<?php echo $_SESSION['user_id']; ?>";
  var userName = "<?php echo $_SESSION['user_name']; ?>";

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
      return performAsync("Loading comments...")
    }).then( () => {
      loadComments();
      return performAsync("Done!");
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
          
          var status = $( '<div id="status' + obj[x].status_id + '">' + 
          '<p>' + obj[x].status_text + '</p>' + 
          '<p>' + obj[x].created_at + '</p>' +
          '<div id="status_options" style="position:relative;top:2px;padding:10px;cursor:pointer;">' +
          '<div onclick="removeStatus(this)" id="deleteStatus' + obj[x].status_id + '"  data-statusid="' + obj[x].status_id + '"><i class="fa fa-trash-o" style="float:right;color:red;">' + 
          '</i></div></div><hr></div>');
          
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
    	      var post = $( '<div data-username="' + obj[x].user_name + '" data-avatar="' + obj[x].avatar + '" id="post' + obj[x].p_id + '" class="section w3-card-4">' +
    	      '<span style="float:left;"><img src="' + obj[x].avatar + '" alt="dubarub user avatar" height="40" width="47" class="w3-circle"/>' + 
    	      '</span><h2 class="title">' + obj[x].title +
              '</h2><img id="post" src="' + obj[x].photo_url + '" alt="" height="385" style="width:100%"></img>' +
              '<div class="metadata"><span class="credit">' + obj[x].photo_cred + '</span><br><br>' +
              '<p class="post_tags" style="margin-left:10px;">' +  
              '<br><i class="fa fa-user fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].user_name + 
              '<i class="fa fa-calendar-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + moment(obj[x].created_at, "YYYY-MM-DD kk:mm:ss").fromNow() + 
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + '<span id="comment_count_post' + obj[x].p_id + '">' + obj[x].comments + '</span>' +
              '<i class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px"></i>' + obj[x].upvote + 
              '<div onclick="removePost(this)" data-pid="' + obj[x].p_id + '" style="position:relative;top:-4px;margin-right:10px;float:right"><button class="w3-button w3-circle w3-red fa fa-remove"></button></div>' +
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div id="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" id="addComment" href="#">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form method="post">' + 
			  '<div class="form-group"><label for="comment_text">Leave a comment</label>' + 
			  '<textarea id="comment_text' + obj[x].p_id + '" class="form-control" rows="3"></textarea></div>' + 
			  '</form><button onclick="submitComment(' + obj[x].p_id + ', ' + obj[x].comments + ')" class="commentSubmit btn btn-primary">Submit</button>' + 
			  '</div><hr><div class="post_comments" id="post_comments' + obj[x].p_id + '"></div></div>');
    	    } else if (obj[x].video == "true") {
    		  var post = $( '<div data-username="' + obj[x].user_name + '" data-avatar="' + obj[x].avatar + '" id="post' + obj[x].p_id + '" class="section w3-card-4" style="height:385;">' + 
    		  '<span style="float:left;"><img src="' + obj[x].avatar + '" alt="dubarub user avatar" height="40" width="47" class="w3-circle"/>' + 
    		  '</span><h2 class="title">' + obj[x].title + '</h2>' +
              '<div id="video-container"><div id="video-contained" class="w3-container">' + 
              '<video width="100%" height="385" id="my-video" controls controlslist="nodownload" poster="' + obj[x].thumbnail + '" allowfullscreen>' +
	          '<source src="' + obj[x].video_mp4 + '" type="video/mp4">' +
              '</video></div></div>' + 
              '<div class="metadata"><span class="credit">' + obj[x].photo_cred + '</span><br><br>' +
              '<p class="post_tags" style="margin-left:10px;">' +
              '<br><i class="fa fa-user fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].user_name +
              '<i class="fa fa-calendar-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + moment(obj[x].created_at, "YYYY-MM-DD kk:mm:ss").fromNow() + 
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i><span id="comment_count_post' + obj[x].p_id + '">' + obj[x].comments + '</span>' + 
              '<i class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px"></i>' + obj[x].upvote + 
              '<div onclick="removePost(this)" data-pid="' + obj[x].p_id + '" style="position:relative;top:-4px;margin-right:10px;float:right"><button class="w3-button w3-circle w3-red fa fa-remove"></button></div>' +
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div id="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" id="addComment" href="#">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form method="post">' + 
			  '<div class="form-group"><label for="comment_text">Leave a comment</label>' + 
			  '<textarea id="comment_text' + obj[x].p_id + '" class="form-control" rows="3"></textarea></div>' + 
			  '</form><button onclick="submitComment(' + obj[x].p_id + ', ' + obj[x].comments + ')" class="commentSubmit btn btn-primary">Submit</button>' +
			  '</div><hr><div class="post_comments" id="post_comments' + obj[x].p_id + '"></div></div>');
    	    } else if (obj[x].external == "true") {
    		  var post = $( '<div data-username="' + obj[x].user_name + '" data-avatar="' + obj[x].avatar + '" id="post' + obj[x].p_id + '" class="section w3-card-4">' + 
    		  '<span style="float:left;"><img src="' + obj[x].avatar + '" alt="dubarub user avatar" height="40" width="47" class="w3-circle"/>' + 
    		  '</span><h2 class="title">' + obj[x].title +
              '</h2><div style="position:relative;height:0px;padding-bottom:56.25%">' +
	          '<iframe src="' + obj[x].external_url + '?playsinline=1" data-src="' + obj[x].external_url + '" frameborder="0"' +
	          'width="640" height="360" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" style="position:absolute;' +
	          'width:100%;height:100%;left:0px;" allowfullscreen></iframe></div>' +
	          '<div class="metadata"><span class="credit">' + obj[x].photo_cred + '</span><br><br>' +
              '<p class="post_tags" style="margin-left:10px;">' +  
              '<br><i class="fa fa-user fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].user_name + 
              '<i class="fa fa-calendar-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + moment(obj[x].created_at, "YYYY-MM-DD kk:mm:ss").fromNow() + 
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i><span id="comment_count_post' + obj[x].p_id + '">' + obj[x].comments + '</span>' +
              '<i class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px"></i>' + obj[x].upvote + 
              '<div onclick="removePost(this)" data-pid="' + obj[x].p_id + '" style="position:relative;top:-4px;margin-right:10px;float:right"><button class="w3-button w3-circle w3-red fa fa-remove"></button></div>' +
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div id="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" id="addComment" href="#">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form method="post">' + 
			  '<div class="form-group"><label for="comment_text">Leave a comment</label>' + 
			  '<textarea id="comment_text' + obj[x].p_id + '" class="form-control" rows="3"></textarea></div>' + 
			  '</form><button onclick="submitComment(' + obj[x].p_id + ', ' + obj[x].comments + ')" class="commentSubmit btn btn-primary">Submit</button>' + 
			  '</div><hr><div class="post_comments" id="post_comments' + obj[x].p_id + '"></div></div>');
    	    } else {
    		  var post = $( '<div data-username="' + obj[x].user_name + '" data-avatar="' + obj[x].avatar + '" id="post' + obj[x].p_id + '" class="section w3-card-4">' + 
    		  '<span style="float:left;"><img src="' + obj[x].avatar + '" alt="dubarub user avatar" height="40" width="47" class="w3-circle"/>' + 
    		  '</span><h2 class="title">' + obj[x].title + '</h2>' +
    		  '<div class="metadata">' + 
              '<p class="post_tags" style="margin-left:10px;">' +
              '<br><i class="fa fa-user fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].user_name +
              '<i class="fa fa-calendar-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' +  moment(obj[x].created_at, "YYYY-MM-DD kk:mm:ss").fromNow() + 
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i><span id="comment_count_post' + obj[x].p_id + '">' + obj[x].comments + '</span>' +
              '<i class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px"></i>' + obj[x].upvote + 
              '<div onclick="removePost(this)" data-pid="' + obj[x].p_id + '" style="position:relative;top:-4px;margin-right:10px;float:right"><button class="w3-button w3-circle w3-red fa fa-remove"></button></div>' +
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div id="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" id="addComment" href="#">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form method="post">' + 
			  '<div class="form-group"><label for="comment_text">Leave a comment</label>' + 
			  '<textarea id="comment_text' + obj[x].p_id + '" class="form-control" rows="3"></textarea></div>' + 
			  '</form><button onclick="submitComment(' + obj[x].p_id + ', ' + obj[x].comments + ')" class="commentSubmit btn btn-primary">Submit</button>' + 
			  '</div><hr><div class="post_comments" id="post_comments' + obj[x].p_id + '"></div></div>');	  
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
  
  function loadComments() {
    var commentRequest = new XMLHttpRequest();
    commentRequest.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var comments = JSON.parse(this.responseText);
        var len = comments.length;
        var value = "<?php echo ( (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : 0 ); ?>";
        console.log("value is: " + value);
        var user_id = value;
        
        // if ( $('#post' + obj.p_id).find('.post_comments').children().length > 0 ) {
        // console.log(len);
        
        /*
         * if current user session id == u_id then add delete icon else show default
         * comment object
        */
        
        if (len > 0) {
          for ( var i = 0; i < len; i++) {
            if (comments[i].upvote == 1) {
              var trophyAmount = "trophy";
            } else {
              var trophyAmount = "trophies";
            }
            
            console.log("comment user id: " + comments[i].u_id + " and current user: " + user_id);
            
            if ( comments[i].u_id == user_id ) {
              $('#post' + comments[i].p_id).find('.post_comments').append('<div data-avatar="' + comments[i].avatar + '" data-upvote="' + comments[i].upvote + " " + trophyAmount + '" data-timestamp="' + moment(comments[i].timestamp, "YYYY-MM-DD kk:mm:ss").fromNow() + '" data-username="' + comments[i].user_name + '" id="comment' + comments[i].c_id +  '" class="media">' +
  			  '<div class="media-left">' + 
  			  '<a href="#"><img style="margin-left:5px" height="64" width="64" class="media-object" src="' + comments[i].avatar + '" alt="user avatar"></a>' +
 			  '</div><div style="position:relative;top:-5px;text-align:left;" class="media-body"><div class="commenter" style="font-size:14px;" class="media-heading"><b><a id="comment_owner_link" href="user.php?name=' + comments[i].user_name + '">' + comments[i].user_name + '</a></b> says:</div>' + 
 			  '<div class="comment_body" style="margin-bottom:2px;font-size:12px">' + comments[i].comment + '</div>' + 
 			  '<div onclick="removeComment(this)" data-postid="' + comments[i].p_id + '" data-commid="' + comments[i].c_id + '" id="remove_comment' + comments[i].c_id + '" style="position:relative;bottom:22px;cursor:pointer;margin-right:10px;float:right">' + 
 			  '<i class="fa fa-times" style="color:red" aria-hidden="true"></i></div>' + 
 			  '<div style="clear:both;font-size:12px" class="comment_options flex-container">' + 
 			  '<div class="comment_timestamp">' + moment(comments[i].timestamp, "YYYY-MM-DD kk:mm:ss").fromNow() + '</div>' +
 			  '<div class="upvote">' + comments[i].upvote + " " + trophyAmount + '</div>' +
 			  '</div></div>');
 			} else {
 			  $('#post' + comments[i].p_id).find('.post_comments').append('<div id="comment' + comments[i].c_id +  '" class="media">' +
  			  '<div class="media-left">' + 
  			  '<a href="#"><img style="margin-left:5px" height="64" width="64" class="media-object" src="' + comments[i].avatar + '" alt="user avatar"></a>' +
 			  '</div><div style="position:relative;top:-5px;text-align:left;" class="media-body"><div id="commenter' + comments[i].c_id + '" style="font-size:14px;" class="media-heading"><b><a id="comment_owner_link" href="user.php?name=' + comments[i].user_name + '">' + comments[i].user_name + '</a></b> says:</div>' + 
 			  '<div class="comment_body" style="margin-bottom:2px;font-size:12px">' + comments[i].comment + '</div>' + 
 			  '<div style="clear:both;font-size:12px" class="comment_options flex-container">' + 
 			  '<div class="comment_timestamp">' + moment(comments[i].timestamp, "YYYY-MM-DD kk:mm:ss").fromNow() + '</div>' +
 			  '<div class="upvote">' + '<span id="trophy_count_comment' + comments[i].c_id + '">' + comments[i].upvote + '</span>' + " " + '<span id="trophy_counter' + comments[i].c_id + '">' + trophyAmount + '</span></div>' +
 			  '<div onclick="handCommentTrophy(this)" class="comment_trophy" data-commid="' + comments[i].c_id + '"><i class="fa fa-trophy" style="color:#b36b00;cursor:pointer" aria-hidden="true"></i></a></div>' + 
 			  '<div onclick="flagComment(this)" class="comment_flag" data-commid="' + comments[i].c_id + '" style="position:relative;left:15px;"><i style="color:red;cursor:pointer" class="fa fa-flag" aria-hidden="true"></i></div>' +
 			  '</div></div>');
 			}
          }
        } else {
          $('.post_comments').append('<div id="no_comment">Be the first to comment...</div>');
        }
      }
    };
    commentRequest.open("GET", "get_user_comments.php", true);
    commentRequest.send();
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
  
  function toggleComment(id) {
    if (($('#post_comments' + id).css("display")) === "none") {
      $('#post_comments' + id).css({
        display: "block"
      });
    } else {
      $('#post_comments' + id).css({
        display: "none"
      });
    }
  }
  
  function toggleCommentBox(event, id) {
    event.preventDefault();
    if (($('#comment_box' + id).css("display")) === "none") {
      $('#comment_box' + id).css({
        display: "block"
      });
    } else {
      $('#comment_box' + id).css({
        display: "none"
      });
    }
  }
  
  function submitComment(postID, commentCount) {
      
    // Required to make comment push to server
    $('#comment_text' + postID).trigger('focusout');
    
    /*
    var avatar = 'dub_priv_user_files/image/avatar/DC716F0D-C500-4993-83D0-7B6DD5032759.jpeg';
    var userName = 'sinclair';
    */
    
    // Implementation for this may different in user.php
    var avatar = $('#post' + postID).data('avatar');
    var userName = $('#post' + postID).data('username');
    
    // Save comment to variable
    var comment = $('#comment_text' + postID).val();
      
    // Clear out comment textarea input
    $('#comment_text' + postID).val('');
          
    $.ajax({
      async: true,
      cache: false,
      url: 'comment_controller.php',  
      type: 'POST',
      dataType: 'json',
      data: { post_owner: 'true', comment_text: comment, post_id: postID }  
    }).done(function ( msg ) {
      // Save reference to comment ID
      var commID = Number(msg.last_id);
      // Update comment count on post 
      $('#comment_count_post' + postID).text(msg.comment_count);
      // Add comment to post asynchronously 
      $('#post' + postID).find('.post_comments').append('<div id="comment' + commID +  '" class="media">' +
	  '<div class="media-left">' + 
	  '<a href="#"><img style="margin-left:5px" height="64" width="64" class="media-object" src="' + avatar + '" alt="user avatar"></a>' +
	  '</div><div style="position:relative;top:-5px;text-align:left;" class="media-body"><div class="commenter" style="font-size:14px;" class="media-heading"><b><a id="comment_owner_link" href="user.php?name=' + userName + '">' + userName + '</a></b> says:</div>' + 
	  '<div class="comment_body" style="margin-bottom:2px;font-size:12px">' + comment + '</div>' + 
	  '<div onclick="removeComment(this)" data-postid="' + postID + '" data-commid="' + commID + '" id="remove_comment' + commID + '" style="position:relative;bottom:22px;cursor:pointer;margin-right:10px;float:right">' + 
	  '<i class="fa fa-times" style="color:red" aria-hidden="true"></i></div>' + 
	  '<div style="clear:both;font-size:12px" class="comment_options flex-container">' + 
	  '<div class="comment_timestamp">a moment ago</div>' +
	  '<div class="upvote">0 trophies</div>' +
	  '</div></div>');
    }).fail(function ( xhr, textStatus) {
      console.log(xhr.statusText);
    });
    
  }
  
  function handPostTrophy(element) {
    console.log("post trophy clicked");
    var hand_trophy = Boolean("<?php echo (isset($_SESSION['user_id']) ? true : false); ?>");
    	
    if (hand_trophy === true) {
    	
      var trophy = $( element );
	  var postID = trophy.data("postid");
		
	  var action = "Upvote Post";
		
	  $.ajax({
        async: true,
      	cache: false,
        url: 'user_action.php',  
        type: 'POST',
        data: { user_action: action, post_id: postID }  
      }).done(function ( msg ) {
        console.log('Post upvote action taken...');
        console.log(msg);
      }).fail(function ( xhr, textStatus) {
        console.log(xhr.statusText);
      });
        
    } else {
      if (confirm("You must be logged in to give a trophy. Sign up/Login?")) {
  	    window.location.assign("https://dubarub.com");
	  } else {
  		return;
	  }
    }     
  }
  
  function handCommentTrophy(element) {
    console.log("comment trophy clicked");
    var hand_trophy = Boolean("<?php echo (isset($_SESSION['user_id']) ? true : false); ?>");
    	
    if (hand_trophy === true) {
    	
      var trophy = $( element );
	  var commID = trophy.data("commid");
		
	  var action = "Upvote Comment";
		
	  $.ajax({
        async: true,
      	cache: false,
        url: 'user_action.php',  
        type: 'POST',
        data: { user_action: action, comment_id: commID }  
      }).done(function ( msg ) {
        console.log('Comment upvote action taken...');
        var extension;
        var count = msg.trophy_count; 
        if (count == 1) {
          extension = "trophy";
        } else {
          extension = "trophies";
        }
        $('#trophy_count_comment' + commID).text(msg.trophy_count);
        $('#trophy_counter' + commID).text(extension);
        console.log(msg);
      }).fail(function ( xhr, textStatus) {
        console.log(xhr.statusText);
      });
        
    } else {
      if (confirm("You must be logged in to give a trophy. Sign up/Login?")) {
  	    window.location.assign("https://dubarub.com");
	  } else {
  		return;
	  }
    }     
  }
  
  function flagComment(element) {
    console.log("flag clicked");
    var flag_comment = Boolean("<?php echo (isset($_SESSION['user_id']) ? true : false); ?>");
    	
    if (flag_comment === true) {

      var flag = $( element );
	  var commID = flag.data("commid");
		
	  var action = "Flag Comment";
		
	  $.ajax({
        async: true,
      	cache: false,
        url: 'user_action.php',  
        type: 'POST',
        data: { user_action: action, comment_id: commID }  
      }).done(function ( msg ) {
        console.log('Flag comment action taken...');
        console.log(msg);
      }).fail(function ( xhr, textStatus) {
        console.log(xhr.statusText);
      });
        
    } else {
      if (confirm("You must be logged in to report this comment. Sign up/Login?")) {
  	    window.location.assign("https://dubarub.com");
	  } else {
  		return;
	  }
    }     
  }
  
  function removeComment(element) {
    console.log("remove comment item clicked");
    var remove_flag = Boolean("<?php echo (isset($_SESSION['user_id']) ? true : false); ?>");
    	
    if (remove_flag === true) {
      if (confirm("Are you sure you want to delete this comment?")) {
    	
        var comment = $( element );
	    var commID = comment.data("commid");
	    
	    // This is needed to decrement the comment counter for post related to comment
	    var postID = comment.data("postid");
	    
	    // Get comment counter for post 
	    var commentCount = $('#comment_count_post' + postID).text();
		
	    var action = "Remove Comment";
		
	    $.ajax({
          async: true,
      	  cache: false,
          url: 'user_action.php',  
          type: 'POST',
          data: { user_action: action, comment_id: commID, post_id: postID }  
        }).done(function ( msg ) {
          console.log('Remove comment action taken...');
          // Remove comment element from DOM 
          $('#comment' + commID).remove();
          // Decrement post comment counter 
          $('#comment_count_post' + postID).text(commentCount - 1);
        }).fail(function ( xhr, textStatus) {
          console.log(xhr.statusText);
        });
      }
       
    } else {
      if (confirm("You must be logged in to remove this comment. Sign up/Login?")) {
  	    window.location.assign("https://dubarub.com");
	  } else {
  		return;
	  }
    }       
  }
  
  function removePost(element) {
    console.log("remove post item clicked");
    var remove_flag = Boolean("<?php echo (isset($_SESSION['user_id']) ? true : false); ?>");
    
    console.log("Remove post flag is set to: " + remove_flag);
    	
    if (remove_flag === true) {
      if (confirm("Are you sure you want to delete this post?")) {
        //console.log("Performing remove action");
        
  	    var post = $( element );
	    var postID = post.data("pid");
	    
	    //console.log("Post ID is: " + postID);
		
	    var action = "Remove Post";
		
	    $.ajax({
          async: true,
      	  cache: false,
          url: 'user_action.php',  
          type: 'POST',
          data: { user_action: action, post_id: postID }  
        }).done(function ( msg ) {
          console.log('Remove post action taken...');
          console.log(msg);
          $('#post' + postID).remove();
        }).fail(function ( xhr, textStatus) {
          console.log(xhr.statusText);
        });
        
	  } else {
  		return;
	  }
        
    } else {
      if (confirm("You must be logged in to remove this post. Sign up/Login?")) {
  	    window.location.assign("https://dubarub.com");
	  } else {
  		return;
	  }
    }       
  }
  
  function removeStatus(element) {
    console.log("remove status item clicked");
    var remove_flag = Boolean("<?php echo (isset($_SESSION['user_id']) ? true : false); ?>");
    
    console.log("Remove status flag is set to: " + remove_flag);
    	
    if (remove_flag === true) {
      if (confirm("Are you sure you want to delete this status?")) {
  	    var status = $( element );
	    var statusID = status.data("statusid");
	    
	    //console.log("status ID is: " + statusID);
		
	    var action = "Remove Status";
		
	    $.ajax({
          async: true,
      	  cache: false,
          url: 'user_action.php',  
          type: 'POST',
          data: { user_action: action, status_id: statusID }  
        }).done(function ( msg ) {
          console.log('Remove status action taken...');
          console.log(msg);
        
          // Get the status update we are to delete
          var statusFlashbackStatusText = $('#status' + statusID).text();
          
          // Replace the timestamp with whitespace
          var revisedFlashbackStatusText = statusFlashbackStatusText.replace(/\d\d\d\d-\d\d-\d\d\s\d\d:\d\d:\d\d/, '').trim();
          //console.log('revised flashback status text: ' + revisedFlashbackStatusText);
          
          // Get the current status container text
          var statusContainerStatusText = $('#status_update').text().replace(/\d\d\d\d-\d\d-\d\d\s\d\d:\d\d:\d\d/, '').trim();
          //console.log('status container text: ' + statusContainerStatusText);
          
          // Get the replacement status to swap out in the status container
          var replacementStatusText = $('#flashback_container').find('#status' + statusID).next().text().replace(/\d\d\d\d-\d\d-\d\d\s\d\d:\d\d:\d\d/, '');
          //console.log('replacement status text: ' + replacementStatusText);
          
          if (revisedFlashbackStatusText == statusContainerStatusText) {
            $('#status_update').text(replacementStatusText);
            //console.log('replaced text');
          }
        
          
          $('#status' + statusID).remove();
        }).fail(function ( xhr, textStatus) {
          console.log(xhr.statusText);
        });
        
	  } else {
  		return;
	  }
        
    } else {
      if (confirm("You must be logged in to remove this status. Sign up/Login?")) {
  	    window.location.assign("https://dubarub.com");
	  } else {
  		return;
	  }
    }       
  }
  
  function getFollowers() {
    window.location.assign("followers.php");
  }
  
  function getFollowing() {
    window.location.assign("following.php");
  }
  
  function appearAsLink(elm) {
    $(elm).css({
      'text-decoration': 'underline', 
      'cursor': 'pointer'
    });
  }
    
  function stripHTML(str) {
    var StrippedString = str.replace(/(<([^>]+)>)/ig,"");
    return StrippedString;
  }
  
  function loadUnreadMessageCount() {
	
	$.ajax({
	  async: true,
	  cache: false,
	  url: 'get_unread_message_count.php',
	  type: 'POST', 
	  dataType: 'json',
	}).done(function (data) {
	  if (data.unreadCount == 0) {
		$('.badge').css("display", "none");
	  } else {
		$('.badge').text(data.unreadCount);
		$('.badge').css("display", "block");
	  }
	}).fail(function(xhr, textStatus) {
	  console.log(textStatus);
	});
	
  }
  
  $(document).ready(function() {
    
    sequenceAsync();
    
	var socket = io('https://dubarub.com:4200');
	loadUnreadMessageCount();
  
	$('#inbox').click(function() {
	  window.location.assign('https://quarterpast4.com/inbox.php');
	});
  
	socket.on('connect', function() {
	  console.log('Connected to message server...');
	  socket.emit('user connected', {userName: userName});
	});
  
	socket.on('message', function(data) {
	
	  if (data.recipient == userName) {
	  
		console.log('new message available for recipient: ' + data.recipient);
		var count = Number($('.badge').text());
		count += 1;
	  
		$('.badge').css("display", "block");
	
		$('.badge').text(count);
		
	  }
	
	});
    
    var status = document.getElementById('status');
    
    // Submit status update onClick of "Enter" key 
    status.addEventListener('keyup', function(event) {
      // Number 13 is the "Enter" key on the keyboard
      if (event.keyCode == 13) {
        // Trigger the button element with a click
        document.getElementById('submit').click();
      }
    });
    
    $('#clear').click(function() {
      $('#status').val('');
    });
    $('#submit').click(function() {
      $('#status').trigger('focusout'); // Required to make status updates push to server
      var message = stripHTML($('#status').val());
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
        
        $('#status_update').text(msg[0].status_text);
        $('#status_time').text(msg[0].created_at);
        
        var status = $( '<div id="status' + msg[0].status_id + '"' + 
        '<p>' + msg[0].status_text + '</p>' + 
        '<p>' + msg[0].created_at + '</p>' +
        '<div id="status_options" style="position:relative;top:2px;padding:10px;cursor:pointer;">' +
        '<div onclick="removeStatus(this)" id="deleteStatus' + msg[0].status_id + '"  data-statusid="' + msg[0].status_id + '"><i class="fa fa-trash-o" style="float:right;color:red;">' + 
        '</i></div></div><hr></div>');
          
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
      
      // Clear out bio_edit textarea input
      $('#bio_edit').val('');
      
      var action = 'Update Bio';
          
      $.ajax({
        async: true,
        cache: false,
        url: 'user_action.php',  
        type: 'POST',
        data: { user_action: action, updated_bio: bio, u_id: id }  
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
    
    /*
    $('#toggle_comments').click(function() {
      if (($('#post_comments').css("display")) === "none")  {
        $('#post_comments').css({
          display: "block"
        });
      } else {
        $('#post_comments').css({
          display: "none"
        });
      }
    });
    */
    
    $('#direct_message').click(function() {
      document.getElementById('message_modal').style.display = 'block';
    }); 
    
    $('#bio_edit_icon').click(function() {
      var id = "<?php echo $_SESSION['user_id']; ?>";
    
      $.ajax({
        async: true,
        cache: false,
        url: 'retrieve_bio.php',  
        type: 'POST',
        dataType: 'json',
        data: { u_id: id }  
      }).done(function ( msg ) {
        console.log('data retrieved...');
        $('#bio_edit').text(msg[0].bio);
      }).fail(function ( xhr, textStatus) {
        console.log(xhr.statusText);
      });
      
      document.getElementById('bio_modal').style.display = 'block';
      
    });
    
    $('#settings').click(function() {
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
    
  });
</script>

<!-- Start of Dynamic Content Section -->

   <!-- Music Dialog -->
  <div id="music-dialog" class="w3-modal">
	<div class="w3-modal-content w3-card-4 w3-animate-zoom">
	  <header class="w3-container w3-blue">
		<span onclick="document.getElementById('music-dialog').style.display = 'none'"
		class="w3-button w3-red w3-display-topright">&times;</span>
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
		class="w3-button w3-red w3-display-topright">&times;</span>
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
		<button id="flashback_dialog_close" class="w3-button w3-right w3-red w3-border"
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
		class="w3-button w3-red w3-display-topright">&times;</span>
		<h2>Edit Bio</h2>
	  </header>
    
      <div class="w3-center"><br>
        <form class="w3-container" method="post">
          <div class="form-group">
            <!-- <label for="bio_edit">Edit bio</label> -->
            <div><i class="w3-xxlarge fa fa-pencil" style="color:#ffcc66;"></i></div>
            <textarea id="bio_edit" name="bio_edit" class="form-control" placeholder="215 character limit" rows="3" maxlength="215" value=""></textarea>
            <br>
          </div>
        </form>
        
        <div id="bio_submit_container">
          <button id="bio_submit" class="btn btn-primary w3-margin-bottom">Update</button>
        </div>
      </div>
      
      <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
        <button onclick="document.getElementById('bio_modal').style.display='none'"
        type="button" class="w3-button w3-left w3-border w3-red">Cancel</button>
      </div>
    </div>
  </div>
  <!-- end bio dialog -->
  
  <!-- start settings dialog -->
  <div id="settings_modal" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-animate-zoom">
      <header class="w3-container w3-blue">
        <span onclick="document.getElementById('settings_modal').style.display = 'none'"
        class="w3-button w3-red w3-display-topright">&times;</span>
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
        type="button" class="w3-button w3-left w3-border w3-red">Cancel</button>
      </div>
    </div>
  </div>
  <!-- end settings dialog -->
  
  <!-- direct message dialog -->
  <div id="message_modal" class="w3-modal">
    <div class="w3-modal-content w3-card-4 w3-anmiate-zoom">
      <header class="w3-container w3-blue">
		<span onclick="document.getElementById('message_modal').style.display = 'none'"
		class="w3-button w3-red w3-display-topright">&times;</span>
		<h2>Send message</h2>
	  </header>

	  
	  <iframe src="https://dubarub.com:4200/message_form/<?php echo $_SESSION['user_name']; ?>" height="100%" width="100%" style="border:none"></iframe>

      
      <div class="w3-container w3-border-top w3-padding-16 w3-light-grey">
        <button onclick="document.getElementById('message_modal').style.display='none'"
        type="button" class="w3-button w3-left w3-border w3-red">Cancel</button>
      </div>
    </div>
  </div>
  <!-- end direct message dialog -->
      
    
            
  <div id="profile_bio_container">  
    <div id="bio_avi">
      <img id="avatar" src="
      <?php 
        $id = $_SESSION['user_id'];
        $user = new User();
        $user->setUserId($id);
        echo $user->getAvatar();
      ?>" width="90" height="90"></img>
      
    </div>
    
    <!-- username character count cannot be greater than 24 -->
    <div id="bio_username">
      <?php 
        $user_id = $_SESSION['user_id'];
        $userName = new User();
        echo "<b>" . $userName->getUsername($user_id) . "</b>"; // must be output this way or else error    
      ?>
    </div>
    
    <!-- Hide display of tagline and replace with action items -->
    <div id="bio_tagline_container">
       "Closed beta v1.0"
    </div>
    
    <div id="bio_action_container" class="flex-container">
      <i id="bio_edit_icon" class="fa fa-pencil action_items" aria-hidden="true"></i>
      <i id="direct_message" class="fa fa-paper-plane action_items" aria-hidden="true"></i>
      <i id="inbox" class="fa fa-envelope action_items" aria-hidden="true"><span style="position:absolute;top:-5px;right:-5px;padding:5px10px;border-radius:50%;background:red;color:white" class="badge"</i>
      <i id="settings" class="fa fa-cog action_items" aria-hidden="true"></i>
    </div>
    
    <!--
    <div id="follow_button_container" style="position:relative;top:-122px;left:146px;padding:0px;">
      <a href="#" class="btn btn-primary btn-sm active" role="button" aria-pressed="true">Follow</a>
    </div>
    --> 
    
    
    <div id="profile_stats">
      <span id="profile_followers" onclick="getFollowers()"><a style="cursor:pointer;">
        <?php
          $follow = new Follow();
          $followers = $follow->getFollowerCount($_SESSION['user_id']);
          if ($followers == 1) {
            echo $followers . " follower";
          } else {
            echo $followers . " followers";
          }
        ?>
        </a></span>
      <span id="profile_following" onclick="getFollowing()"><a style="cursor:pointer">
        <?php
          $follow = new Follow();
          $following = $follow->getFollowingCount($_SESSION['user_id']);
          echo $following . " following";
        ?>
        </a></span>
      <span id="profile_posts">
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
    
    <div id="bio_text">
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
  </div>
  <div id="status_date">
    <span id="status_time">
      <?php
        $user_id = $_SESSION['user_id'];
        $timestamp = new Status();
        echo $timestamp->getTimestamp($user_id);
      ?>
    </span>
  </div>
  <i id="flashback" onclick="document.getElementById('flashback-dialog').style.display='block'" class="fa fa-bolt fa-lg" aria-hidden="true"></i>

<!-- start mobile display -->
  
  <div id="form_container"> 
    <form id="status_form" method="post">
      <div class="form-group">
        <label for="status">Update status</label>
        <textarea id="status" name="status" class="form-control" rows="3" maxlength="2000"></textarea>
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
    
  <div id="up_shortcut" style="z-index:999999">
    <a href="#profile_bio_container" class="btn btn-primary btn-sm active" role="button" aria-pressed="true">
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