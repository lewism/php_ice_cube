<?php
namespace IceCube;
class Rule {

  protected $until_date;
  protected $ocurrence_count;

  protected $validation_collection;

  protected $interval;

  // Set the interval for the rule.  Depending on the type of rule,
  // interval means every (n) weeks, months, etc. starting on the start_date's
  public function __construct($interval = 1) {
    if($interval <= 0) {
      throw new InvalidArgumentException("Interval must be > 0");
    }
    $this->validation_collection  = new ValidationCollection($this);
    $this->interval               = $interval;
  }

  // create a new daily rule
  public static function daily($interval = 1) {
    return new DailyRule($interval);
  }
  
  // create a new weekly rule
  public static function weekly($interval = 1) {
    return new WeeklyRule($interval);
  }
  
  // create a new monthly rule
  public static function monthly($interval = 1) {
    return new MonthlyRule($interval);
  }
  
  // create a new yearly rule
  public static function yearly($interval = 1) {
    return new YearlyRule($interval);
  }
  
  // create a new hourly rule
  public static function hourly($interval = 1) {
    return new HourlyRule($interval);
  }
  
  // create a new minutely rule
  public static function minutely($interval = 1) {
    return new MinutelyRule($interval);
  }
  
  // create a new secondly rule
  public static function secondly($interval = 1) {
    return new SecondlyRule($interval);
  }
  
  // Set the time when this rule will no longer be effective
  public function until($until_date) {
    if($this->occurrence_count) {
      // FIXME: original code refers to @count rather than @occurrence_count so it may be
      // checking something different - but could just be a bug in ice_cube?
      throw new InvalidArgumentException("Cannot specify until and count on the same rule");
    }
    $this->until_date = $until_date;
    return $this;
  }
  
  // Set the number of occurrences after which this rule is no longer effective
  public function count($count) {
    if($count < 0) {
      throw new InvalidArgumentException("Argument must be a positive integer");
    }
    $this->occurrence_count = $count;
    return $this;
  }
  
  public function get_occurrence_count() {
    return $this->occurrence_count;
  }
  
  public function get_until_date() {
    return $this->until_date;
  }
  
  public function validate_single_date($date) {
    return $this->validation_collection->validate_single_date($date);
  }
  
  // The key to speed - extremely educated guesses
  // This spidering behavior will go through look for the next suggestion
  // by constantly moving the farthest back value forward
  public function next_suggestion($date) {
    // get the next date recommendation set
    $suggestions = $this->validation_collection->get_suggestions($date);
    
    $compact_suggestions = array_unique(array_values($suggestions));
    
    // find the next date to go to
    if(count($compact_suggestions) == 0) {
      $attempt_count = 0;
      while(true) {
        // keep going through rule suggestions
        $next_date = $this->default_jump($date, $attempt_count == 1);
        if($next_date && $this->validate_single_date($next_date)) {
          return $next_date;
        }
      }
    } else {
      while(true) {
        $compact_suggestions = array_unique(array_values($suggestions));
        $min_suggestion = min($compact_suggestions);
        // validate all against he minimum
        if($this->validate_single_date($min_suggestion)) {
          return $min_suggestion;
        }
        $suggestions = $this->validation_collection->get_suggestions($min_suggestion);
      }
    }
    
  }
  
  private function to_string_base($singular, $plural) {
    $representation = "";
    $representation = ($this->interval == 1 ? $singular : $plural);
    $representation += implode("", array_map(function() {
      return " " + $this->to_string();
    }, $validation_types));
    $representation += " " + $this->occurrence_count + " ";
    $representation += ($this->occurrence_count && $this->occurrence_count == 1 ? "time" : "times");
  }
  
  // 
  // Delegate to validation collection
  
  public function day() {
    $args = func_get_args();
    call_user_func_array(array($this->validation_collection, "day"), $args);
    return $this;
  }
  
  public function month_of_year() {
    $args = func_get_args();
    call_user_func_array(array($this->validation_collection, "month_of_year"), $args);
    return $this;
  }
  
  public function second_of_minute() {
    $args = func_get_args();
    call_user_func_array(array($this->validation_collection, "second_of_minute"), $args);
    return $this;
  }
  
}
?>