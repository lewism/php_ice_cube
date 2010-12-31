<?php
namespace IceCube;
class DailyRule extends Rule
{
  # TODO repair
  # Determine whether this rule occurs on a give date.
  public function in_interval($date, $start_date) {
    //$day_count = (int) (($date - $start_date) / ICIceCube::ONE_DAY);
    //return $day_count % $this->interval == 0
    return true;
  }
  
  public function to_string() {
    return $this->to_string_base("Daily", "Every " . $this->interval . " days");
  }
  
  protected function default_jump($date, $attempt_count = null) {
    $goal = $date + (IceCube::ONE_DAY * $this->interval);
    return $goal;
  }
}
?>