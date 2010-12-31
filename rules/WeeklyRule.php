<?php
namespace IceCube;
class WeeklyRule extends Rule
{
  # Determine whether or not this rule occurs on a given date.
  # Weekly rules occurs if we're in one of the interval weeks,
  # and we're in a valid day of the week.
  public function in_interval($date, $start_date) {
    // make sure we're in the right interval
    $date = strtotime(date("Y-m-d", $date));
    $start_date = strtotime(date("Y-m-d", $start_date));
    
    #Move both to the start of their respective weeks,
    #and find the number of full weeks between them
    $date_arr       = getdate($date);
    $start_date_arr = getdate($start_date);
    
    // TODO:
    //no_weeks = ((date - date.wday) - (start_date - start_date.wday)) / 7
    //return no_weeks % @interval == 0
    
    return true;
  }
  
  protected function default_jump($date, $attempt_count = null) {
    $goal = $date + 7 * IceCube::ONE_DAY * $this->interval;
    return $goal;
  }
}
?>