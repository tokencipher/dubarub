<?php

// inbox.php

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
<title><?php echo $_SESSION['user_name']; ?> | Inbox</title>
<?php require_once ('php_inc/inc_user_home_nav.php'); ?>

</head>
<body>

  <div id="message_container" class="w3-container"></div>
  
  <div id="message_thread_container">
    <iframe src="" height="100%" width="100%" style="border:none"></iframe>
  </div>
  
  <script src="node_modules/socket.io-client/dist/socket.io.js"></script>
  <script>
    var socket = io('https://dubarub.com:4200');
    var userID = "<?php echo $_SESSION['user_id']; ?>";
    var userName = "<?php echo $_SESSION['user_name']; ?>";
    
    function removeMessage(element) {
	  console.log("remove message item clicked");
	  var remove_flag = Boolean("<?php echo (isset($_SESSION['user_id']) ? true : false); ?>");
		
	  if (remove_flag === true) {
		if (confirm("Are you sure you want to delete this message?")) {
		
		  var message = $( element );
		  var messageID = message.data("messageid");
		
		  var action = "Remove Message";
		
		  $.ajax({
			async: true,
			cache: false,
			url: 'user_action.php',  
			type: 'POST',
			data: { user_action: action, message_id: messageID }  
		  }).done(function ( msg ) {
			console.log('Remove comment action taken...');
			// Remove comment element from DOM 
			$('#message' + messageID).remove();
			var messages = $('#message_container').children().length;
			if (messages == 0) {
			  $('iframe').css('display', 'none');
			}
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
    
    function openMessageThread(elem) {    
      var messageID = $(elem).data('messageid');
      var sender = $(elem).data('sender');
      var senderID = $(elem).data('senderid');    
      
      // TODO: set unread flag to false for corresponding message id 
      $.ajax({
        async: true,
        cache: false,
        url: 'set_message_unread_flag.php',  
        type: 'POST',
        data: { user_name: userName, message_id: messageID }  
      }).done(function ( data ) {
        console.log('Opening message thread with message id: ' + messageID + ' for ' + userName + ' with id ' + userID + ' sent from ' + sender + ' who has id ' + senderID);
        if (screen.width >= 768) {
          $('iframe').attr('src', 'https://dubarub.com:4200/message_thread/' + messageID + '/' + userName + '/' + userID + '/' + sender + '/' + senderID);
          $('iframe').css('display', 'block');
        } else {
          window.location.assign('https://dubarub.com:4200/message_thread/' + messageID + '/' + userName + '/' + userID + '/' + sender + '/' + senderID);
        }
      }).fail(function ( xhr, textStatus) {
        console.log(xhr.statusText);
      });
      
      
    }

	function loadMailbox() {
	  var mailboxRequest = new XMLHttpRequest();
	  mailboxRequest.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
		  var message = JSON.parse(this.responseText);
		  var messageCnt = message.length;
		
		  /*
		  if (messageCnt == 0) {
			var noMessage = $('<p class="noMessage" class="w3-center">No messages to load...</p>');
			var mostRecentMessage = $('#message_container').first();
			noMessage.prependTo(mostRecentMessage);
			return;
		  }
		  */
	
	
		  if (messageCnt == 0) {
		    var alert = $('<p id="noMessages" class="w3-center">Your inbox is empty...</p>');
			var mostRecentMessage = $('#message_container').first();
			alert.prependTo(mostRecentMessage);   
			return; 
		  }
		  

		  for ( var x = 0; x < messageCnt; x++ ) {
		  
		    if (message[x].unread == 'true') {
		  
			  var messageElement = $('<div onclick="openMessageThread(this)" data-messageid="' + message[x].message_id + '" data-sender="' + message[x].sender + '"data-senderid="' + message[x].sender_id + '" id="message' + message[x].message_id +  '" class="media direct-message">' +
			  '<div class="media-left">' + 
			  '<a href="#"><img style="margin-left:5px" height="64" width="64" class="media-object" src="' + message[x].avatar + '" alt="user avatar"></a>' +
			  '</div><div style="position:relative;top:-5px;text-align:left;" class="media-body"><div class="body" style="font-size:14px;" class="media-heading"><b>' + message[x].sender + '</b></div>' + 
			  '<div class="message-body" style="margin-bottom:2px;font-size:12px;font-weight:bold;">' + message[x].body + '->' + message[x].recipient + '</div>' + 
			  '<div onclick="event.stopPropagation(); removeMessage(this)" class="message-delete" data-messageid="' + message[x].message_id + '" id="delete_message' + message[x].message_id + '">' + 
			  '<i class="fa fa-times" style="color:red" aria-hidden="true"></i></div>' + 
			  '<div style="clear:both;font-size:12px" class="message_options flex-container">' + 
			  '<div class="message_timestamp">' + message[x].created_at + '</div>' +
			  '</div></div>');
			  
			} else {
			  
			  var messageElement = $('<div onclick="openMessageThread(this)" data-messageid="' + message[x].message_id + '" data-sender="' + message[x].sender + '"data-senderid="' + message[x].sender_id + '" id="message' + message[x].message_id +  '" class="media direct-message">' +
			  '<div class="media-left">' + 
			  '<a href="#"><img style="margin-left:5px" height="64" width="64" class="media-object" src="' + message[x].avatar + '" alt="user avatar"></a>' +
			  '</div><div style="position:relative;top:-5px;text-align:left;" class="media-body"><div class="body" style="font-size:14px;" class="media-heading">' + message[x].sender + '->' + message[x].recipient + '</div>' + 
			  '<div class="message-body" style="margin-bottom:2px;font-size:12px">' + message[x].body + '</div>' + 
			  '<div onclick="event.stopPropagation(); removeMessage(this)" class="message-delete" data-messageid="' + message[x].message_id + '" id="delete_message' + message[x].message_id + '">' + 
			  '<i class="fa fa-times" style="color:red" aria-hidden="true"></i></div>' + 
			  '<div style="clear:both;font-size:12px" class="message_options flex-container">' + 
			  '<div class="message_timestamp">' + message[x].created_at + '</div>' +
			  '</div></div>');
			
			}
		 
			var mostRecentMessage = $('#message_container').first();
			messageElement.prependTo(mostRecentMessage);   
			  
		  }	   
		}
	  };
	  mailboxRequest.open("GET", "retrieve_mailbox.php", true);
	  mailboxRequest.send();
	}
	  
	$(document).ready(function() {
	  loadMailbox();  
	  
	  socket.on('connect', function() {
	    console.log('Connected to message server...');
	    socket.emit('user connected', {userName: userName});
	  });
	  
	  socket.on('message', function(data) {
	    if (data.recipient == userName) {
	      var messageElement = $('<div onclick="openMessageThread(this)" data-messageid="' + data.message_id + '" data-sender="' + data.sender + '"data-senderid="' + data.sender_id + '" id="message' + data.message_id +  '" class="media direct-message">' +
			'<div class="media-left">' + 
			'<a href="#"><img style="margin-left:5px" height="64" width="64" class="media-object" src="' + data.avatar + '" alt="user avatar"></a>' +
			'</div><div style="position:relative;top:-5px;text-align:left;" class="media-body"><div class="body" style="font-size:14px;" class="media-heading"><b>' + data.sender + '->' + message[x].recipient + '</b></div>' + 
			'<div class="message-body" style="margin-bottom:2px;font-size:12px;font-weight:bold;">' + data.body + '</div>' + 
			'<div onclick="event.stopPropagation(); removeMessage(this)" class="message-delete" data-messageid="' + data.message_id + '" id="delete_message' + data.message_id + '">' + 
			'<i class="fa fa-times" style="color:red" aria-hidden="true"></i></div>' + 
			'<div style="clear:both;font-size:12px" class="message_options flex-container">' + 
			'<div class="message_timestamp">' + data.created_at + '</div>' +
			'</div></div>');
		 
		  $('#noMessages').css('display', 'none');
		  var mostRecentMessage = $('#message_container').first();
		  messageElement.prependTo(mostRecentMessage);   
	    }
	    
	  });
	  
	});
	  
  </script>
  <script src="js/index.js"></script> 
  
</body>
</html>