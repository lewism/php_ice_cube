<?php
namespace IceCube;
class MonthlyRule extends Rule
{
  # Determine for a given date/start_date if this rule occurs or not.
  # Month rules occur if we're in a valid interval
  # and either (1) we're on a valid day of the week (ie: first sunday of the month)
  # or we're on a valid day of the month (1, 15, -1)
  # Note: Rollover is not implemented, so the 35th day of the month is invalid.
  public function in_interval($date, $start_date) {
    $date_arr       = getdate($date);
    $start_date_arr = getdate($start_date);
    $months_to_start_date = ($date_arr["mon"] - $start_date_arr["mon"]) + ($date_arr["year"] - $start_date_arr["year"]) + 12;
    
    return $months_to_start_date % $this->interval == 0;
  }
  
  protected function default_jump($date, $attempt_count = 1) {
    return TimeUtil::date_in_n_months($date, $attempt_count * $this->interval);
  }
}
?>