<?php
require 'test_helper.php';

class DailyRuleTest extends PHPUnit_Framework_TestCase
{  
  public function testCorrectDaysForInterval1() {
    $start_date = strtotime("01/03/2010");
    $schedule = new IceCube\Schedule($start_date);
    $schedule->add_recurrence_rule(IceCube\Rule::daily());
    $dates = $schedule->occurrences($start_date + 2 * IceCube\IceCube::ONE_DAY);
    $this->assertEquals(3, count($dates));
    $this->assertEquals(array($start_date, $start_date + 1 * IceCube\IceCube::ONE_DAY, $start_date + 2 * IceCube\IceCube::ONE_DAY), $dates);
  }
  
  public function testCorrectDaysForInterval2() {
    $start_date = strtotime("01/03/2010");
    $schedule = new IceCube\Schedule($start_date);
    $schedule->add_recurrence_rule(IceCube\Rule::daily(2));
    $dates = $schedule->occurrences($start_date + 5 * IceCube\IceCube::ONE_DAY);
    $this->assertEquals(3, count($dates));
    $this->assertEquals(array($start_date, $start_date + 2 * IceCube\IceCube::ONE_DAY, $start_date + 4 * IceCube\IceCube::ONE_DAY), $dates);
  }
}
?>