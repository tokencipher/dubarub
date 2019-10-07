<nav class="navbar navbar-default navbar-fixed-top">
    <div id="header" class="w3-container w3-white" style="opacity:0.6">
      <img style="position:relative;top:6px" src="img/dubarub.jpg" alt="dubarub" id="dub" height="40" width="47" />
      <span style="position:relative;top:13px" id="logoTitle"> Public BETA </span>
      <div class="dropdown">
        <button class="btn  btn-lg  dropdown-toggle" id="dropdownMenu1" type="button"  aria-label="Dropdown Menu">
        <span class="glyphicon glyphicon-menu-hamburger"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu1">
          <li><a onmouseover="this.style.color='blue'" onmouseout="this.style.color='grey'" href="home.php">Home</a></li>
          <!--<li><a target="_blank" onmouseover="this.style.color='blue'" onmouseout="this.style.color='grey'" href="./video_preview.php">Video</a></li>-->
          <li><a target="_self" onmouseover="this.style.color='blue'" onmouseout="this.style.color='grey'" href="post_form.php">Add Post</a></li>
          <li><a target="_blank" onmouseover="this.style.color='blue'" onmouseout="this.style.color='grey'" id="music-link" href="#music-dialog">Playlist</a></li>
          <li><a target="_self" onmouseover="this.style.color='blue'" onmouseout="this.style.color='grey'" href="playlist_form.php">Add to Playlist</a></li>
          <li><a target="_self" onmouseover="this.style.color='blue'" onmouseout="this.style.color='grey'" href="playlist_modify.php">Delete from Playlist</a></li>
          <li><a target="_self" onmouseover="this.style.color='blue'" onmouseout="this.style.color='grey'" href="music_form.php">Upload Track(s)</a></li>
          <li><a target="_self" onmouseover="this.style.color='blue'" onmouseout="this.style.color='grey'" href="users.php">View All Users</a></li>
          <li><a target="_self" onmouseover="this.style.color='red'" onmouseout="this.style.color='grey'" href="logout.php?PHPSESSID=<?php echo session_id()?>">Logout</a></li>
          <!--<li><a target="_blank" onmouseover="this.style.color='blue'" onmouseout="this.style.color='grey'" href="./gallery.php">Gallery</a></li>-->
          <!--<li><a target="_blank" onmouseover="this.style.color='blue'" onmouseout="this.style.color='grey'" href="./working.php">Tags</a></li>-->
          <!--<li><a target="_blank" onmouseover="this.style.color='blue'" onmouseout="this.style.color='grey'" href="./working.php">About</a></li>-->
          <!--<li><a target="_blank" onmouseover="this.style.color='blue'" onmouseout="this.style.color='grey'" href="./working.php">Contact</a></li>-->
        </ul>
      </div>
    </div>
</nav>