const webpush = require('web-push');
const bodyParser = require('body-parser');
const axios = require('axios');
const qs = require('qs');
const util = require('util');
const timestamp = require('time-stamp');

var uniqid = require('uniqid');
//console.log('Genereated unique id: ' + uniqid())
console.log('message-server is listening on port 4200...');

var fs = require('fs');
var options = {
  key: fs.readFileSync('/etc/letsencrypt/live/dubarub.com/privkey.pem'),
  cert: fs.readFileSync('/etc/letsencrypt/live/dubarub.com/cert.pem'),
  requestCert: false
};
var express = require('express');
var app = express();
var server = require('https').createServer(options, app);
var io = require('socket.io')(server);
var cors = require('cors');
var mysql = require('mysql');
var users = [];
var onlineUsers = [];
var display = '';
var userSocketIDs = new Map();

/*
var con = mysql.createConnection({
  host: "localhost",
  user: "root",
  password: process.env.ROOT_MYSQL_PASS,
  database: "dubarub" 
});

con.connect(function(err) {
  if (err) throw err; 
  console.log("Connected!");
  var sql = "SELECT user_name FROM user";
  con.query(sql, function(err, result) {
    result.forEach((value, index, array) => {
      users.push(value.user_name);
      display += '<li><a href="#">' + value.user_name + '</a></li>';
      console.log("Username: " + value.user_name);
    });
  });
});
*/

app.use(cors());
app.use(bodyParser.json());
app.use(express.static(__dirname + '/node_modules'));

app.post('/get_all_users', function(req, res) {
  res.json(users);
});

app.get('/', function(req, res, next) {
  res.sendFile(__dirname + '/index.html');
});

app.get('/message_form/:sender', function(req, res) {
  res.send(
    '<!DOCTYPE html>' +
    '<html lang="en">' +
    '<head>' +
    '<meta charset="utf-8"/>' +
    '<meta http-equiv="X-UA-Compatible" content="IE=edge"' +
    '<meta name="viewport" content="width=device-width, initial-scale=1"/>' +
    '<meta http-equiv="content-type" content="text/html; charset="utf-8"/>'  +
    '<link rel="stylesheet" href="css/index.css" />' +
    '<link rel="stylesheet" href="lib/bootstrap-3.3.7-dist/css/bootstrap.min.css"/>' +
    '<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"/>' +
    '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"/>' +
    '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">' +
    '<style>' +
    '#message_to { margin-bottom: 12px; }' +
    '#results { list-style-type: none; padding: 0; margin: 0;}' +
    '#results li a { border:1px solid #ddd;margin-top:-1px;background-color:#f6f6f6;padding:12px;text-decoration:none;font-size:18px;color:black;display:block}' +
    '#results li a:hover:not(.header) { background-color: #eee; }' + 
    '</style>' +
    '<script> function userSearchFunction() { var input, filter, ul, li, a, i, txtValue;' +
    'input = document.getElementById("message_to");filter = input.value.toLowerCase();' +
    'ul = document.getElementById("results");li = ul.getElementsByTagName("li");' +
    'for (i = 0; i < li.length; i++) { a = li[i].getElementsByTagName("a")[0];' +
    'txtValue = a.textContent || a.innerText; if (txtValue.toLowerCase().indexOf(filter) > -1) {' +
    'li[i].style.display = "";} else {li[i].style.display = "none"; }}}</script>' +
    '<script src="https://code.jquery.com/jquery-3.2.1.min.js"' +
    'integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="' +
    'crossorigin="anonymous">' +
    '</script>' +
    '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>' +
    '</head>' + 
    '<body>' +
     '<div id="sender" style="display:none">' + req.params.sender + '</div>' +
     '<div class="w3-center"><br>' +
        '<form class="w3-container" method="post">' +
          '<div class="form-group">' +
            '<div><i class="w3-xxlarge fa fa-paper-plane" style="color:#339966;"></i></div>' +
            '<!--<label for="post_title">Enter caption of new post</label>-->' +
            'To: <input type="text" onkeyup="userSearchFunction()" id="message_to" name="message_to" maxlength="" class="form-control" aria-describedby="subject_help" placeholder="Enter name of recipient..." />' +
	        '<ul id="results"></ul>' +
            '<!--<small id="subject_help" class="form-text text-muted">Subject cannot be any longer than 140 characters.</small>-->' +
          '</div>' +
          '<div class="form-group">' +
            '<!-- <label for="bio_edit">Edit bio</label> -->' +
            'Body: <textarea id="message_body" name="message_body" class="form-control" placeholder="" rows="3" maxlength="" required></textarea>' +
            '<br>' +
          '</div>' +
        '</form>' +
        
        '<div id="message_submit_container">' +
          '<button  id="message_submit" class="btn btn-primary w3-margin-bottom">Send</button>' +
        '</div>' +
      '</div>' +
      '<div class="alert alert-success" style="position:relative;top:-58px;z-index:-1;display:none" role="alert"><strong>Message delivered</strong></div>' +
      '<div class="alert alert-warning" style="position:relative;top:-58px;z-index:-1;display:none" role="alert"><strong>User not found</strong></div>' +
      '<script src="/socket.io/socket.io.js"></script>' +
      '<script src="https://dubarub.com/message-server/js/send_message.js"></script></body></html>'
  );
});

app.get('/message_thread/:message_id/:user_name/:user_id/:recipient/:recipient_id', function(req, res) {
  res.send(
    '<!DOCTYPE html>' +
    '<html lang="en">' +
    '<head>' +
    '<meta charset="utf-8"/>' +
    '<meta http-equiv="X-UA-Compatible" content="IE=edge"' +
    '<meta name="viewport" content="width=device-width, initial-scale=1"/>' +
    '<meta http-equiv="content-type" content="text/html; charset="utf-8"/>'  +
    '<link rel="stylesheet" href="css/index.css" />' +
    '<link rel="stylesheet" href="lib/bootstrap-3.3.7-dist/css/bootstrap.min.css"/>' +
    '<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"/>' +
    '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css"/>' +
    '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">' +
    '<style>' +
    '* { margin: 0; padding: 0; box-sizing: border-box; }' +
    'body { font: 13px Helvetica, Arial; }' +
    'form { background: #000; padding: 3px; position: fixed; bottom: 0; width: 100%; }' +
    'form input { border: 0; padding: 10px; width: 90%; margin-right: .5%; }' +
    'form button { width: 9%; background: rgb(130, 224, 255); border: none; padding: 10px; }' +
    '#messages { list-style-type: none; margin: 0; padding: 0; }' +
    '#messages li { padding: 5px 10px; }' +
    '#messages li:nth-child(odd) { background: #eee; }' +
    '</style>' +
    '<script src="https://code.jquery.com/jquery-3.2.1.min.js"' +
    'integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="' +
    'crossorigin="anonymous">' +
    '</script>' +
    '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>' +
    '</head>' + 
    '<body>' +
    '<div id="message_id" style="display:none">' + req.params.message_id + '</div>' +
    '<div id="user_name" style="display:none">' + req.params.user_name + '</div>' +
    '<div id="user_id" style="display:none">' + req.params.user_id + '</div>' + 
    '<div id="recipient" style="display:none">' + req.params.recipient + '</div>' +
    '<div id="recipient_id" style="display:none">' + req.params.recipient_id + '</div>' +
	'<ul id="messages"></ul>' +
	'<form action="">' +
	'<input id="m" autocomplete="off" /><button>Send</button>' +
	'</form>' + 
	'<div class="alert alert-success" style="position:relative;top:-58px;z-index:-1;display:none" role="alert"><strong>Message delivered</strong></div>' +
	'<div class="alert alert-warning" style="position:relative;top:-58px;z-index:-1;display:none" role="alert"><strong>User not found</strong></div>' +
	'<script src="/socket.io/socket.io.js"></script>' +
	'<script src="https://dubarub.com/message-server/js/send_message_thread.js"></script></body></html>'
  );
});

io.on('connection', function(socket) {
  
  socket.on('user connected', function(data) {
    
    // Add connected user socket id to map of online users
    userSocketIDs.set(data.userName, socket.id);
    
    // Add connected user to online user array 
    onlineUsers.push(data.userName);
    
    console.log(data.userName + ' has connected...');
    
    socket.broadcast.emit('user connected', {whoSignedOn: data.userName});
    
  });
  
  socket.on('disconnect', function() {
    
    // Retrieve user that is to be removed from list of online users
    for (let [key, value] of userSocketIDs.entries()) {
      if (value == socket.id ) {
        var userName = key; 
      }
    }
    
    console.log(userName + 'has disconnected...');
    
    var toBeRemoved = userName;
    var position = onlineUsers.indexOf(toBeRemoved);
    
    if (position !== -1) {
      var removed = onlineUsers.indexOf(toBeRemoved);
    }
    
    if (removed == toBeRemoved) {
      console.log(removed + ' has been removed from list of online user array');
    }
    
    userSocketIDs.delete(userName);
    
    socket.broadcast.emit('user disconnected', {whoSignedOff: userName});
    
  });
  
  socket.on('user typing', function(data) {
    console.log(data.userName + ' is typing...');
    socket.broadcast.emit('user typing', data);
  });  
  
  socket.on('save socket details', function(data) {
    console.log('Save socket details event emitted from socket id: ' + socket.id);
    onlineUsers.push({
      clientID: socket.id, 
      userName: data.userName
    });
  });
  
  socket.on('load message thread', function(data) {
    console.log('attempting to load message thread with message id: ' + data.messageID);
    var messageObj = {message_id: data.messageID}
    axios({
      method: 'post', 
      url: 'https://dubarub.com/load_message_thread.php', 
      data: messageObj
    })
    .then(response => {
      socket.emit('thread history retrieved',  response.data);
      console.log("Message thread history retrieved for user: " + response.data[0].recipient);
    })
    .catch(err => {
      console.log("There has been an error retrieving message thread history: " + err);
    });
  });
  
  socket.on('message thread', function(data) {
    console.log('Message thread event emitted');
    
    axios({
      method: 'post', 
      url: 'https://dubarub.com/create_message_thread.php',
      data: data
    })
    .then(response => {
      console.log('Message thread persisted to db');
    })
    .catch(err => {
      console.log(err);
    });
    
    console.log("Number of users online: " + onlineUsers.length);
    /*
    var recipient = onlineUsers.filter((x) => x.userName == data.recipient);
    var recipientClientID = recipient[0].clientID;
    */
    
    var recipientSocketID = userSocketIDs.get(data.recipient);
    
    data.created_at = timestamp('YYYY-MM-DD HH:mm:ss');
    
    socket.broadcast.emit('message thread', data);
    //socket.broadcast.to(recipientSocketID).emit('message thread', data);
    
  });

  socket.on('message', function(data) {
    console.log("Message event emitted by client: " + data.sender);
    // TODO: Create message_id 
    var messageID = uniqid();
    var created_at = timestamp('YYYY-MM-DD HH:mm:ss');
    var params = {
      message_id: messageID,
      sender: data.sender,
      recipient: data.recipient, 
      body: data.message 
    };

    axios({
      method: 'post', 
      url: 'https://dubarub.com/message_inflater.php',
      data: params
    }).then( response => {  
      //console.log("Message ID: " + response.data.messageID);
      var messageID = response.data.messageID;
      //console.log("Sender ID: " + response.data.senderID);
      var senderID = response.data.senderID;
      //console.log("Recipient ID: " + response.data.recipientID);
      var recipientID = response.data.recipientID;
      //console.log("Avatar: " + response.data.avatar);
      var avatar = response.data.avatar;
      //console.log("Sender: " + response.data.sender);
      var sender = response.data.sender;
      //console.log("Sender mailbox: " + response.data.senderMailbox);
      var senderMailbox = response.data.senderMailbox;
      //console.log("Recipient: " + response.data.recipient);
      var recipient = response.data.recipient;
      //console.log("Recipient mailbox: " + response.data.recipientMailbox);
      var recipientMailbox = response.data.recipientMailbox;
      //console.log("Message: " + response.data.message);
      var message = response.data.message;
      
      params.avatar = avatar;
      params.sender_id = senderID;
      params.created_at = created_at;
      socket.broadcast.emit('message', params);

      /*
      var userObj = { user_id: recipientID };
      axios({
        url: 'https://dubarub.com/retrieve_subscription.php',
        method: 'post', 
        data: userObj
      }).then(response => {
        console.log("Retrieved subscriptions from db for recipient in preparation of push notification");
        //console.log(response.data.subscriptions);
        
        response.data.subscriptions.forEach((value, index, array) => {
          console.log("Endpoint retrieved for push service: " + value.endpoint);
          console.log("p256dh key retrieved for push service: " + value.ptwofivesixdh_key);
          console.log("auth key retrieved for push service: " + value.auth);
          
          
          const options = {
			vapidDetails: {
			  subject: 'mailto:generalpublicllc@gmail.com',
			  publicKey: process.env.PUBLIC_VAPID_KEY, 
			  privateKey: process.env.PRIVATE_VAPID_KEY
			},
			// 1 hour in seconds
			TTL: 60 * 60
		  };

		  const pushSubscription = {
			endpoint: value.endpoint,
			keys: {
			  p256dh: value.ptwofivesixdh_key,
			  auth: value.auth
			}
		  };

		  const payload = JSON.stringify({
			body: message,
			username: sender
		  });

		  webpush.sendNotification(
			pushSubscription,
			payload,
			options
		  ).then(() => {
	        console.log('Push notification sent!');
		  }).catch((err) => {
		  
		    if (err.statusCode === 404 || err.statusCode == 410) {
		      console.log('Subscription has expired or is no longer valid: ' + err);
		      
		      axios({
		        url: 'https://dubarub.com/delete_subscription.php/' + userObj.user_id,
                method: 'post', 
                data: {endpoint: value.endpoint}
		      }).then(response => {
		        console.log('invalid or expired subscription has been deleted');
		      }).catch(err => {
		        console.log(err);
		      });
		      	      
		    } else {
		      console.log(err);
		    }
		    
		  });
		  
		  
		});
		
      }).catch(err => {
        console.log(err);
      });
      */
    }).catch(err => {
      console.log(err);
    });    
    
  });

});

server.listen(4200);
