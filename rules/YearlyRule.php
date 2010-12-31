<?php
namespace IceCube;
class YearlyRule extends Rule
{
  // Determine whether or not the rule, given a start date,
  // occurs on a given date.
  // Yearly occurs if we're in a proper interval
  // and either (1) we're on a day of the year, or (2) we're on a month of the year as specified
  // Note: rollover dates don't work, so you can't ask for the 400th day of a year
  // and expect to roll into the next year (this might be a possible direction in the future)
  public function in_interval($date, $start_date)
  {
    // make sure we're in the proper interval
    $arr = getdate($date);
    $arr2 = getdate($start_date);
    return ($arr["year"] - $arr2["year"]) % $this->interval == 0;
  }
  
  public function to_string() {
    return $this->to_string_base("Yearly", "Every " . $this->interval . " years");
  }
  
  // one year from now, the same month and day of the year
  protected function default_jump($date, $attempt_count = 1) {
    return strtotime(date("Y-m-d", $date) . "+1 year");
  }
}
?>