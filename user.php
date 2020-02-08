<!-- Author: Bryan Thomas -->
<!-- Last modified: 02/08/20 -->
<?php 
  session_start();
  
  // Get user name, set session
  $user = isset($_GET['name']) ? $_GET['name'] : '';
  if (!empty($user)) {
    $_SESSION['user'] = $user;
  } else {
    $_SESSION['user'] = '';
  }
 
  
?>

<?php include_once ('php_inc/inc_visitor_header.php'); ?>


<?php 
  ini_set( 'display_errors', 1 ); 
  error_reporting( E_ALL );
?>

  <title>dubarub | <?php echo $user; ?></title>  
  <?php require_once ('php_class/class_Status.php'); ?>
  <?php require_once ('php_class/class_Playlist.php'); ?>
  <?php require_once ('php_inc/inc_nav.php'); ?>
  <?php require_once ('php_class/class_Post.php'); ?>
  <?php require_once ('php_class/class_Follow.php'); ?>
  <?php require_once ('php_class/class_User.php'); ?>
  <?php require_once ('php_inc/inc_db_qp4.php'); ?>
</head>
<body>

<?php 

  try {

    // Retrieve username from db based on user_id
    if ($conn !== false) {
      $table = "user";
      $user_name = $_SESSION['user'];
      $sql = 'SELECT u_id, user_name FROM user WHERE user_name = :user';
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(':user', $user_name);
      $stmt->execute();
      if (!$stmt->rowCount() > 0) {
    	exit('<div style="position:relative;margin:auto;" class="w3-center">' . 
    	'<p style="color:red;">User not found.</p>' . 
    	'<p><a class="one" target="_self" href="home.php">Get back to the hot tub!</a></p></div>');
      } else {
        while ($row = $stmt->fetch()) {
          $id = "{$row['u_id']}";
          $user = "{$row['user_name']}";
          $_SESSION['id'] = $id;
          $_SESSION['user'] = $user; 
        }
      }
    }
  } catch (Exception $e) {
    echo "DB exception: $e";
  }
	
  if ((isset($_SESSION['user_id']))) {
    // Identify if user profile being viewed is being followed by currently logged in user
    $followObj = new Follow();
    $following = $followObj->getFollowFlag($_SESSION['user_id'], $_SESSION['id']);
    $_SESSION['following'] = $following;
    $_SESSION['logged_in_user'] = true;
  } else { 
    $_SESSION['following'] = false;
    $_SESSION['logged_in_user'] = false;
  }


?>

  
<script>
  var oldCount;
  var logged_in_user = Boolean("<?php echo $_SESSION['logged_in_user']; ?>");

  var id = "<?php echo $_SESSION['id']; ?>";
  var user = "<?php echo $_SESSION['user']; ?>";
  var following = Boolean("<?php echo $_SESSION['following']; ?>");
  var oldCount = 0;
  
  function thousands_separator(num) {
    var numParts = num.toString().split(".");
    numParts[0] = numParts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return numParts.join(".");
  } 

  function performAsync(message, callback) {
    return new Promise( (resolve, reject) => {
      console.log(message);
      resolve();
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
      return performAsync("Loading comments...");
    }).then( () => {
      loadComments();
      return performAsync("Done!");
    });
  }
  
  // get_index_avatar.php makes no use of 'user_id' session variable
  function loadAvatar() {
    var avatarRequest = new XMLHttpRequest();
    avatarRequest.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var avatar = JSON.parse(this.responseText);
        $('#avatar').attr("src", avatar[0].avatar);
      }
    };
    avatarRequest.open("GET", "get_index_avatar.php", true);
    avatarRequest.send();
  }
  
  // get_status.php makes no use of 'user_id' session variable
  function loadStatusHistory() {
    var statusRequest = new XMLHttpRequest();
    statusRequest.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var obj = JSON.parse(this.responseText);
        var statusCnt = obj.length;
        
        /*
        if (statusCnt == 0) {
          var status = $( '<div>No status history retrieved...</div>');
          var mostRecentStatus = $('#status_history_container').first();
          status.prependTo(mostRecentStatus); 
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
            '<p>' + obj[x].created_at + '</p><hr></div>');
            var mostRecentStatus = $('#status_history_container').first();
            status.prependTo(mostRecentStatus);  
            	   
        }
      }
    };
    statusRequest.open("GET", "get_status.php", true);
    // xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    statusRequest.send();
  }

  // get_status.php makes no use of 'user_id' session variable 
  function rewriteStatus() {
    var statusRequest = new XMLHttpRequest();
    statusRequest.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var obj = JSON.parse(this.responseText);
        var statusCnt = obj.length;
        
        $('#status_history_container').empty();

        for ( var x = 0; x < statusCnt; x++ ) {
              
          var status = $( '<div id="status' + obj[x].status_id + '"' + 
          '<p>' + obj[x].status_text + '</p>' + 
          '<p>' + obj[x].created_at + '</p><hr></div>');
          
          var mostRecentStatus = $('#status_history_container').first();
          status.prependTo(mostRecentStatus);   
            
    	}   
      }
    };
    statusRequest.open("GET", "get_status.php", true);
    // xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    statusRequest.send();
  
  }
  
  // get_index_posts.php makes no use of 'user_id' session variable
  function loadPosts() {
    var postRequest = new XMLHttpRequest();
    postRequest.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var obj = JSON.parse(this.responseText);
        var postCnt = obj.length;
        
        if (postCnt == 0) {
          var post = $('<p id="noPosts" class="w3-center">' + user + ' hasn\'t posted yet...</p>');
          var mostRecentPost = $('#post_container').first();
          post.prependTo(mostRecentPost);   
          return; 
        } 
 
        for ( var x = 0; x < postCnt; x++ ) {
    
    	    if (obj[x].image == "true") {
    	      var post = $( '<div id="post' + obj[x].p_id + '" class="section w3-card-4">' +
    	      '<span style="float:left;"><img src="' + obj[x].avatar + '" alt="dubarub user avatar" height="40" width="47" class="w3-circle"/>' + 
    	      '</span><h2 class="title">' + obj[x].title +
              '</h2><img id="post" src="' + obj[x].photo_url + '" alt="" height="385" style="width:100%"></img>' +
              '<div class="metadata"><span class="credit">' + obj[x].photo_cred + '</span><br><br>' +
              '<p class="post_tags" style="margin-left:10px;">' +  
              '<br><i class="fa fa-user fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].user_name + 
              '<i class="fa fa-calendar-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + moment(obj[x].created_at, "YYYY-MM-DD kk:mm:ss").fromNow() + 
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + '<span id="comment_count_post' + obj[x].p_id + '">' + obj[x].comments + '</span>' + 
              '<i onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px;cursor:pointer"></i>' + obj[x].upvote + 
              '<div onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" style="position:relative;top:-8px;margin-right:10px;float:right;font-size:24px;color:#b36b00"><button class="w3-square fa fa-trophy"></button></div>' +
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div class="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" href="#" id="addComment">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form method="POST">' + 
			  '<div class="form-group"><label for="comment_text">Leave a comment</label>' + 
			  '<textarea id="comment_text' + obj[x].p_id + '" class="form-control" rows="3"></textarea></div>' + 
			  '</form><button onclick="submitComment(' + obj[x].p_id + ', ' + obj[x].comments + ')" class="commentSubmit btn btn-primary">Submit</button>' + 
			  '</div><hr><div class="post_comments" id="post_comments' + obj[x].p_id + '"></div></div>');
    	    } else if (obj[x].video == "true") {
    		  var post = $( '<div id="post' + obj[x].p_id + '" class="section w3-card-4" style="height:385">' + 
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
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + '<span id="comment_count_post' + obj[x].p_id + '">' + obj[x].comments + '</span>' +
              '<i onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px;cursor:pointer"></i>' + obj[x].upvote + 
              '<div onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" style="position:relative;top:-8px;margin-right:10px;float:right;font-size:24px;color:#b36b00"><button class="w3-square fa fa-trophy"></button></div>' +
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div class="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" id="addComment" href="#">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form method="POST">' + 
			  '<div class="form-group"><label for="comment_text">Leave a comment</label>' + 
			  '<textarea id="comment_text' + obj[x].p_id + '" class="form-control" rows="3"></textarea></div>' + 
			  '</form><button onclick="submitComment(' + obj[x].p_id + ', ' + obj[x].comments + ')" class="commentSubmit btn btn-primary">Submit</button>' + 
			  '</div><hr><div class="post_comments" id="post_comments' + obj[x].p_id + '"></div></div>');
    	    } else if (obj[x].external == "true") {
    		  var post = $( '<div id="post' + obj[x].p_id + '" class="section w3-card-4">' + 
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
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + '<span id="comment_count_post' + obj[x].p_id + '">' + obj[x].comments + '</span>' +  
              '<i onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px;cursor:pointer"></i>' + obj[x].upvote + 
              '<div onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" style="position:relative;top:-8px;margin-right:10px;float:right;font-size:24px;color:#b36b00"><button class="w3-square fa fa-trophy"></button></div>' +
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div id="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" id="addComment" href="#">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form method="POST">' + 
			  '<div class="form-group"><label for="comment_text">Leave a comment</label>' + 
			  '<textarea id="comment_text' + obj[x].p_id + '" class="form-control" rows="3"></textarea></div>' + 
			  '</form><button onclick="submitComment(' + obj[x].p_id + ', ' + obj[x].comments + ')" class="commentSubmit btn btn-primary">Submit</button>' + 
			  '</div><hr><div class="post_comments" id="post_comments' + obj[x].p_id + '"></div></div>');
    	    } else {
    		  var post = $( '<div id="post' + obj[x].p_id + '" class="section w3-card-4">' + 
    		  '<span style="float:left;"><img src="' + obj[x].avatar + '" alt="dubarub user avatar" height="40" width="47" class="w3-circle"/>' + 
    		  '</span><h2 class="title">' + obj[x].title + '</h2>' + 
    		  '<div class="metadata"><p class="post_tags" style="margin-left:10px;">' +
              '<br><i class="fa fa-user fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].user_name +
              '<i class="fa fa-calendar-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' +  moment(obj[x].created_at, "YYYY-MM-DD kk:mm:ss").fromNow() + 
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + '<span id="comment_count_post' + obj[x].p_id + '">' + obj[x].comments + '</span>' +
              '<i onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px;cursor:pointer"></i>' + obj[x].upvote + 
              '<div onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" style="position:relative;top:-8px;margin-right:10px;float:right;font-size:24px;color:#b36b00"><button class="w3-square fa fa-trophy"></button></div>' +
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div class="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" id="addComment" href="#">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form>' + 
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
    postRequest.open("GET", "get_index_posts.php", true);
    postRequest.send();
  
  }
	
  // get_index_tags.php makes no use of 'user_id' session variable
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
    tagRequest.open("GET", "get_index_tags.php", true);
    // xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    tagRequest.send();
  
  }
  
  // get_comments.php makes no use of 'user_id' session variable 
  function loadComments() {
    var commentRequest = new XMLHttpRequest();
    commentRequest.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var comments = JSON.parse(this.responseText);
        var len = comments.length;
        
        // revise for user who isn't logged in 
        var value = "<?php echo ( (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : 0 ); ?>";
        //console.log("value is: " + value);
        var user_id = parseInt(value);
        
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
            
            //console.log("comment owner user id: " + comments[i].u_id + " and current user: " + user_id);
            
            if ( comments[i].u_id == user_id ) {
              $('#post' + comments[i].p_id).find('.post_comments').append('<div id="comment' + comments[i].c_id +  '" class="media">' +
  			  '<div class="media-left">' + 
  			  '<a href="#"><img style="margin-left:5px" height="64" width="64" class="media-object" src="' + comments[i].avatar + '" alt="user avatar"></a>' +
 			  '</div><div style="position:relative;top:-5px;text-align:left;" class="media-body"><div id="commenter' + comments[i].c_id + '" style="font-size:14px;" class="media-heading"><b><a id="comment_owner_link" href="user.php?name=' + comments[i].user_name + '">' + comments[i].user_name + '</a></b> says:</div>' + 
 			  '<div class="comment_body' + comments[i].p_id + '" style="margin-bottom:2px;font-size:12px">' + comments[i].comment + '</div>' + 
 			  '<div onclick="removeComment(this)" data-postid="' + comments[i].p_id + '" class="remove_comment" data-commid="' + comments[i].c_id + '" style="position:relative;bottom:22px;margin-right:10px;float:right">' + 
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
 			  '<div class="comment_body" style="margin-bottom:5px;font-size:12px">' + comments[i].comment + '</div>' + 
 			  '<div style="clear:both;font-size:12px" class="comment_options flex-container">' + 
 			  '<div class="comment_timestamp">' + moment(comments[i].timestamp, "YYYY-MM-DD kk:mm:ss").fromNow() + '</div>' +
 			  '<div class="upvote">' + comments[i].upvote + " " + trophyAmount + '</div>' +
 			  '<div onclick="handCommentTrophy(this)" class="comment_trophy" data-commid="' + comments[i].c_id + '"><i class="fa fa-trophy" style="color:#b36b00" aria-hidden="true"></i></a></div>' + 
 			  '<div onclick="flagComment(this)" class="comment_flag" data-commid="' + comments[i].c_id + '" style="position:relative;left:15px;"><i style="color:red" class="fa fa-flag" aria-hidden="true"></i></div>' +
 			  '</div></div>');
 			}
          }
        } else {
          $('.post_comments').append('<div id="no_comment">Be the first to comment...</div>');
        }
      }
    };
    commentRequest.open("GET", "get_comments.php", true);
    commentRequest.send();
  }
  
  // status_count.php makes no use 'user_id' session variable 
  function statusCount() {
    var statusCount = new XMLHttpRequest();
    statusCount.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var data = JSON.parse(this.responseText);
        var statusId = data.status_id;
        oldCount = statusId - document.getElementById("status_history_container").childNodes.length;
        //console.log("oldCount value: " + oldCount);
        //alert(oldCount);
      }
    }; 
    statusCount.open("GET", "status_count.php", true);
    // xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    statusCount.send();
  
  }
  
  // query_status.php makes no use of 'user_id' session variable 
  function loadStatusEngine() {
    var statusEngine = new XMLHttpRequest();
    statusEngine.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        console.log('Data transmitted...');
      }
    };
    statusEngine.open("GET", "query_status.php", true);
    statusEngine.send();
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
    
    // Save comment to variable
    var comment = $('#comment_text' + postID).val();
    console.log(comment);
    console.log(postID);
      
    // Clear out comment textarea input
    $('#comment_text' + postID).val('');
          
    $.ajax({
      async: true,
      cache: false,
      url: 'comment_controller.php',  
      type: 'POST',
      dataType: 'json',
      data: { post_owner: 'false', comment_text: comment, post_id: postID }  
    }).done(function ( msg ) {
      console.log('comment submitted...');
      // Retrieve username
      console.log("Username of commenter: " + msg.user_name);
      var userName = msg.user_name;
      // Retrieve avatar 
      console.log("Avatar path of commenter: " + msg.avatar);
      var avatar = msg.avatar;
      
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
    
    // revise for user who isn't logged in 
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
    
    // revise for user who isn't logged in 
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
    
    // revise for user who isn't logged in 
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
    // revise for user who isn't logged in 
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
		
	  } else {
		if (confirm("You must be logged in to remove this comment. Sign up/Login?")) {
		  window.location.assign("https://dubarub.com");
		} else {
		  return;
		}
	  } 
	}        
  }
  
  function follow(elem) {
    console.log("Follow button clicked");
    
    // revise for user who isn't logged in 
    var logged_in = Boolean("<?php echo (isset($_SESSION['user_id']) ? true : false); ?>");
    	
    if (logged_in === true) {	
	  var action = "Follow";
	  var renderedUserId = "<?php echo $_SESSION['id']; ?>";
	  var renderedUserName = "<?php echo $_SESSION['user']; ?>";
	    
	  $(elem).text("Unfollow");
	  $(elem).parent().attr("id", "unfollow_button_container");
	  $(elem).attr("onclick", "unfollow(this)");
	  
	  var currentFollowerCount = $("#profile_followers").text().match(/\d/g).join('');
	  currentFollowerCount = thousands_separator(currentFollowerCount);
	  currentFollowerCount = (Number(currentFollowerCount) + 1).toString();
	  
	  if (currentFollowerCount == 1) {
		$("#profile_followers").text(currentFollowerCount + " follower");
	  } else {
		$("#profile_followers").text(currentFollowerCount + " followers");
	  }
	    
		
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
    
    // revise for user who isn't logged in 
    var logged_in = Boolean("<?php echo (isset($_SESSION['user_id']) ? true : false); ?>");
    
    if (logged_in === true) {
      var action = "Unfollow";
	  var renderedUserId = "<?php echo $_SESSION['id']; ?>";
	  var renderedUserName = "<?php echo $_SESSION['user']; ?>";
	  
	  $(elem).text("Follow");
	  $(elem).attr("onclick", "follow(this)");
	  
	  $(elem).text("Follow");
	  $(elem).parent().attr("id", "follow_button_container");
	  $(elem).attr("onclick", "follow(this)");
	  
	  var currentFollowerCount = $("#profile_followers").text().match(/\d/g).join('');
	  currentFollowerCount = thousands_separator(currentFollowerCount);
	  currentFollowerCount = (Number(currentFollowerCount) - 1).toString();
	  
	  if (currentFollowerCount == 1) {
		$("#profile_followers").text(currentFollowerCount + " follower");
	  } else {
		$("#profile_followers").text(currentFollowerCount + " followers");
	  }
      
      
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
  
  function viewFollowers() {
    console.log("followers link clicked");
    
    // revise for user who isn't logged in 
    var logged_in = Boolean("<?php echo (isset($_SESSION['user_id']) ? true : false); ?>");
    
    if (logged_in === true) {
      window.location.assign("https://dubarub.com/followers.php");
    } else {
      if (confirm("You must be logged in to view this user followers. Sign up/Login?")) {
        window.location.assign("https://dubarub.com");
      } else {
        return;
      }
    }
  }
  
  function viewFollowing() {
    console.log("following link clicked");
    
    // revise for user who isn't logged in 
    var logged_in = Boolean("<?php echo (isset($_SESSION['user_id']) ? true : false); ?>");
    
    if (logged_in === true) {
      window.location.assign("https://dubarub.com/following.php");
    } else {
      if (confirm("You must be logged in to view who this user is following. Sign up/Login?")) {
        window.location.assign("https://dubarub.com");
      } else {
        return;
      }
    }
  }
  
  $(document).ready(function() { 
  
    sequenceAsync();
    setTimeout(function() { statusCount(); }, 3000);
    loadStatusEngine();
    
    /*
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
    */
    
    // Get ready to receive status update events 
    if (!!window.EventSource) { 
      var source = new EventSource('query_status.php');
    } else {
      // Result to xhr polling 
    }
	
	/*
    var source = new EventSource('query_status.php');
       
    source.addEventListener('open', function(event) {
      // Connection was opened
      var data = JSON.parse(event.data);
      //loadStatus();
      //alert("opened");
    }, false);
    */
    
    /*
     * When updates are pushed from the server, the onmessage handler fires and new data
     * will be available in its e.data property. The magical part is that whenever the 
     * connection is closed, the browser will automatically reconnect to the source after
     * ~3 seconds. Your server implementation can even have control over this reconnection
     * timeout. 
    */
    
    var parent = document.getElementById("status_history_container");
    var child = document.getElementById("status_history_container").childNodes[0];
    
    source.addEventListener('message', function(event) {
      var data = JSON.parse(event.data);
      //var accum = data.status_id - document.getElementById("status_history_container").childNodes.length;
      //$('#status_history_container').empty();
      $('#status_update').text(data.status_text);
      $('#status_time').text(data.created_at);
      var status = $( '<div id="status' + data.status_id + '"' + 
        '<p>' + data.status_text + '</p>' + 
        '<p>' + data.created_at + '</p><hr></div>');
      
      setTimeout(function() { 
        var newCount = data.status_id - document.getElementById("status_history_container").childNodes.length; 
        //console.log("statusId in 'message' event: " + data.status_id);
        //console.log("Number of status updates in status history container: " + document.getElementById("status_history_container").childNodes.length);
        //console.log("newCount value (" + data.status_id  + "-" +  document.getElementById("status_history_container").childNodes.length + "status updates in status history container: " + newCount);
        if ( newCount > oldCount) {
          rewriteStatus();
          statusCount();
        } else if (newCount < oldCount) {
          rewriteStatus();
          statusCount();
        }
      }, 3000);
    }, false);
    
    source.addEventListener('error', function(event) {
      if (event.readyState == EventSource.CLOSED) {
        alert("closed");
        // Connection was closed.
      }
    }, false);
  
    $('#flashback').click(function() {
      // Placeholder  
    });  
    
    $('#direct_message').click(function() {
      document.getElementById('message_modal').style.display = 'block';
    }); 
    
    // If current logged in user has the same user id as user id of profile being 
    // currently viewed then hide follow/unfollow button 
    if (logged_in_user) {
      
      // revise for visitor 
      var user_id = "<?php echo (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : 0; ?>";
      if (user_id == id) {
        /*
        $('#follow_button_container').css('display', 'none');  
        */
        $('#direct_message').css('display', 'none');
      }
      
      if (following == true) {   
        $('#unfollow_button_container').css('display', 'block');
      } else {
        if (user_id == id) {
          return;
        } else {
          $('#follow_button_container').css('display', 'block');
        }
      }
    } else {
      console.log("user not logged. show follow button still...");
      $('#follow_button_container').css('display', 'block');
    }

  
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
	  </div>

   
	  <div id="my_playlist" class="w3-container music-tab">
	    <?php
	    $user_id = $_SESSION['id'];
	    $playlist = new Playlist();
	    $playlist->setUID($user_id);
	    $output = $playlist->getPlaylist();
	    if (!($output)) {
	      echo "No tracks added to playlist yet...";
	    } else {
	      echo $output;
	    }
	    ?>
	  </div>

	  <div class="w3-container w3-light-grey w3-padding">
		<button class="w3-button w3-left w3-red w3-border"
		  onclick="$( '#music-dialog' ).css('display', 'none');">Close
		</button>
	  </div>

	</div>
  </div>
  
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
  
  <!-- status flashback dialog -->
  <div id="flashback-dialog" class="w3-modal">
	<div class="w3-modal-content w3-card-4 w3-animate-zoom">
	  <header class="w3-container w3-yellow">
		<span onclick="document.getElementById('flashback-dialog').style.display = 'none'"
		class="w3-button w3-red w3-display-topright">&times;</span>
		<h2 style="color:white;">Status Flashback</h2>
	  </header>
	  
	  <div id="flashback_container" class="w3-container status-tab">
	    <div id="status_history_container">
	      <?php
	      	
            $id = $_SESSION['id'];
            $status = new Status();
            $lastUpdate = $status->getText($id);
            
            if (empty($lastUpdate)) {
              echo "<p>No status history to retrieve...</p>";
            } else {
              echo "<p>Loading...</p>";
            }
            
	      ?>
	    </div>
	  </div>	
	  
      <div class="w3-container w3-light-grey w3-padding">
		<button class="w3-button w3-left w3-red w3-border"
		  onclick="$( '#flashback-dialog' ).css('display', 'none');">Close
		</button>
	  </div>

	</div>
  </div>
  
  <div id="profile_bio_container">
    
    <div id="bio_avi">
      <img id="avatar" src="
       <?php 
        $id = $_SESSION['id'];
        $user = new User();
        $user->setUserId($id);
        echo $user->getAvatar();
      ?>" width="90" height="90"></img>
    </div>
    
    <div id="bio_username">
      <?php 
        $id = $_SESSION['id'];
        $userName = new User();
        echo "<b>" . $userName->getUsername($id) . "</b>"; // must be output this way or else error    
      ?>
    </div>
    
    <!-- Hide display -->
    <div id="bio_tagline_container">
    </div>
    
    <div id="bio_action_container">
      <i id="direct_message" class="fa fa-paper-plane action_items" aria-hidden="true"></i>
    </div>
    
    <div id="follow_button_container">
      <a onclick="follow(this)" class="btn btn-primary btn-sm active" role="button" aria-pressed="true">Follow</a>
    </div>
    
    <div id="unfollow_button_container">
      <a onclick="unfollow(this)" class="btn btn-primary btn-sm active" role="button" aria-pressed="true">Unfollow</a>
    </div>
    
    <div id="profile_stats">
      <span id="profile_followers"><a style="cursor:pointer" onclick="viewFollowers()">
        <?php
          $follow = new Follow();
          $followers = $follow->getFollowerCount($_SESSION['id']);
          if ($followers == 1) {
            echo $followers . " follower";
          } else {
            echo $followers . " followers";
          }
        ?>
      </a></span>
      <span id="profile_following"><a style="cursor:pointer" onclick="viewFollowing()">
        <?php
          $follow = new Follow();
          $following = $follow->getFollowingCount($_SESSION['id']);
          echo $following . " following";
        ?>
      </a></span>
      <span id="profile_posts">
      <?php
      
        $id = $_SESSION['id'];
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
       <?php
         
         $id = $_SESSION['id'];
         $bio = new User();
         $bio->setUserId($id);
         echo $bio->getBio();
       
       ?>
    </div>
    
  </div>
  
  <div id="status_container">
    <span id="status_update">
    
      <?php
        $id = $_SESSION['id'];
        $status = new Status();
        $lastUpdate = $status->getText($id);
        
        
        if (empty($lastUpdate)) {
          echo '{' . $_SESSION['user'] . ' hasn\'t made a status update yet. [womp, womp, wommppp]}';
        } else {
          echo htmlentities($lastUpdate);
        }
      ?>
      
    </span>
    <br>
  </div>
  <div id="status_date">
    <span id="status_time">
        <?php
          
          $id = $_SESSION['id'];
          $timestamp = new Status();
          echo $timestamp->getTimestamp($id);
          
        ?>
    </span>
  </div>
  <span id="flashback">
    <i onclick="document.getElementById('flashback-dialog').style.display='block'" class="fa fa-bolt fa-lg" aria-hidden="true"></i> 
  </span>
  
  <!-- start mobile display -->
  
  <div id="up_shortcut" style="z-index:999999">
    <a href="#profile_bio_container" class="btn btn-primary btn-sm active" role="button" aria-pressed="true">
      <span class="glyphicon glyphicon-circle-arrow-up"></span>
    </a>
  </div>
  
  <div id="close_flashback" style="z-index:999999">
    <span class="glyphicon glyphicon-remove-sign btn btn-primary btn-sm active" role="button" aria-pressed="true"></span>
  </div>
  
  <!-- end mobile display -->
  
  <div id="post_container"></div>
  
  <?php include_once("php_inc/inc_music_source.php"); ?>
<?php include_once("php_inc/inc_footer.php"); ?>