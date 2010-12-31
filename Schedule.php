<?php
namespace IceCube;
class Schedule
{
  private $rrule_occurrence_heads;
  private $exrule_occurrence_heads;
  private $exdates;
  private $rdates;
  private $start_date;
  private $duration;
  private $end_time;
  
  public function __construct($start_date, $options = array()) {
    $this->rrule_occurrence_heads   = array();
    $this->exrule_occurrence_heads  = array();
    $this->rdates                   = array();
    $this->exdates                  = array();
    $this->start_date               = $start_date;
    // TODO: raise if duration is negative
    $this->duration = $options['duration'];
    // TODO: raise if start time is >= end time
    $this->end_time = $options['end_time'];
  }
  
  # Return all possible occurrences 
  # In order to make this call, all rules in the schedule must have
  # either an until date or an occurrence count

  public function all_occurrences() {
    return $this->find_occurrences(function($head) {
      return $head->all_occurrences();
    });
  }
  
  // Find all occurrences until a certain date
  public function occurrences($end_date) {
    if($this->end_time && $this->end_time < $end_date) {
      $end_date = $this->end_time;
    }
    return $this->find_occurrences(function($head) use ($end_date) {            
      return $head->upto($end_date);
    });
  }
  
  // Find remaining occurrences
  public function remaining_occurrences($from = null) {
    if($from === null) $from = time();
    // TODO: raise if schedule doesn't have an end time
    // raise ArgumentError.new('Schedule must have an end_time to use remaining_occurrences') unless @end_time
    return $this->occurrences_between($from, $this->end_time);
  }
  
  # Retrieve the first (n) occurrences of the schedule.  May return less than
  # n results, if the rules end before n results are reached.
  public function first($n = null) {
    $dates = $this->find_occurrences(function($head) use ($n) {
      return $head->first($n ? $n : 1);
    });
    return $n === null ? $dates[0] : array_slice($dates, 0, $n);
  }
  
  public function add_recurrence_date($date) {
    if($date) {
      $this->rdates[] = $date;
    }
  }
  
  public function add_exception_date($date) {
    if($date) {
      $this->exdates[] = $date;
    }
  }
  
  public function add_recurrence_rule($rule) {
    // TODO: check $rule and raise if invalid?
    $this->rrule_occurrence_heads[] = new RuleOccurrence($rule, $this->start_date, $this->end_time);
  }
  
  public function add_exception_rule($rule) {
    // TODO: raise ArgumentError.new('Argument must be a valid rule') unless rule.class < Rule
    $this->exrule_occurrence_heads[] = new RuleOccurrence($rule, $this->start_date, $this->end_time);
  }
  
  public function occurrences_between($begin_time, $end_time) {
    // adjust to the proper end date
    if($this->end_time && $this->end_time < $end_time)
      $end_time = $this->end_time;
    // collect the occurrences
    $include_dates = array_values($this->rdates);
    $exclude_dates = array_values($this->exdates);
    foreach($this->rrule_occurrence_heads as $rrule_occurrence_head)
      $include_dates = array_merge($include_dates, $rrule_occurrence_head->between($begin_time, $end_time));
    foreach($this->exrule_occurrence_heads as $exrule_occurrence_head)
      $exclude_dates = array_merge($exclude_dates, $exrule_occurrence_head->between($begin_time, $end_time));
    $reject = array();
    foreach($include_dates as $include_date) {
      if(in_array($include_date, $exclude_dates) || $include_date < $begin_time || $include_date > $end_time) {
        $reject[] = $include_date; 
      }
    }
    $include_dates = array_diff($include_dates, $reject);
    return $include_dates;
  }
  
  // Find all occurrences (following rules and exceptions) from the schedule's start date to end date
  // Use custom methods to say when to end
  private function find_occurrences($block) {
    $exclude_dates = array_values($this->exdates);
    $include_dates = array_values($this->rdates);
                
    # walk through each rule, adding it to dates
    foreach($this->rrule_occurrence_heads as $rrule_occurrence_head) {
      $include_dates = array_merge($include_dates, $block($rrule_occurrence_head));
    }
    # walk through each exrule, removing it from dates
    foreach($this->exrule_occurrence_heads as $exrule_occurrence_head) {
      $exclude_dates = array_merge($exclude_dates, $block($exrule_occurrence_head));
    }
    // return a uniue list of dates
    return array_diff($include_dates, $exclude_dates);
  }
}
?>