function stripHTML(str) {
  var StrippedString = str.replace(/(<([^>]+)>)/ig,"");
  return StrippedString;
}

$(document).ready(function() {
  var socket = io();
  
  var messageID = $('#message_id').text();
  var userName = $('#user_name').text();
  var userID = $('#user_id').text();
  var recipient = $('#recipient').text();
  var recipientID = $('#recipient_id').text();
  
  socket.on('connect', function() {
    socket.emit('user connected', {userName: userName});
    socket.emit('load message thread', {messageID: messageID});
  });
  
  socket.on('thread history retrieved', function(thread) {
    $('#messages').empty();
    thread.forEach((value, index, array) => {
      console.log(value);
      $('#messages').append($('<li>').text(thread[index].sender + ': ' + thread[index].body));
    });
  });
  
  $('form').submit(function(e) {
    e.preventDefault(); // prevents page reloading
    var thread = stripHTML($('#m').val());
    var threadObj = {
      message_id: messageID, 
	  sender: userName, 
	  sender_id: userID, 
	  recipient: recipient,  
	  recipient_id: recipientID, 
	  body: thread
	}
    
    // Don't send the same message to the user that sent it himself. Instead, append the 
    // message directly as soon as he presses enter
    $('#messages').append($('<li>').text(userName + ': ' + thread));
    
    socket.emit('message thread', threadObj);
    $('#m').val('');
    return false;
  });
  socket.on('message thread', function(data) {
    if (data.message_id == messageID) {
      $('#messages').append($('<li>').text(data.sender + ': ' + data.body));
    }
        
    /*
    if (data.messageID == messageID) {
       console.log('This incoming thread is intended for message id: ' + data.messageID);
      $('#messages').append($('<li>').text(data.sender + ': ' + data.body));
    }
    */
  });
  
});