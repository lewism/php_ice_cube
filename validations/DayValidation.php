<?php
namespace IceCube;
class DayValidation extends Validation
{
  private $rule;
  private $days = array();
  
  function __construct($rule) {
    $this->rule = $rule;
  }

  public function add_days($days) {
    $this->days = array_unique(array_merge($days, $this->days));
  }
  
  public function validate($date) {
    if(empty($this->days)) return true;
    $arr  = getdate($date);
    return in_array($arr['wday'], $this->days);
  }
  
  public function closest($date) {
    if(empty($this->days)) return null;
    // turn days into distances
    $arr  = getdate($date);
    $wday = $arr['wday'];
    $days = array_map(function($day) use ($wday) {
      return $day > $wday ? ($day - $wday) : (7 - $wday + $day);
    }, $this->days);
    $days = array_unique($days);
    // go to the closest distance away, the start of that day
    $goal = $date + (min($days) * IceCube::ONE_DAY);
    //return $this->adjust($goal, $date);
    return $goal;
  }
}
?>