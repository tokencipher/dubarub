<audio id="my-audio" controls>
  <source id="mp3-src" src="" type="audio/mpeg">
  <source id="ogg-src" src="" type="audio/ogg">
</audio>
  
<div id="aside"></div>

<div class="stereo"> 

<div id="cover_art_container">
  <div id="album_art" style="margin:auto;padding:1px;">
	<img id="cover_art" src="img/cover_art/vinyl_stub.jpg" width="325" height="280">
  </div>
</div>
  
<div class="container">
  <div id="jquery_jplayer_1" class="jp-jplayer"></div>

  <div id="jp_container_1" class="jp-audio">
	<div class="jp-style-single">
  
	  <div class="jp-title w3-center" >
		<ul>
		  <li>Insert track title here</li>
		</ul>
	  </div>

	  <div class="jp-gui jp-interace">
	  
		<ul class="jp-controls">
		  <li><a href="javascript:;" class="jp-play" tabindex="1"><i class="fa fa-play" aria-hidden="true"></i></a></li>
		  <li><a href="javascript:;" class="jp-pause" tabindex="1"><i class="fa fa-pause" aria-hidden="true"></i></a></li>
		  <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute"><i class="fa fa-volume-off" aria-hidden="true"></i></a></li>
		  <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute"><i class="fa fa-volume-up" aria-hidden="true"></i></a></li>
		</ul>

		<div class="jp-progress">
		  <div class="jp-seek-bar">
			<div class="jp-play-bar"></div>
		  </div>
		</div>
		 
		<div class="jp-time-holder">
		  <div class="jp-current-time"></div>
		</div>
		
		<div class="jp-volume-bar">
		  <div class="jp-volume-bar-value"></div>
		</div>

	  </div>
	  <div class="jp-no-solution">
		<span>Update Required</span>
		To play the media you will need to either update your browser to a recent version 
		or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">
		Flash plugin</a>.
	  </div>
	</div>
  </div>
</div>
</div>

<script>

  $(document).ready(function() {
    
    $( "a" ).click(function(e) {
      var elem = $( this );
      if ( elem.attr( "id" ) == "music-link") {
	    e.preventDefault();
	    $( "#music-dialog" ).css("display", "block");
      }
    });
    
    $("#jquery_jplayer_1").jPlayer({
	  ready: function() {
		$(this).jPlayer("setMedia", {
		  mp3: "",
		  oga: ""
		});
	  },
	  swfPath: "/js",
	  supplied: "mp3,oga"
	});
  
    $( ".track" ).css("cursor", "pointer");
	$( ".track:hover" ).css("background-color", "yellow"); 
	
	
	//function makeAlert(title) {
	  
	  /*
	  function tempAlert(msg,duration)
		{
		 var el = document.createElement("div");
		 el.setAttribute("style","position:absolute;top:40%;left:20%;background-color:white;");
		 el.innerHTML = msg;
		 setTimeout(function(){
		  el.parentNode.removeChild(el);
		 },duration);
		 document.body.appendChild(el);
		}
	  */
	/*  
	  var b_audio = document.getElementById('my-audio');
      b_audio.addEventListener('play', function(){
        alert("Now playing: " + title); 
      });
    }
    */
		
      
      $( ".track" ).mousedown(function(e) {
        e.preventDefault();
      });
      
	  $( ".track" ).dblclick(function(e) {
		console.log("double clicked");
	    $(".track").css("color", "black");    
	  
		var elem = $( this );
		var title = elem.data("title");
		var artist = elem.data("artist");
		var album = elem.data("album");
		var mp3_path = elem.data("mp3-path");
		var ogg_path = elem.data("ogg-path");
		var cover_art = elem.data("cover-art");
		
		( elem ).css("color", "red");
		$("#cover_art").attr("src", cover_art);
		
		
		/*
		var next_track = elem.next();
		var next_title = next_track.data("title");
		var next_mp3_path = next_track.data("mp3-path");
		var next_ogg_path = next_track.data("ogg-path");
		*/
				
		/*
		$("#jquery_jplayer_1").jPlayer("clearMedia");
		*/
		
		$(".jp-title ul li").text(title);
	
		$("#jquery_jplayer_1").jPlayer("setMedia", {
		  mp3: mp3_path, 
		  oga: ogg_path
		}).jPlayer("play");
		
		
		/*
	    $("#jquery_jplayer_1").bind($.jPlayer.event.ended, function(event) {
	      if (event.jPlayer.status.waitForPlay) {
	        $("textarea").text(event.jPlayer.status.waitForPlay);
	        $(this).jPlayer("setMedia", {
	          mp3: next_mp3_path,
	          oga: next_ogg_path
	        }).jPlayer("play");
	      }
	    });
	    */
		
		/*
		if ( $("#jquery_jplayer_1").jPlayer("playHead", 99) ) {
		  $("#jquery_jplayer_1").jPlayer("setMedia", {
		    mp3: next_mp3_path, 
		    oga: next_ogg_path
		  }).jPlayer("play");  
		  
		  $(".jp-title ul li").text(next_title);
		}
		*/
		
		//$("#jquery_jplayer_1").jPlayer("play");
		
		/*
		$("#jquery_jplayer_1").jPlayer("ended", function(event) {
		  $(this).jPlayer("setMedia", {
		    mp3: next_mp3_path,
		    oga: next_ogg_path
		  });
		});
		
		$("#jquery_jplayer_1").jPlayer("play");
        */
		
		/*
		$("#jquery_jplayer_1").jPlayer({
		  ready: function() {
		    $(this).jPlayer("setMedia", {
		      mp3: mp3_path, 
		      oga: ogg_path
		    }).jPlayer("play");
		  },
		  ended: function() {
		    $(this).jPlayer("setMedia", {
		      mp3: next_mp3_path,
		      oga: next_ogg_path
		    }).jPlayer("play");
		  },
		    swfPath: "/js",
		    supplied: "mp3,oga"
		  });
		});
		*/
	  	  
	  });
	  
	  
	  // For switching track on mobile
    if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
      
      $(".track").on("click", function() {
        
        $(".track").css("color", "black");
        
        var audio = $("#my-audio");
        var elem = $( this );
        
        ( elem ).css("color", "red");
        
		var title = elem.data("title");
		var artist = elem.data("artist");
		var album = elem.data("album");
		var mp3_path = elem.data("mp3-path");
		var ogg_path = elem.data("ogg-path");
		var cover_art = elem.data("cover-art");
		
        
        //makeAlert(title);
        
        /*
        audio.bind("play", function() {
          alert("Now Playing: " + title);
        });
        */    
        
        //alert(mp3_path);
        
        $("#my-audio").off("play");
        $("#mp3-src").attr("src", mp3_path);
        
        //var test = $("#mp3-src").attr("src");
        
        $("#ogg-src").attr("src", ogg_path);
        // $("#cover_art").attr("src", cover_art);
        
        //alert(test);
        
        audio[0].pause();
        audio[0].load();     
        
        audio.trigger("play");
          
      });
      
    } // End mobile detection
	  
  });
  
</script>