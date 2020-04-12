function stripHTML(str) {
  var StrippedString = str.replace(/(<([^>]+)>)/ig,"");
  return StrippedString;
}

$(document).ready(function() {
  const socket = io();

  $('#message_submit').click(function(e){
    e.preventDefault();
    var sender, recipient, message;
    console.log('send message button clicked...');
    sender = $('#sender').text();
    recipient = $('#message_to').val();
    message = stripHTML($('#message_body').val());
    console.log('Recipient: ' + recipient);
    console.log('Sender: ' + sender);    

    $.ajax({
      url: "https://dubarub.com:4200/get_all_users", 
      type: "POST",
      dataType: "json"
    }).done(function(data) {
      console.log(data);
      var result = data.indexOf(recipient.toLowerCase());
      if (result == -1) {
        console.log('User not found!');
        $('.alert-warning').css('display', 'block');
        setTimeout(function() {
          $('.alert-warning').css('display', 'none');
        }, 3000);
      } else {
        // TODO: Gather contents of message for delivery
        var messageObj = {
          sender: sender,
          recipient: recipient, 
          message: message
        };

        socket.emit('message', messageObj);
 
        $('.alert-success').css('display', 'block');
        setTimeout(function() {
          $('.alert-success').css('display', 'none');
        }, 3000);

      }
    }).fail(function(xhr, status, errorThrown) {
      console.log("Error: " + errorThrown);
      console.log("Status: " + status);
    }) 
  }); 
});
