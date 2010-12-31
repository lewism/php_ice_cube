<?php
namespace IceCube;
class HourlyRule extends Rule {
  // Determine whether or not this rule occurs on a given date
  public function in_interval($date, $start_date) {
    // Make sure we're in a proper interval
    $day_count = (int) (($date - $start_date) / IceCube::ONE_HOUR);
    return $day_count % $this->interval == 0;
  }
  
  public function to_string() {
    return $this->to_string_base("Hourly", "Every " + $this->interval + " hours");
  }
  
  protected function default_jump($date, $attempt_count = null) {
    // return $date + IceCube::ONE_HOUR + $this->interval;
    return strtotime(date("Y-m-d H:i:s", $date) . "+" . $this->interval . "hour");
  }
}
?>