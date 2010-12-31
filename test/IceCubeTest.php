<?php
class IceCubeTest extends PHPUnit_Framework_TestCase
{
  private $WEDNESDAY;
  private $DAY;
  
  public function setup() {
    $this->DAY       = strtotime("2010-01-01");
    $this->WEDNESDAY = strtotime("2010-06-23 05:00:00");
  }
    
  public function testComplexCombinations() {
    $start_date = strtotime("01 January 2010");
    $schedule = new IceCube\Schedule($start_date);
    $schedule->add_recurrence_rule(IceCube\Rule::yearly(2)->day("wednesday")->month_of_year("april"));
    $dates = $schedule->occurrences(strtotime("31 December 2011"));
    $this->assertEquals(4, count($dates));
    foreach($dates as $date) {
      $arr2 = getdate($start_date);
      $arr  = getdate($date);
      $this->assertEquals(3, $arr["wday"]);
      $this->assertEquals(4, $arr["mon"]);
        $this->assertEquals($arr["year"], $arr2["year"]);
    }
  }
  
  public function testSingleDateEvent() {
    $start_date = time();
    $schedule   = new IceCube\Schedule($start_date);
    $schedule->add_recurrence_date(strtotime(date("Y-m-d",$start_date)."+2 day"));
    $dates = $schedule->occurrences(strtotime(date("Y-m-d",$start_date)."+50 day"));
    $this->assertEquals(1, count($dates));
    $this->assertEquals(strtotime(date("Y-m-d",$start_date)."+2 day"), $dates[0]);
  }
  
  public function testReturnsNothingWithSingleExcludedDate() {
    $start_date = time();
    $schedule = new IceCube\Schedule($start_date);
    $schedule->add_recurrence_date(strtotime(date("Y-m-d",$start_date)."+2 day"));
    $schedule->add_exception_date(strtotime(date("Y-m-d",$start_date)."+2 day"));
    $this->assertEquals(0, count($schedule->occurrences($start_date + 50*IceCube\IceCube::ONE_DAY)));
  }
  
  public function testCombinationOfRecurrenceAndExceptionRule() {
    $start_date = strtotime("01 January 2010");
    $schedule = new IceCube\Schedule($start_date);
    $schedule->add_recurrence_rule(IceCube\Rule::daily()); // every day
    $schedule->add_exception_rule(IceCube\Rule::weekly()->day("monday", "tuesday", "wednesday")); // except these
    $this->assertEquals(8, count($schedule->occurrences($start_date + 13*IceCube\IceCube::ONE_DAY)));
  }
  
  public function testExcludeCertainDateFromRange() {
    $start_date = time();
    $schedule = new IceCube\Schedule($start_date);
    $schedule->add_recurrence_rule(IceCube\Rule::daily());
    $schedule->add_exception_date($start_date + 1 * IceCube\IceCube::ONE_DAY); # all days except tomorrow
    $dates = $schedule->occurrences($start_date + 13 * IceCube\IceCube::ONE_DAY); # 2 weeks
    $this->assertEquals(13, count($dates));
    $this->assertFalse(in_array($start_date + 1 * IceCube\IceCube::ONE_DAY, $dates));
  }
  
  public function testScheduleWithStartDateNotInRuleCountBehavesProperly() {
    $start_date = $this->WEDNESDAY;
    $schedule = new IceCube\Schedule($start_date);
    $schedule->add_recurrence_rule(IceCube\Rule::weekly()->day('thursday')->count(5));
    $dates = $schedule->all_occurrences();
    $this->assertEquals(5, count(array_unique($dates)));
    foreach($dates as $date) {
      $arr = getdate($date);
      $this->assertEquals(4, $arr["wday"]);
    }
    $this->assertFalse(in_array($this->WEDNESDAY, $dates));
  }
  
  public function testScheduleWithStartDateIncludedInRuleCountBehavesProperly() {
    $start_date = $this->WEDNESDAY + IceCube\IceCube::ONE_DAY;
    $schedule = new IceCube\Schedule($start_date);
    $schedule->add_recurrence_rule(IceCube\Rule::weekly()->day('thursday')->count(5));
    $dates = $schedule->all_occurrences();
    $this->assertEquals(5, count(array_unique($dates)));
    foreach($dates as $date) {
      $arr = getdate($date);
      $this->assertEquals(4, $arr["wday"]);
    }
    $this->assertTrue(in_array($this->WEDNESDAY+IceCube\IceCube::ONE_DAY, $dates));
  }
  
  public function testSecondOfMinuteRuleWorksProperly() {
    $start_date = $this->DAY;
    $schedule = new IceCube\Schedule($start_date);
    $schedule->add_recurrence_rule(IceCube\Rule::weekly()->second_of_minute(30));
    $dates = $schedule->occurrences($start_date + 30 * 60);
    foreach($dates as $date) {
      $arr = getdate($date);
      $this->assertEquals(30, $arr["seconds"]);
    }
  }
  
  public function testNoOccurrencesWhenCountIsZero() {
    $start_date = $this->DAY;
    $schedule = new IceCube\Schedule($start_date);
    $schedule->add_recurrence_rule(IceCube\Rule::daily()->count(0));
    $this->assertEquals(array(), $schedule->all_occurrences());
  }
  
  // TODO: it 'should be able to be schedules at 1:0:st and 2:0:st every day' do
  // TODO: it 'will only return count# if you specify a count and use .first' do
  // TODO: it 'occurs yearly' do
    
  public function testOccursYearly() {
    $start_date = $this->DAY;
    $schedule = new IceCube\Schedule($start_date);
    $schedule->add_recurrence_rule(IceCube\Rule::yearly());
    
    $dates = $schedule->first(10);
    
    $start_date_arr = getdate($start_date);
    foreach($dates as $date) {
      $date_arr = getdate($date);
      $this->assertEquals($start_date_arr["mon"], $date_arr["mon"]);
      $this->assertEquals($start_date_arr["day"], $date_arr["day"]);
      $this->assertEquals($start_date_arr["hour"], $date_arr["hour"]);
      $this->assertEquals($start_date_arr["minutes"], $date_arr["minutes"]);
      $this->assertEquals($start_date_arr["seconds"], $date_arr["seconds"]);
    }
  }
  
  public function testOccursMonthly() {
    $start_date = time();
    $schedule = new IceCube\Schedule($start_date);
    $schedule->add_recurrence_rule(IceCube\Rule::monthly());
    $dates = $schedule->first(10);
    
    $start_date_arr = getdate($start_date);
    foreach($dates as $date) {
      $date_arr = getdate($date);
      $this->assertEquals($start_date_arr["mon"], $date_arr["mon"]);
      $this->assertEquals($start_date_arr["hour"], $date_arr["hour"]);
      $this->assertEquals($start_date_arr["minutes"], $date_arr["minutes"]);
      $this->assertEquals($start_date_arr["seconds"], $date_arr["seconds"]);
    }
  }
  
  public function testOccursDaily() {
    $start_date = time();
    $schedule = new IceCube\Schedule($start_date);
    $schedule->add_recurrence_rule(IceCube\Rule::daily());
    $dates = $schedule->first(10);
    $start_date_arr = getdate($start_date);
    foreach($dates as $date) {
      $date_arr = getdate($date);
      $this->assertEquals($start_date_arr["hour"], $date_arr["hour"]);
      $this->assertEquals($start_date_arr["minutes"], $date_arr["minutes"]);
      $this->assertEquals($start_date_arr["seconds"], $date_arr["seconds"]);
    }
  }
  
  public function testOccursHourly() {
    $start_date = time();
    $schedule = new IceCube\Schedule($start_date);
    $schedule->add_recurrence_rule(IceCube\Rule::hourly());
    $dates = $schedule->first(10);
    $start_date_arr = getdate($start_date);
    foreach($dates as $date) {
      $date_arr = getdate($date);
      $this->assertEquals($start_date_arr["minutes"], $date_arr["minutes"]);
      $this->assertEquals($start_date_arr["seconds"], $date_arr["seconds"]);
    }    
  }
  
  // TODO: it 'occurs minutely' do
  // TODO: it 'occurs every second for an hour' do
}
?>