<?php


class TimeElapsed {
  private $timestamps = array();
  
  function TimeElapsed($oldTime) {
    $this->oldTime = $oldTime;
  }
  
  public function setTime($time) {
    $this->oldTime = $time;
  }
  
  public function addTime($time) {
    array_push($this->timestamps, $time);
    print_r($this->timestamps);
  }
  
  public function time_elapsed_A($secs) {
    $bit = array(
      'y' => $secs / 31556926 % 12,
      'w' => $secs / 604800 % 52,
      'd' => $secs / 86400 % 7,
      'h' => $secs / 3600 % 24,
      'm' => $secs / 60 % 60,
      's' => $secs % 60
    );
    
    foreach ($bit as $k => $v) {
      if ($v > 0) {
        $ret = $v . $k;
      }
    }
    return join(' ', $ret);
  }
  
  public function time_elapsed_B($secs) {
    $bit = array(
      ' year' => $secs / 31556926 % 12,
      ' week' => $secs / 604800 % 52,
      ' day' => $secs / 86400 % 7, 
      ' hour' => $secs / 3600 % 24,
      ' minute' => $secs / 60 % 60,
      ' second' => $secs % 60
     );
    
    foreach ($bit as $k => $v) {
      if ($v > 1) $ret[] = $v . $k . 's';
      if ($v == 1) $ret[] = $v . $k;
    }
    array_splice($ret, count($ret) - 1, 0, 'and');
    $ret[] = 'ago.';
    
    return join(' ', $ret);
  }
  
  public function get_time_elapsed_A($nowTime) {
    time_elapsed_A($nowTime - $this->oldTime);
  }
  
  public function get_time_elapsed_B($nowTime) {
    time_elapsed_B($nowTime - $this->oldTime);
  }
  
}

?>