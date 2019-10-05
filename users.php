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
    #profile_bio_container {}
    #bio_avi {
      position:relative;
      padding:8px;
      width:100px;
      height:100px;
    }
    #avatar {
      position:relative;
    }
    #bio_username_container {
      position:relative;
      top:-118px;
      left:88px;
      font-size:12px;
      width:100px;
      height:70px;
      overflow:scroll;
    }
    #bio_username {
      text-align:left;
    }
    #bio_tagline_container {
      position:relative;
      top:-75px;
      left:145px;
      display:none;
    }
    #bio_action_container {
      position:relative;
      top:-145px;
      left:45px;
      width:113px;
      height:25px;
      padding:2px;
    }
    #direct_message {
      font-size:22px;
      color:green;
      cursor:pointer;
    }
    #follow_button_container {
      position:relative;
      top:-70px;
      margin:auto;
      padding:10px;
      width:100%;
    }
    #unfollow_button_container {
      position:relative;
      top:-110px;
      left:110px;
      padding:0px;
      display:none;
    }
    #profile_stats {
      position:relative;
      left:1px;
      margin-left:2px;
      top:-200px;
      font-size:8px;
    }
    #profile_following {
      margin-left:13px;
    }
    #profile_posts {
      margin-left:13px;
    }
    #bio_text {
      position:relative;
      top:-54px;
    }
  </style>
</head>
<body class="flex_container">

  <div class="flex_item" id="profile_bio_container">
    
    <div id="bio_avi">
      <img id="avatar" src="img/model_flic.jpg" width="65" height="65"></img>
    </div>
    
    <div id="bio_username_container">
      <div id="bio_username">
        sinclairssfsdsdsdsdssdgdgdsgfd
      </div>
    </div>
    
    <!-- Hide display -->
    <div id="bio_tagline_container">
      Something to say 
    </div>
    
    <div id="bio_action_container">
      <i id="direct_message" class="fa fa-paper-plane action_items" aria-hidden="true"></i>
    </div>
    
    <div id="follow_button_container">
      <a href="#" onclick="follow()" class="btn btn-primary btn-sm btn-block active" role="button" aria-pressed="true">Follow</a>
    </div>
    
    <div id="unfollow_button_container">
      <a href="#" onclick="unfollow()" class="btn btn-primary btn-sm active" role="button" aria-pressed="true">Unfollow</a>
    </div>
    
    <div id="profile_stats">
      <span id="profile_followers">
        
      </span>
      <span id="profile_following">
        
      </span>
      <span id="profile_posts">

      </span>
    </div>
    
    <div id="bio_text">
    
    </div>
    
  </div>
  <div class="flex_item">2</div>
  <div class="flex_item">3</div>
  <div class="flex_item">4</div>
  <div class="flex_item">5</div>
  <div class="flex_item">6</div>
  <div class="flex_item">7</div>
  <div class="flex_item">8</div>
  <div class="flex_item">9</div>
  <div class="flex_item">10</div>
    
</body>
</html>