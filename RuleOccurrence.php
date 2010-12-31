<?php
namespace IceCube;
class RuleOccurrence
{
  private $rule;
  private $start_date;
  private $end_time;
  private $date;
  private $index;
  
  public function __construct($rule, $start_date, $end_time, $date = null, $index = 0) {
    $this->rule       = $rule;
    $this->start_date = $start_date;
    $this->end_time   = $end_time;
    $this->date       = $date;
    $this->index      = $index;
  }
  
  public function to_time() {
    return $this->date;
  }
  
  public function all_occurrences() {
    // TODO: raise ArgumentError.new("Rule must specify either an until date or a count to use 'all_occurrences'") unless @rule.occurrence_count || @rule.until_date || @end_time
    if($this->rule->get_occurrence_count() === null && !$this->rule->get_until_date() && !$this->end_time)
      die("Rule must specify either an until date or a count");
      //throw new Exception("Rule must specify either an until date or a count to use 'all_occurrences'");
    return $this->find_occurrences(function() { return false; });
  }
  
  public function between($begin_time, $end_time) {
    // TODO: find_occurrences { |roc| roc > end_time }.select { |d| d >= begin_time }
    $selected = array();
    $occurrences = $this->find_occurrences(function($val) use ($begin_time, $end_time) {
      return $val > $end_time;
    });
    foreach($occurrences as $occurrence) {
      if($occurrence >= $begin_time) $selected[] = $occurrence;
    }
    return $selected;
  }
  
  public function upto($end_date) {        
    return $this->find_occurrences(function($val) use ($end_date) {       
      return $val > $end_date;
    });
  }
  
  public function first($n) {
    $count = 0;
    return $this->find_occurrences(function($roc) use (&$count, $n) {
      $count += 1;
      return $count > $n;
    });
  }
  
  // get the next occurrence of this rule
  public function succ($filter = null) {
    if($this->rule->get_occurrence_count() !== null && $this->index >= $this->rule->get_occurrence_count())
      return null;
     
    $date = null; 
    if($this->date === null) {
      if($this->rule->validate_single_date($this->start_date))
        $date = $this->start_date;        
      if(!$date)
        $date = $this->rule->next_suggestion($this->start_date);
    } else {
      $date = $this->rule->next_suggestion($this->date);
    }
        
    do {
      if($this->end_time && $date > $this->end_time) return null;
      if($this->rule->get_until_date() && $date > $this->rule->get_until_date()) return null;      
      if(!$this->rule->in_interval($date, $this->start_date)) continue;
      if($filter && $filter($date)) return null;
      return new RuleOccurrence($this->rule, $this->start_date, $this->end_time, $date, $this->index+1);
    } while($date = $this->rule->next_suggestion($date));
    
  } 
  
  private function find_occurrences($block) {
    $include_dates = array();
    $roc = $this;
    do {
      if($roc === null)
        break;
      if($roc->to_time() === null)
        continue;
      $include_dates[] = $roc->to_time();
    } while($roc = $roc->succ($block));
    return $include_dates;
  }
}
?>