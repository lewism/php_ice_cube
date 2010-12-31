<?php
namespace IceCube;
class TimeUtil
{
  public static $LEAP_YEAR_MONTH_DAYS    = array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
  public static $COMMON_YEAR_MONTH_DAYS  = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
  
  public static function days_in_month($date) {
    $arr   = getdate($date);
    $year  = $arr["year"];
    $month = $arr["mon"];
    return self::is_leap_year($year) ? self::$LEAP_YEAR_MONTH_DAYS[$month - 1] : self::$COMMON_YEAR_MONTH_DAYS[$month - 1];
  }
  
  public static function is_leap_year($year) {
    return ($year % 4 == 0 && $year % 100 != 0) || ($year % 400 == 0);
  }
  
  public static function date_in_n_months($start_date, $months) {
    return strtotime(date("Y-m-d H:i:s", $start_date)."+$months month");
  }
}
?>