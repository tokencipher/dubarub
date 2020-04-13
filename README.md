# Dubarub 

![alt text][logo]

[logo]: https://dubarub.com/images/dubarub.jpg

Dubarub is a social media web app for musicians to distribute content, collaborate, grow 
and connect with their fanbase. 

## Description 

v1.0 features include:
• follow/unfollow mechanism
• sending out status updates (twitter-esque) 
• creating posts 
• commenting on posts
• like/upvote mechanism (given/received as 'trophy')
• direct-messaging
• user-uploaded content (audio, video, and images)
• adding to and deleting music from playlist
• feed of posts from all users followed

## Usage

Visit [dubarub](https://www.dubarub.com) to sign up!

## Installation

These are the installation instructions as executed on Ubuntu v16.04.6 LTS

Feel free to use a database and schema of your preference :)

### Requirements  

>Note: App hasn't been tested with versions lower or higher than those listed below

php v7.2.9
php-imagick v3.4.4

npm v6.4.1 
node v10.10.0
jquery v3.2.1
mysql v5.7.29

getID3 1.9.15
ffmpeg 2.8.15

socket.io

Update the package index using `apt`:

```bash 
apt-get update
```

Install the updated packages using `apt`:

```bash 
apt-get install
```

Install php:

```bash
apt-get install php
```

Install imagick:

```bash 
apt-get install php-imagick
```

Install node:

```bash 
apt-get install node
```

npm gets installed with node, check version 

```bash
npm -v
```

Socket.io needs to be installed in project root and has been saved as dependency

In project root run:

```bash 
npm install
```

Install mysql default package: 

```bash
apt-get install mysql-server
```

Run the security script:

```bash 
mysql_secure_installaion
```

Throughout the source code there is a file referenced by the name of 'inc_db_qp4.php'.
This file is where the db connection is made and how the application persists user data to
the database by way of PDO (PHP Data Objects) amongst other implementations (controller/api calls).

You may want to make sure your php.ini file has the PDO module enabled.

The pathname is: /php_inc/inc_db_qp4.php

Open this file with a text editor of your choice and insert this config. Fill in the username
and password of your MySQL user where applicable. 

```php
<?php
$host = "localhost";
$user = "";
$passwd = "";
$DBName = "dubarub";
$dsn = "mysql:host=$host;dbname=$DBName";

$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
	$conn = new PDO($dsn, $user, $passwd, $opt);
} catch (PDOException $e) {
	echo "Connection Failed: " . $e->getMessage();
	$conn = null;
} 
?>
```

FFmpeg is a complete, cross-platform solution to record, convert and stream audio and 
video. It's current use in the application is to convert user-uploaded videos from 
iOS devices (mov) to mp4 in effort to keep browser support. 

Install FFmpeg:

```bash
apt-get install ffmpeg
```

FFmpeg is executed at the command-line every minute as a scheduled cron job in the current
implementation of Dubarub. FFmpeg is expected to be executed programmatically in later 
releases of Dubarub. Pull requests are always appreciated and welcome :) 

Schedule user-uploaded video conversion cron job: 

```bash
crontab -e 
```

Append this line to eof:

```bash
* * * * * cd /path/to/dubarub; ./video_convert.sh
```

### Create Application Back-end 

#### Application DB SCHEMA 

As long as you don't modify PRIMARY and FOREIGN KEY fields, you may add additional fields
without the application breaking (assuming you haven't modified the codebase). 

Run SQL queries located in db_scripts directory. 

### Install Back-end Node.js 'Message Server'

From the project root:

```bash
cd message-server
npm install 
```

This should install all of the message servers' dependencies. 

Run the message server as a foreground process via:

```bash 
node app.js
```

### (Optional) Install [PM2](https://pm2.keymetrics.io/)

[PM2](https://pm2.keymetrics.io/) is an advanced process manager for production Node.js applications. 

Use this tool if you'd like to run the message-server in a production environment. 

## Roadmap 

There are a few things in the codebase that I may've tinkered with, left alone for the sake of working
on something else, and intended on tweaking before making this repo public--nevertheless, I felt
it would be best to not postpone making this repo public any longer. 

This isn't a comprehensive list by any means. You're welcome to make improvements wherever
seen fit.

#### Source Code
* Improve html mark-up for better search engine ranking
* Refactor where applicable
* Ensure codebase enforces scalability 

#### Data
* Add more fields for audio streaming analytics 

#### Settings 
* Reset password setting
* Delete account functionality 

#### Features
* Implement service worker
* Push notifications 
* Improve programmatic audio metadata/tags collection from user-uploaded audio (check out [music-metadata](https://www.npmjs.com/package/music-metadata)
* Allow music from playlist to be played while offline 

#### UI
* Inbox design update
* Message thread design update
* Display cover art for currently playing audio on mobile in landscape mode
* Improve UI for displaying playlist 
* Improve UI for adding tracks to playlist
* Make powerful music search UI for adding tracks to playlist
* Preserve white-space entered in by user when creating posts
* Implement page that loads posts related to tag search query

#### UX
* Make audio player draggable on mobile 
* Polling mechanism for re-loading feed as necessary and notifying user 
* Implement flagging of posts
* Implement flagging of comments

#### Accessibility
* Made app W3C accessibility compliant

 
## Contributing 

I am open to contributions and am eager to see what kind of awesome improvements you may 
bring about. 

## Support

* Contact me at [bryan.thomas.nyc@gmail.com](mailto:bryan.thomas.nyc@gmail.com) 
* Send me a message on the platform at https://www.dubarub.com/sinclair

## Author

* Bryan Thomas

## License 

This project is licensed under the [GPLv3](https://choosealicense.com/licenses/gpl-3.0/) License.




 



