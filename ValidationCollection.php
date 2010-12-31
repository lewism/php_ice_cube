<?php
namespace IceCube;
class ValidationCollection {
  private $rule               = null;
  private $validation_types   = array();
  
  public function __construct($rule) {
    $this->rule = $rule;
  }
  
  public function validate_single_date($date) {
    $all = true;
    foreach($this->validation_types as $validation_type) {
      $response = $validation_type->validate($date);
      if(!$response) {
        $all = false;
        break;
      }
    }
    return $all;
  }
  
  public function get_suggestions($date) {
    $suggestions = array();
    foreach($this->validation_types as $k => $validation) {
      $suggestions[$k] = $validation->closest($date);
    }
    return $suggestions;
  }
  
  public function second_of_minute() {
    if(!$this->validation_types["second_of_minute"]) $this->validation_types["second_of_minute"] = new SecondOfMinuteValidation($this->rule);
    $seconds  = array();
    $args     = func_get_args();
    foreach($args as $second) {
      if(!($second < 60 && $second >= 0)) throw new InvalidArgumentException("Argument must be a valid second");
      $seconds[] = $second;
    }
    $seconds = array_unique($seconds);
    $this->validation_types["second_of_minute"]->add_seconds_of_minute($seconds);
  }
  
  public function day() {
    if(!$this->validation_types['day']) $this->validation_types['day'] = new DayValidation($this->rule, $this->validations['day']);
  
    $days = array();
    $args = func_get_args();
  
    foreach($args as $day) {
      if(is_string($day)) {
        // TODO: raise argument error unless valid day string
        // raise ArgumentError.new('Argument must be a valid day of the week') unless IceCube::DAYS.has_key?(day)
        $days[] = IceCube::$DAYS[$day];
      } else {
        // TODO: raise argument error unless valid day integer
        // raise ArgumentError.new('Argument must be a valid day of week (0-6)') unless day >= 0 && day <= 6
        $days[] = $day;
      }
    }
    
    $this->validation_types['day']->add_days($days);
  }
  
  public function day_of_year() {
    
  }
  
  // Specify what months of the year this rule applies to.  
  // ie: Schedule.yearly(2).month_of_year(:january, :march) would create a
  // rule which occurs every january and march, every other year
  // Note: you cannot combine day_of_year and month_of_year in the same rule.
  public function month_of_year() {
    if(!$this->validation_types["month_of_year"])
      $this->validation_types["month_of_year"] = new MonthOfYearValidation($this->rule);
    $months = array();
    $args   = func_get_args();
    foreach($args as $month) {
      if(is_string($month)) {
        // TODO:
        // raise ArgumentError.new('Argument must be a valid month') unless IceCube::MONTHS.has_key?(month)
          $months[] = IceCube::$MONTHS[$month];
      } else {
          // TODO:
          // raise ArgumentError.new('Argument must be a valid month (1-12)') unless month >= 1 && month <= 12
          $months[] = $month;
      }
    }
    $this->validation_types["month_of_year"]->add_months_of_year($months);
  }
  
  public function day_of_week() {
    
  }
  
  public function hour_of_day() {
    
  }
  
  public function day_of_month() {
    
  }
  
  public function minute_of_hour() {
    
  }
}
?>