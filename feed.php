<?php include_once('php_inc/inc_header.php');?>

  <title>dubarub | Feed</title>  
  
<?php include_once ('php_inc/inc_user_nav.php'); ?>
</head>
<body>
  
  <script>  
  
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

    function rewritePosts() {
      var postRequestTwo = new XMLHttpRequest();
      postRequestTwo.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          var obj = JSON.parse(this.responseText);
          var postCnt = obj.length;
        
          $('#post_container').empty();

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
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].comments +  
              '<i onclick="handPostTrophy(this)" class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px" data-postid="' + obj[x].p_id + '"></i>' + obj[x].upvote + 
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div class="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" href="#" id="addComment">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form action="user.php" method="POST" enctype="multipart/form-data">' + 
			  '<div class="form-group"><label for="comment_text">Leave a comment</label>' + 
			  '<textarea id="comment_text" name="comment_text" class="form-control" rows="3" required></textarea></div>' + 
			  '<button onclick="submitComment(event,' + obj[x].p_id + ')" id="commentSubmit" class="btn btn-primary">Submit</button></form>' + 
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
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].comments +  
              '<i onclick="handPostTrophy(this)" class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px" data-postid="' + obj[x].p_id + '"></i>' + obj[x].upvote + 
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div class="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" id="addComment" href="#">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form action="user.php" method="POST" enctype="multipart/form-data">' + 
			  '<div class="form-group"><label for="comment_text">Leave a comment</label>' + 
			  '<textarea id="comment_text" name="comment_text" class="form-control" rows="3" required></textarea></div>' + 
			  '<button onclick="submitComment(event,' + obj[x].p_id + ')" id="commentSubmit" class="btn btn-primary">Submit</button></form>' + 
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
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].comments +  
              '<i class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px"></i>' + obj[x].upvote + 
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div id="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" id="addComment" href="#">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form action="user.php" method="POST" enctype="multipart/form-data">' + 
			  '<div class="form-group"><label for="comment_text">Leave a comment</label>' + 
			  '<textarea id="comment_text" name="comment_text" class="form-control" rows="3" required></textarea></div>' + 
			  '<button onclick="submitComment(event,' + obj[x].p_id + ')" id="commentSubmit" class="btn btn-primary">Submit</button></form>' +
			  '</div><hr><div class="post_comments" id="post_comments' + obj[x].p_id + '"></div></div>');
    	    } else {
    		  var post = $( '<div id="post' + obj[x].p_id + '" class="section w3-card-4">' + 
    		  '<span style="float:left;"><img src="' + obj[x].avatar + '" alt="dubarub user avatar" height="40" width="47" class="w3-circle"/>' + 
    		  '</span><h2 class="title">' + obj[x].title + '</h2>' + 
    		  '<div class="metadata"><p class="post_tags" style="margin-left:10px;">' +
              '<br><i class="fa fa-user fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].user_name +
              '<i class="fa fa-calendar-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' +  moment(obj[x].created_at, "YYYY-MM-DD kk:mm:ss").fromNow() + 
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].comments +
              '<i onclick="handPostTrophy(this)" class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px" data-postid="' + obj[x].p_id + '"></i>' + obj[x].upvote + 
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div class="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" id="addComment" href="#">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form action="user.php" method="POST" enctype="multipart/form-data">' + 
			  '<div class="form-group"><label for="comment_text">Leave a comment</label>' + 
			  '<textarea id="comment_text" name="comment_text" class="form-control" rows="3" required></textarea></div>' + 
			  '<button onclick="submitComment(event,' + obj[x].p_id + ')" id="commentSubmit" class="btn btn-primary">Submit</button></form>' + 
			  '</div><hr><div class="post_comments" id="post_comments' + obj[x].p_id + '"></div></div>');
    	    } 	  
              
            var mostRecentPost = $('#post_container').first();
            post.prependTo(mostRecentPost);    
    
    	  }   
        }
      };
      postRequestTwo.open("GET", "feed_controller.php", true);
      postRequestTwo.send();
  
    }
  
	function loadFeed() {
	  var postRequest = new XMLHttpRequest();
      postRequest.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          var obj = JSON.parse(this.responseText);
          var postCnt = obj.length;
        
          if (postCnt == 0) {
            var post = $('<p id="noPosts" class="w3-center">None of your followers has posted yet...</p>');
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
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].comments +  
              '<i onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px;cursor:pointer"></i>' + obj[x].upvote + 
              '<div onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" style="position:relative;top:-8px;margin-right:10px;float:right;font-size:24px;color:#b36b00"><button class="w3-square fa fa-trophy"></button></div>' +
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div class="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" href="#" id="addComment">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form action="user.php" method="POST" enctype="multipart/form-data">' + 
			  '<div class="form-group"><label for="comment_text">Leave a comment</label>' + 
			  '<textarea id="comment_text" name="comment_text" class="form-control" rows="3" required></textarea></div>' + 
			  '<button onclick="submitComment(event,' + obj[x].p_id + ')" id="commentSubmit" class="btn btn-primary">Submit</button></form>' + 
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
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].comments +  
              '<i onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px;cursor:pointer"></i>' + obj[x].upvote + 
              '<div onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" style="position:relative;top:-8px;margin-right:10px;float:right;font-size:24px;color:#b36b00"><button class="w3-square fa fa-trophy"></button></div>' +
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div class="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" id="addComment" href="#">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form action="user.php" method="POST" enctype="multipart/form-data">' + 
			  '<div class="form-group"><label for="comment_text">Leave a comment</label>' + 
			  '<textarea id="comment_text" name="comment_text" class="form-control" rows="3" required></textarea></div>' + 
			  '<button onclick="submitComment(event,' + obj[x].p_id + ')" id="commentSubmit" class="btn btn-primary">Submit</button></form>' + 
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
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].comments +  
              '<i onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px;cursor:pointer"></i>' + obj[x].upvote + 
              '<div onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" style="position:relative;top:-8px;margin-right:10px;float:right;font-size:24px;color:#b36b00"><button class="w3-square fa fa-trophy"></button></div>' +
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div id="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" id="addComment" href="#">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form action="user.php" method="POST" enctype="multipart/form-data">' + 
			  '<div class="form-group"><label for="comment_text">Leave a comment</label>' + 
			  '<textarea id="comment_text" name="comment_text" class="form-control" rows="3" required></textarea></div>' + 
			  '<button onclick="submitComment(event,' + obj[x].p_id + ')" id="commentSubmit" class="btn btn-primary">Submit</button></form>' +
			  '</div><hr><div class="post_comments" id="post_comments' + obj[x].p_id + '"></div></div>');
    	    } else {
    		  var post = $( '<div id="post' + obj[x].p_id + '" class="section w3-card-4">' + 
    		  '<span style="float:left;"><img src="' + obj[x].avatar + '" alt="dubarub user avatar" height="40" width="47" class="w3-circle"/>' + 
    		  '</span><h2 class="title">' + obj[x].title + '</h2>' + 
    		  '<div class="metadata"><p class="post_tags" style="margin-left:10px;">' +
              '<br><i class="fa fa-user fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].user_name +
              '<i class="fa fa-calendar-o fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' +  moment(obj[x].created_at, "YYYY-MM-DD kk:mm:ss").fromNow() + 
              '<br><i class="fa fa-comments fa-lg" aria-hidden="true" style="margin-left:5px;padding-right:2px;"></i>' + obj[x].comments +
              '<i onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px;cursor:pointer"></i>' + obj[x].upvote + 
              '<div onclick="handPostTrophy(this)" data-postid="' + obj[x].p_id + '" style="position:relative;top:-8px;margin-right:10px;float:right;font-size:24px;color:#b36b00"><button class="w3-square fa fa-trophy"></button></div>' +
              '<i onclick="handPostTrophy(this)" class="fa fa-trophy fa-lg post_trophy" aria-hidden="true" style="color:#b36b00;margin-left:5px;padding-right:2px" data-postid="' + obj[x].p_id + '"></i>' + obj[x].upvote + 
              '</p></div><hr><p class="entry">' + obj[x].entry + '</p>' + 
              '<div class="post_options" style="position:relative;font-size:16px;font-family:\'Aref Ruqaa\',serif;text-align:justify;top:20px;padding:10px;">' +
              '<button onclick="toggleComment(' + obj[x].p_id + ')" id="toggle_comments" style="text-align:left;color:blue;text-decoration:underline;">Show/Hide Comments</button>' +
              '<span style="float:right;color:blue;text-decoration:underline;"><a onclick="toggleCommentBox(event,' + obj[x].p_id + ')" id="addComment" href="#">Add Comment</a></span>' +
			  '</div><br><div class="comment_box" id="comment_box' + obj[x].p_id + '" style="padding:5px;"><form action="user.php" method="POST" enctype="multipart/form-data">' + 
			  '<div class="form-group"><label for="comment_text">Leave a comment</label>' + 
			  '<textarea id="comment_text" name="comment_text" class="form-control" rows="3" required></textarea></div>' + 
			  '<button onclick="submitComment(event,' + obj[x].p_id + ')" id="commentSubmit" class="btn btn-primary">Submit</button></form>' + 
			  '</div><hr><div class="post_comments" id="post_comments' + obj[x].p_id + '"></div></div>');
    	    } 	  
              
            var mostRecentPost = $('#post_container').first();
            post.prependTo(mostRecentPost);    
    
          }  
        }
      };
      postRequest.open("GET", "feed_controller.php", true);
      postRequest.send();
    }
    
    $(document).ready(function() {
      loadFeed();
      setInterval(function() {
        rewritePosts();
      }, 3000); 
    });
  </script>


  <div id="post_container"></div>
<?php include_once('php_inc/inc_user_footer.php'); ?>
