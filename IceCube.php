<?php
namespace IceCube;

require 'Schedule.php';
require 'TimeUtil.php';
require 'Rule.php';
require 'RuleOccurrence.php';
require 'rules/DailyRule.php';
require 'rules/HourlyRule.php';
require 'rules/YearlyRule.php';
require 'rules/WeeklyRule.php';
require 'rules/MonthlyRule.php';
require 'Validation.php';
require 'ValidationCollection.php';
require 'validations/DayValidation.php';
require 'validations/MonthOfYearValidation.php';
require 'validations/SecondOfMinuteValidation.php';

class IceCube {
  const ONE_DAY     = 86400;
  const ONE_HOUR    = 3600;
  const ONE_MINUTE  = 60;
  const ONE_SECOND  = 1;
  public static $DAYS = array(
    'sunday'    => 0,
    'monday'    => 1,
    'tuesday'   => 2,
    'wednesday' => 3,
    'thursday'  => 4,
    'friday'    => 5,
    'saturday'  => 6 
  );
  public static $MONTHS = array(
    'january'   => 1,
    'february'  => 2,
    'march'     => 3,
    'april'     => 4,
    'may'       => 5,
    'june'      => 6,
    'july'      => 7,
    'august'    => 8,
    'september' => 9,
    'october'   => 10,
    'november'  => 11,
    'december'  => 12
  );
}
?>