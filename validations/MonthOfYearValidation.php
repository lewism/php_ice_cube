<?php
namespace IceCube;
class MonthOfYearValidation extends Validation
{
  private $rule;
  private $months_of_year;
  
  public function __construct($rule, $months_of_year = array()) {
    $this->rule           = $rule;
    $this->months_of_year = $months_of_year;
  }
  
  public function add_months_of_year($months_of_year) {
    $this->months_of_year = array_unique(array_merge($months_of_year, $this->months_of_year));
  }
  
  public function validate($date) {
    if(empty($this->months_of_year)) return true;
    $arr    = getdate($date);
    $month  = $arr["mon"];
    return in_array($month, $this->months_of_year);
  }
  
  public function closest($date) {
    if(empty($this->months_of_year)) return null;
    // turn months into month of year
    // month > 12 should fall into the next year
    $months = array_map(function($m) use ($date) {
      $arr   = getdate($date);
      $month = $arr["month"];
      return $m > $month ? $m - $month : 12 - $month + $m;
    }, $this->months_of_year);
    $months = array_unique($months);
    $goal   = $date;
    for($i = 0; $i < min($months); $i++) {
      $goal += TimeUtil::days_in_month($goal) * IceCube::ONE_DAY;
    }
    return $goal;
  }
}
?>