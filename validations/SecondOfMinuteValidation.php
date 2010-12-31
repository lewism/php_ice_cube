<?php
namespace IceCube;
class SecondOfMinuteValidation extends Validation
{
  private $rule;
  private $seconds_of_minute = array();
  
  public function __construct($rule) {
    $this->rule = $rule;
  }
  
  public function add_seconds_of_minute($seconds) {
    $this->seconds_of_minute = array_merge($this->seconds_of_minute, $seconds);
  }
  
  public function validate($date) {
    if(empty($this->seconds_of_minute)) return true;
    $arr = getdate($date);
    $sec = $arr["seconds"];
    return in_array($sec, $this->seconds_of_minute);
  }
  
  public function closest($date) {
    if(empty($this->seconds_of_minute)) return null;
    $arr = getdate($date);
    $sec = $arr["seconds"];
    # turn seconds into seconds of minute
    # second >= 60 should fall into the next minute
    $seconds = array_map(function($s) use ($sec) {
      return $s > $sec ? $s - $sec : 60 - $sec + $s;
    }, $this->seconds_of_minute);
    $seconds = array_unique($seconds);
    // go to the closest distance away
    $closest_second = min($seconds);
    $goal = $date + $closest_second;
    return $goal;
  }
}
?>