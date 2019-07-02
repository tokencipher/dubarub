<?php

class Track {

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
  private $file_size;
  private $bpm; 
  private $cover_art;
  private $mp3_path;
  private $ogg_path;
  
  public function __construct() {
    include ("php_inc/inc_db_qp4.php");
    if ($conn !== false) {
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
  
  public function getUserId() {
    return $this->user_id;
  }
  
  public function getTrackInfo($track_id) {
  	$table = "track";
    $sql = "SELECT u_id, title, artist, genre, album, duration, mp3_path, ogg_path, art, bpm FROM $table WHERE track_id = $track_id ORDER BY artist ASC ";
    $object = array();
    $x = 0;
    foreach ($this->db->query($sql) as $row) {
      $object['u_id'] = "{$row['u_id']}";
      $object['title'] = "{$row['title']}";
      $object['artist'] = "{$row['artist']}";
      $object['genre'] = "{$row['genre']}";
      $object['album'] = "{$row['album']}";
      $object['duration'] = "{$row['duration']}";
      $object['mp3_path'] = "{$row['mp3_path']}";
      $object['ogg_path'] = "{$row['ogg_path']}";
      $object['art'] = "{$row['art']}";
      $object['bpm'] = "{$row['bpm']}";
      //++$x; 
    }
    return $object;
  }
  
  public function createTrack() {
    $table = "track";
    $sql = "INSERT INTO $table(u_id, artist, album, title, year, genre, duration, format, file_size, mp3_path, art) VALUES(:user_id, :artist, :album, :title, :year, :genre, :duration, :format, :file_size, :mp3_path, :cover_art)";
    
    $stmt = $this->db->prepare($sql);
    
    $stmt->bindParam(':user_id', $this->user_id);
    $stmt->bindParam(':artist', $this->artist);
    $stmt->bindParam(':album', $this->album);
    $stmt->bindParam(':title', $this->title);
    $stmt->bindParam(':year', $this->year);
    $stmt->bindParam(':genre', $this->genre);
    $stmt->bindParam(':duration', $this->duration);
    $stmt->bindParam(':format', $this->format);
    $stmt->bindParam(':file_size', $this->file_size);
    $stmt->bindParam(':mp3_path', $this->mp3_path);
    //$stmt->bindParam(':bpm', $this->bpm);
    $stmt->bindParam(':cover_art', $this->cover_art);
    
    return $stmt->execute();
  }
  
  public function getTrackIdByUId() {
    $table = "track";
    $u_id = $this->user_id;
    $sql = "SELECT track_id FROM $table WHERE u_id=$u_id;";
    foreach ($this->db->query($sql) as $row) {
      $track_id = "{$row['track_id']}";
    }
    return $track_id;
  }
  
  public function getTrackIdByTitle($title) {
    $table = "track";
    $u_id = $this->user_id;
    $sql = "SELECT track_id FROM $table WHERE title=$title;";
    foreach ($this->db->query($sql) as $row) {
      $track_id = "{$row['track_id']}";
    }
    return $track_id;
  }
  
  public function getTrackIdByAlbum($album) {
    $table = "track";
    $u_id = $this->user_id;
    $sql = "SELECT track_id FROM $table WHERE album=$album;";
    foreach ($this->db->query($sql) as $row) {
      $track_id = "{$row['p_id']}";
    }
    return $track_id;
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
  
  public function getBPM() {
    return $this->bpm;
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
  
  public function setUserId($user_id) {
    $this->user_id = $user_id;
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
  
  public function setFileSize($size) {
    $this->file_size = $size;
  }
  
  public function setBPM($bpm) {
    $this->bpm = $bpm;
  }
  
  public function setCoverArt($cover_art) {
    $this->cover_art = $cover_art;
  }
  
  public function setMp3Path($mp3_path) {
    $this->mp3_path = $mp3_path;
  }
  
  public function setOggPath($ogg_path) {
    $this->ogg_path = $ogg_path;
  }

}

?>