<?php

class Playlist {
  
  private $db;
  private $user_id;
  private $track_id;
  private $title;
  private $artist;
  private $album;
  private $year;
  private $genre;
  private $duration;
  private $format;
  private $bpm;
  private $cover_art;
  private $file_size;
  private $mp3_path;
  private $ogg_path;

  public function __construct() {
    include ("php_inc/inc_db_qp4.php");
    if ($conn !== FALSE) {
	  $this->db = $conn;
    }
  }

  public function __destruct() {
    $this->db = null;
  }

  // specify which data members of the class to serialize
  public function __sleep() {}

  // initialize any data members that were not saved with the serialization 
  public function __wakeup() {}

  public function dbClose() {
    $this->db = null;
  }
  
  public function getPlaylist() {
	$user_id = $this->user_id;
    $table = "playlist";
    $sql = "SELECT track_id, title, artist, album, year, genre, duration, mp3_path, ogg_path, art, bpm FROM $table WHERE u_id = $user_id";
    $object = array();
    $x = 0;
    $input = "";
    $input .= '<ol style="list-style-position:inside;margin:0;padding:0;">';
    
    foreach ($this->db->query($sql) as $row) {
      $object[$x]['track_id'] = "{$row['track_id']}";
      $object[$x]['title'] = "{$row['title']}";
      $object[$x]['artist'] = "{$row['artist']}";
      $object[$x]['album'] = "{$row['album']}";
      $object[$x]['year'] = "{$row['year']}";
      $object[$x]['genre'] = "{$row['genre']}";
      $object[$x]['duration'] = "{$row['duration']}";
      $object[$x]['mp3_path'] = "{$row['mp3_path']}";
      $object[$x]['ogg_path'] = "{$row['ogg_path']}";
      $object[$x]['art'] = "{$row['art']}";
      $object[$x]['bpm'] = "{$row['bpm']}";
      $object[$x]['art'] = empty($object[$x]['art']) ?  "img/cover_art/vinyl_stub.jpg" : $object[$x]['art'] ;
	  $input .= '<li class="track" id="' . $object[$x]['track_id'] . '" data-title="' . 
	  $object[$x]['title'] . '" data-artist="' . $object[$x]['artist'] . '" data-genre="' .
	  $object[$x]['genre'] . '" data-album="' . $object[$x]['album'] . '" data-mp3-path="' . 
	  $object[$x]['mp3_path'] . '" data-ogg-path="' . $object[$x]['ogg_path'] . '" data-cover-art="' .
	  $object[$x]['art'] . '">' . $object[$x]['artist'] . " - " . $object[$x]['title'] . '</li>';
      ++$x; 
    }
    
    if (isset($object)) {
       $input .= '</ol>';
    } else {
       $input = '';
    }
    
    return $input;
  }
  
  public function addTrack($track_id, $user_id, $title, $artist, $genre, $album, $duration, $mp3_path, $ogg_path, $cover_art, $bpm) {
    $table = "playlist";
    $sql = "INSERT INTO $table(track_id, u_id, title, artist, genre, album, duration, mp3_path, ogg_path, art, bpm) VALUES(:track_id, :u_id, :title, :artist, :genre, :album, :duration, :mp3_path, :ogg_path, :cover_art, :bpm)";
    
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':track_id', $track_id);
    $stmt->bindParam(':u_id', $user_id);
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':artist', $artist);
    $stmt->bindParam(':genre', $genre);
    $stmt->bindParam(':album', $album);
    $stmt->bindParam(':duration', $duration);
    $stmt->bindParam(':mp3_path', $mp3_path);
    $stmt->bindParam(':ogg_path', $ogg_path);
    $stmt->bindParam(':cover_art', $cover_art);
    $stmt->bindParam(':bpm', $bpm);
    
    return $stmt->execute();
  }
  
  public function deleteTrack($track_id) {
    $table = "playlist";
    $sql = "DELETE FROM $table WHERE track_id = :track_id";
    
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':track_id', $track_id);
    
    return $stmt->execute();
  } 

  public function getUserId() {
    return $this->user_id;
  }
  
  public function getTrackId() {
    return $this->track_id;
  }
  
  public function getTitle() {
    return $this->title;
  }
  
  public function getArtist() {
    return $this->artist;
  }
  
  public function getAlbum() {
    return $this->album;
  }
  
  public function getYear() {
    return $this->year;
  }
  
  public function getGenre() {
    return $this->genre;
  }
  
  public function getDuration() {
    return $this->duration;
  }
  
  public function getFormat() {
    return $this->format;
  }
  
  public function getFileSize() {
    return $this->file_size;
  }
  
  public function getMp3Path() {
    return $this->mp3_path;
  }
  
  public function getOggPath() {
    return $this->ogg_path;
  }
  
  public function setUId($u_id) {
    $this->user_id = $u_id;
  }
  
  public function setTrackId($track_id) {
    $this->track_id = $track_id;
  }
  
  public function setTitle($title) {
    $this->title = $title;
  }
  
  public function setArtist($artist) {
    $this->artist = $artist;
  }
  
  public function setAlbum($album) {
    $this->album = $album;
  }
  
  public function setYear($year) {
    $this->year = $year;
  }
  
  public function setGenre($genre) {
    $this->genre = $genre;
  }
  
  public function setDuration($duration) {
    $this->duration = $duration;
  }
  
  public function setFormat($format) {
    $this->format = $format;
  }
  
  public function setMp3Path($mp3_path) {
    $this->mp3_path = $mp3_path;
  }
  
  public function setOggPath($ogg_path) {
    $this->ogg_path = $ogg_path;
  }
  
  
}


?>