<?php

// Start of date v.5.4.3-4~precise+1

/** @jms-builtin */
class DateTime  {
	const ATOM = "Y-m-d\TH:i:sP";
	const COOKIE = "l, d-M-y H:i:s T";
	const ISO8601 = "Y-m-d\TH:i:sO";
	const RFC822 = "D, d M y H:i:s O";
	const RFC850 = "l, d-M-y H:i:s T";
	const RFC1036 = "D, d M y H:i:s O";
	const RFC1123 = "D, d M Y H:i:s O";
	const RFC2822 = "D, d M Y H:i:s O";
	const RFC3339 = "Y-m-d\TH:i:sP";
	const RSS = "D, d M Y H:i:s O";
	const W3C = "Y-m-d\TH:i:sP";


	/**
	 * @param $time [optional]
	 * @param $object [optional]
	 */
	public function __construct ($time, $object) {}

	public function __wakeup () {}

	public static function __set_state () {}

	/**
	 * @param $format
	 * @param $time
	 * @param $object [optional]
	 */
	public static function createFromFormat ($format, $time, $object) {}

	public static function getLastErrors () {}

	/**
	 * @param $format
	 */
	public function format ($format) {}

	/**
	 * @param $modify
	 */
	public function modify ($modify) {}

	/**
	 * @param $interval
	 */
	public function add ($interval) {}

	/**
	 * @param $interval
	 */
	public function sub ($interval) {}

	public function getTimezone () {}

	/**
	 * @param $timezone
	 */
	public function setTimezone ($timezone) {}

	public function getOffset () {}

	/**
	 * @param $hour
	 * @param $minute
	 * @param $second [optional]
	 */
	public function setTime ($hour, $minute, $second) {}

	/**
	 * @param $year
	 * @param $month
	 * @param $day
	 */
	public function setDate ($year, $month, $day) {}

	/**
	 * @param $year
	 * @param $week
	 * @param $day [optional]
	 */
	public function setISODate ($year, $week, $day) {}

	/**
	 * @param $unixtimestamp
	 */
	public function setTimestamp ($unixtimestamp) {}

	public function getTimestamp () {}

	/**
	 * @param $object
	 * @param $absolute [optional]
	 */
	public function diff ($object, $absolute) {}

}

/** @jms-builtin */
class DateTimeZone  {
	const AFRICA = 1;
	const AMERICA = 2;
	const ANTARCTICA = 4;
	const ARCTIC = 8;
	const ASIA = 16;
	const ATLANTIC = 32;
	const AUSTRALIA = 64;
	const EUROPE = 128;
	const INDIAN = 256;
	const PACIFIC = 512;
	const UTC = 1024;
	const ALL = 2047;
	const ALL_WITH_BC = 4095;
	const PER_COUNTRY = 4096;


	/**
	 * @param $timezone
	 */
	public function __construct ($timezone) {}

	public function getName () {}

	/**
	 * @param $datetime
	 */
	public function getOffset ($datetime) {}

	/**
	 * @param $timestamp_begin
	 * @param $timestamp_end
	 */
	public function getTransitions ($timestamp_begin, $timestamp_end) {}

	public function getLocation () {}

	public static function listAbbreviations () {}

	/**
	 * @param $what [optional]
	 * @param $country [optional]
	 */
	public static function listIdentifiers ($what, $country) {}

}

/** @jms-builtin */
class DateInterval  {
    /** @var integer */
    public $y;

    /** @var integer */
    public $m;

    /** @var integer */
    public $d;

    /** @var integer */
    public $h;

    /** @var integer */
    public $i;

    /** @var integer */
    public $s;

    /** @var integer */
    public $invert;

    /** @var integer|false */
    public $days;

	/**
	 * @param $interval_spec [optional]
	 */
	public function __construct ($interval_spec) {}

	public function __wakeup () {}

	public static function __set_state () {}

	/**
	 * @param $format
	 */
	public function format ($format) {}

	/**
	 * @param $time
	 */
	public static function createFromDateString ($time) {}

}

/** @jms-builtin */
class DatePeriod implements Traversable {
	const EXCLUDE_START_DATE = 1;


	/**
	 * @param $start
	 * @param $interval
	 * @param $end
	 */
	public function __construct ($start, $interval, $end) {}

}

/**
 * (PHP 4, PHP 5)<br/>
 * Parse about any English textual datetime description into a Unix timestamp
 * @link http://php.net/manual/en/function.strtotime.php
 * @param string $time <p>A date/time string. Valid formats are explained in Date and Time Formats.</p>
 * @param int $now [optional] <p>
 * The timestamp which is used as a base for the calculation of relative
 * dates.
 * </p>
 * @return int a timestamp on success, <b>FALSE</b> otherwise. Previous to PHP 5.1.0,
 * this function would return -1 on failure.
 * @jms-builtin
 */
function strtotime ($time, $now = 'time()') {}

/**
 * (PHP 4, PHP 5)<br/>
 * Format a local time/date
 * @link http://php.net/manual/en/function.date.php
 * @param string $format <p>
 * The format of the outputted date string. See the formatting
 * options below. There are also several
 * predefined date constants
 * that may be used instead, so for example <b>DATE_RSS</b>
 * contains the format string 'D, d M Y H:i:s'.
 * </p>
 * <p>
 * <table>
 * The following characters are recognized in the
 * <i>format</i> parameter string
 * <tr valign="top">
 * <td><i>format</i> character</td>
 * <td>Description</td>
 * <td>Example returned values</td>
 * </tr>
 * <tr valign="top">
 * Day</td>
 * <td>---</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>d</td>
 * <td>Day of the month, 2 digits with leading zeros</td>
 * <td>01 to 31</td>
 * </tr>
 * <tr valign="top">
 * <td>D</td>
 * <td>A textual representation of a day, three letters</td>
 * <td>Mon through Sun</td>
 * </tr>
 * <tr valign="top">
 * <td>j</td>
 * <td>Day of the month without leading zeros</td>
 * <td>1 to 31</td>
 * </tr>
 * <tr valign="top">
 * <td>l (lowercase 'L')</td>
 * <td>A full textual representation of the day of the week</td>
 * <td>Sunday through Saturday</td>
 * </tr>
 * <tr valign="top">
 * <td>N</td>
 * <td>ISO-8601 numeric representation of the day of the week (added in
 * PHP 5.1.0)</td>
 * <td>1 (for Monday) through 7 (for Sunday)</td>
 * </tr>
 * <tr valign="top">
 * <td>S</td>
 * <td>English ordinal suffix for the day of the month, 2 characters</td>
 * <td>
 * st, nd, rd or
 * th. Works well with j
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>w</td>
 * <td>Numeric representation of the day of the week</td>
 * <td>0 (for Sunday) through 6 (for Saturday)</td>
 * </tr>
 * <tr valign="top">
 * <td>z</td>
 * <td>The day of the year (starting from 0)</td>
 * <td>0 through 365</td>
 * </tr>
 * <tr valign="top">
 * Week</td>
 * <td>---</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>W</td>
 * <td>ISO-8601 week number of year, weeks starting on Monday (added in PHP 4.1.0)</td>
 * <td>Example: 42 (the 42nd week in the year)</td>
 * </tr>
 * <tr valign="top">
 * Month</td>
 * <td>---</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>F</td>
 * <td>A full textual representation of a month, such as January or March</td>
 * <td>January through December</td>
 * </tr>
 * <tr valign="top">
 * <td>m</td>
 * <td>Numeric representation of a month, with leading zeros</td>
 * <td>01 through 12</td>
 * </tr>
 * <tr valign="top">
 * <td>M</td>
 * <td>A short textual representation of a month, three letters</td>
 * <td>Jan through Dec</td>
 * </tr>
 * <tr valign="top">
 * <td>n</td>
 * <td>Numeric representation of a month, without leading zeros</td>
 * <td>1 through 12</td>
 * </tr>
 * <tr valign="top">
 * <td>t</td>
 * <td>Number of days in the given month</td>
 * <td>28 through 31</td>
 * </tr>
 * <tr valign="top">
 * Year</td>
 * <td>---</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>L</td>
 * <td>Whether it's a leap year</td>
 * <td>1 if it is a leap year, 0 otherwise.</td>
 * </tr>
 * <tr valign="top">
 * <td>o</td>
 * <td>ISO-8601 year number. This has the same value as
 * Y, except that if the ISO week number
 * (W) belongs to the previous or next year, that year
 * is used instead. (added in PHP 5.1.0)</td>
 * <td>Examples: 1999 or 2003</td>
 * </tr>
 * <tr valign="top">
 * <td>Y</td>
 * <td>A full numeric representation of a year, 4 digits</td>
 * <td>Examples: 1999 or 2003</td>
 * </tr>
 * <tr valign="top">
 * <td>y</td>
 * <td>A two digit representation of a year</td>
 * <td>Examples: 99 or 03</td>
 * </tr>
 * <tr valign="top">
 * Time</td>
 * <td>---</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>a</td>
 * <td>Lowercase Ante meridiem and Post meridiem</td>
 * <td>am or pm</td>
 * </tr>
 * <tr valign="top">
 * <td>A</td>
 * <td>Uppercase Ante meridiem and Post meridiem</td>
 * <td>AM or PM</td>
 * </tr>
 * <tr valign="top">
 * <td>B</td>
 * <td>Swatch Internet time</td>
 * <td>000 through 999</td>
 * </tr>
 * <tr valign="top">
 * <td>g</td>
 * <td>12-hour format of an hour without leading zeros</td>
 * <td>1 through 12</td>
 * </tr>
 * <tr valign="top">
 * <td>G</td>
 * <td>24-hour format of an hour without leading zeros</td>
 * <td>0 through 23</td>
 * </tr>
 * <tr valign="top">
 * <td>h</td>
 * <td>12-hour format of an hour with leading zeros</td>
 * <td>01 through 12</td>
 * </tr>
 * <tr valign="top">
 * <td>H</td>
 * <td>24-hour format of an hour with leading zeros</td>
 * <td>00 through 23</td>
 * </tr>
 * <tr valign="top">
 * <td>i</td>
 * <td>Minutes with leading zeros</td>
 * <td>00 to 59</td>
 * </tr>
 * <tr valign="top">
 * <td>s</td>
 * <td>Seconds, with leading zeros</td>
 * <td>00 through 59</td>
 * </tr>
 * <tr valign="top">
 * <td>u</td>
 * <td>Microseconds (added in PHP 5.2.2)</td>
 * <td>Example: 654321</td>
 * </tr>
 * <tr valign="top">
 * Timezone</td>
 * <td>---</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>e</td>
 * <td>Timezone identifier (added in PHP 5.1.0)</td>
 * <td>Examples: UTC, GMT, Atlantic/Azores</td>
 * </tr>
 * <tr valign="top">
 * <td>I (capital i)</td>
 * <td>Whether or not the date is in daylight saving time</td>
 * <td>1 if Daylight Saving Time, 0 otherwise.</td>
 * </tr>
 * <tr valign="top">
 * <td>O</td>
 * <td>Difference to Greenwich time (GMT) in hours</td>
 * <td>Example: +0200</td>
 * </tr>
 * <tr valign="top">
 * <td>P</td>
 * <td>Difference to Greenwich time (GMT) with colon between hours and minutes (added in PHP 5.1.3)</td>
 * <td>Example: +02:00</td>
 * </tr>
 * <tr valign="top">
 * <td>T</td>
 * <td>Timezone abbreviation</td>
 * <td>Examples: EST, MDT ...</td>
 * </tr>
 * <tr valign="top">
 * <td>Z</td>
 * <td>Timezone offset in seconds. The offset for timezones west of UTC is always
 * negative, and for those east of UTC is always positive.</td>
 * <td>-43200 through 50400</td>
 * </tr>
 * <tr valign="top">
 * Full Date/Time</td>
 * <td>---</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>c</td>
 * <td>ISO 8601 date (added in PHP 5)</td>
 * <td>2004-02-12T15:19:21+00:00</td>
 * </tr>
 * <tr valign="top">
 * <td>r</td>
 * <td>RFC 2822 formatted date</td>
 * <td>Example: Thu, 21 Dec 2000 16:01:07 +0200</td>
 * </tr>
 * <tr valign="top">
 * <td>U</td>
 * <td>Seconds since the Unix Epoch (January 1 1970 00:00:00 GMT)</td>
 * <td>See also <b>time</b></td>
 * </tr>
 * </table>
 * </p>
 * <p>
 * Unrecognized characters in the format string will be printed
 * as-is. The Z format will always return
 * 0 when using <b>gmdate</b>.
 * </p>
 * <p>
 * Since this function only accepts integer timestamps the
 * u format character is only useful when using the
 * <b>date_format</b> function with user based timestamps
 * created with <b>date_create</b>.
 * </p>
 * @param int $timestamp [optional]
 * @return string a formatted date string. If a non-numeric value is used for
 * <i>timestamp</i>, <b>FALSE</b> is returned and an
 * <b>E_WARNING</b> level error is emitted.
 * @jms-builtin
 */
function date ($format, $timestamp = 'time()') {}

/**
 * (PHP 5)<br/>
 * Format a local time/date as integer
 * @link http://php.net/manual/en/function.idate.php
 * @param string $format <p>
 * <table>
 * The following characters are recognized in the
 * <i>format</i> parameter string
 * <tr valign="top">
 * <td><i>format</i> character</td>
 * <td>Description</td>
 * </tr>
 * <tr valign="top">
 * <td>B</td>
 * <td>Swatch Beat/Internet Time</td>
 * </tr>
 * <tr valign="top">
 * <td>d</td>
 * <td>Day of the month</td>
 * </tr>
 * <tr valign="top">
 * <td>h</td>
 * <td>Hour (12 hour format)</td>
 * </tr>
 * <tr valign="top">
 * <td>H</td>
 * <td>Hour (24 hour format)</td>
 * </tr>
 * <tr valign="top">
 * <td>i</td>
 * <td>Minutes</td>
 * </tr>
 * <tr valign="top">
 * <td>I (uppercase i)</td>
 * <td>returns 1 if DST is activated,
 * 0 otherwise</td>
 * </tr>
 * <tr valign="top">
 * <td>L (uppercase l)</td>
 * <td>returns 1 for leap year,
 * 0 otherwise</td>
 * </tr>
 * <tr valign="top">
 * <td>m</td>
 * <td>Month number</td>
 * </tr>
 * <tr valign="top">
 * <td>s</td>
 * <td>Seconds</td>
 * </tr>
 * <tr valign="top">
 * <td>t</td>
 * <td>Days in current month</td>
 * </tr>
 * <tr valign="top">
 * <td>U</td>
 * <td>Seconds since the Unix Epoch - January 1 1970 00:00:00 UTC -
 * this is the same as <b>time</b></td>
 * </tr>
 * <tr valign="top">
 * <td>w</td>
 * <td>Day of the week (0 on Sunday)</td>
 * </tr>
 * <tr valign="top">
 * <td>W</td>
 * <td>ISO-8601 week number of year, weeks starting on
 * Monday</td>
 * </tr>
 * <tr valign="top">
 * <td>y</td>
 * <td>Year (1 or 2 digits - check note below)</td>
 * </tr>
 * <tr valign="top">
 * <td>Y</td>
 * <td>Year (4 digits)</td>
 * </tr>
 * <tr valign="top">
 * <td>z</td>
 * <td>Day of the year</td>
 * </tr>
 * <tr valign="top">
 * <td>Z</td>
 * <td>Timezone offset in seconds</td>
 * </tr>
 * </table>
 * </p>
 * @param int $timestamp [optional]
 * @return int an integer.
 * </p>
 * <p>
 * As <b>idate</b> always returns an integer and
 * as they can't start with a "0", <b>idate</b> may return
 * fewer digits than you would expect. See the example below.
 * @jms-builtin
 */
function idate ($format, $timestamp = 'time()') {}

/**
 * (PHP 4, PHP 5)<br/>
 * Format a GMT/UTC date/time
 * @link http://php.net/manual/en/function.gmdate.php
 * @param string $format <p>
 * The format of the outputted date string. See the formatting
 * options for the <b>date</b> function.
 * </p>
 * @param int $timestamp [optional]
 * @return string a formatted date string. If a non-numeric value is used for
 * <i>timestamp</i>, <b>FALSE</b> is returned and an
 * <b>E_WARNING</b> level error is emitted.
 * @jms-builtin
 */
function gmdate ($format, $timestamp = 'time()') {}

/**
 * (PHP 4, PHP 5)<br/>
 * Get Unix timestamp for a date
 * @link http://php.net/manual/en/function.mktime.php
 * @param int $hour [optional] <p>
 * The number of the hour relative to the start of the day determined by
 * <i>month</i>, <i>day</i> and <i>year</i>.
 * Negative values reference the hour before midnight of the day in question.
 * Values greater than 23 reference the appropriate hour in the following day(s).
 * </p>
 * @param int $minute [optional] <p>
 * The number of the minute relative to the start of the <i>hour</i>.
 * Negative values reference the minute in the previous hour.
 * Values greater than 59 reference the appropriate minute in the following hour(s).
 * </p>
 * @param int $second [optional] <p>
 * The number of seconds relative to the start of the <i>minute</i>.
 * Negative values reference the second in the previous minute.
 * Values greater than 59 reference the appropriate second in the following minute(s).
 * </p>
 * @param int $month [optional] <p>
 * The number of the month relative to the end of the previous year.
 * Values 1 to 12 reference the normal calendar months of the year in question.
 * Values less than 1 (including negative values) reference the months in the previous year in reverse order, so 0 is December, -1 is November, etc.
 * Values greater than 12 reference the appropriate month in the following year(s).
 * </p>
 * @param int $day [optional] <p>
 * The number of the day relative to the end of the previous month.
 * Values 1 to 28, 29, 30 or 31 (depending upon the month) reference the normal days in the relevant month.
 * Values less than 1 (including negative values) reference the days in the previous month, so 0 is the last day of the previous month, -1 is the day before that, etc.
 * Values greater than the number of days in the relevant month reference the appropriate day in the following month(s).
 * </p>
 * @param int $year [optional] <p>
 * The number of the year, may be a two or four digit value,
 * with values between 0-69 mapping to 2000-2069 and 70-100 to
 * 1970-2000. On systems where time_t is a 32bit signed integer, as
 * most common today, the valid range for <i>year</i>
 * is somewhere between 1901 and 2038. However, before PHP 5.1.0 this
 * range was limited from 1970 to 2038 on some systems (e.g. Windows).
 * </p>
 * @param int $is_dst [optional] <p>
 * This parameter can be set to 1 if the time is during daylight savings time (DST),
 * 0 if it is not, or -1 (the default) if it is unknown whether the time is within
 * daylight savings time or not. If it's unknown, PHP tries to figure it out itself.
 * This can cause unexpected (but not incorrect) results.
 * Some times are invalid if DST is enabled on the system PHP is running on or
 * <i>is_dst</i> is set to 1. If DST is enabled in e.g. 2:00, all times
 * between 2:00 and 3:00 are invalid and <b>mktime</b> returns an undefined
 * (usually negative) value.
 * Some systems (e.g. Solaris 8) enable DST at midnight so time 0:30 of the day when DST
 * is enabled is evaluated as 23:30 of the previous day.
 * </p>
 * <p>
 * As of PHP 5.1.0, this parameter became deprecated. As a result, the
 * new timezone handling features should be used instead.
 * </p>
 * @return int <b>mktime</b> returns the Unix timestamp of the arguments
 * given.
 * If the arguments are invalid, the function returns <b>FALSE</b> (before PHP 5.1
 * it returned -1).
 * @jms-builtin
 */
function mktime ($hour = 'date("H")', $minute = 'date("i")', $second = 'date("s")', $month = 'date("n")', $day = 'date("j")', $year = 'date("Y")', $is_dst = -1) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Get Unix timestamp for a GMT date
 * @link http://php.net/manual/en/function.gmmktime.php
 * @param int $hour [optional] <p>
 * The number of the hour relative to the start of the day determined by
 * <i>month</i>, <i>day</i> and <i>year</i>.
 * Negative values reference the hour before midnight of the day in question.
 * Values greater than 23 reference the appropriate hour in the following day(s).
 * </p>
 * @param int $minute [optional] <p>
 * The number of the minute relative to the start of the <i>hour</i>.
 * Negative values reference the minute in the previous hour.
 * Values greater than 59 reference the appropriate minute in the following hour(s).
 * </p>
 * @param int $second [optional] <p>
 * The number of seconds relative to the start of the <i>minute</i>.
 * Negative values reference the second in the previous minute.
 * Values greater than 59 reference the appropriate second in the following minute(s).
 * </p>
 * @param int $month [optional] <p>
 * The number of the month relative to the end of the previous year.
 * Values 1 to 12 reference the normal calendar months of the year in question.
 * Values less than 1 (including negative values) reference the months in the previous year in reverse order, so 0 is December, -1 is November, etc.
 * Values greater than 12 reference the appropriate month in the following year(s).
 * </p>
 * @param int $day [optional] <p>
 * The number of the day relative to the end of the previous month.
 * Values 1 to 28, 29, 30 or 31 (depending upon the month) reference the normal days in the relevant month.
 * Values less than 1 (including negative values) reference the days in the previous month, so 0 is the last day of the previous month, -1 is the day before that, etc.
 * Values greater than the number of days in the relevant month reference the appropriate day in the following month(s).
 * </p>
 * @param int $year [optional] <p>
 * The year
 * </p>
 * @param int $is_dst [optional] <p>
 * Parameters always represent a GMT date so <i>is_dst</i>
 * doesn't influence the result.
 * </p>
 * @return int a integer Unix timestamp.
 * @jms-builtin
 */
function gmmktime ($hour = 'gmdate("H")', $minute = 'gmdate("i")', $second = 'gmdate("s")', $month = 'gmdate("n")', $day = 'gmdate("j")', $year = 'gmdate("Y")', $is_dst = -1) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Validate a Gregorian date
 * @link http://php.net/manual/en/function.checkdate.php
 * @param int $month <p>
 * The month is between 1 and 12 inclusive.
 * </p>
 * @param int $day <p>
 * The day is within the allowed number of days for the given
 * <i>month</i>. Leap <i>year</i>s
 * are taken into consideration.
 * </p>
 * @param int $year <p>
 * The year is between 1 and 32767 inclusive.
 * </p>
 * @return bool <b>TRUE</b> if the date given is valid; otherwise returns <b>FALSE</b>.
 * @jms-builtin
 */
function checkdate ($month, $day, $year) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Format a local time/date according to locale settings
 * @link http://php.net/manual/en/function.strftime.php
 * @param string $format <p>
 * <table>
 * The following characters are recognized in the
 * <i>format</i> parameter string
 * <tr valign="top">
 * <td><i>format</i></td>
 * <td>Description</td>
 * <td>Example returned values</td>
 * </tr>
 * <tr valign="top">
 * Day</td>
 * <td>---</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>%a</td>
 * <td>An abbreviated textual representation of the day</td>
 * <td>Sun through Sat</td>
 * </tr>
 * <tr valign="top">
 * <td>%A</td>
 * <td>A full textual representation of the day</td>
 * <td>Sunday through Saturday</td>
 * </tr>
 * <tr valign="top">
 * <td>%d</td>
 * <td>Two-digit day of the month (with leading zeros)</td>
 * <td>01 to 31</td>
 * </tr>
 * <tr valign="top">
 * <td>%e</td>
 * <td>
 * Day of the month, with a space preceding single digits. Not
 * implemented as described on Windows. See below for more information.
 * </td>
 * <td> 1 to 31</td>
 * </tr>
 * <tr valign="top">
 * <td>%j</td>
 * <td>Day of the year, 3 digits with leading zeros</td>
 * <td>001 to 366</td>
 * </tr>
 * <tr valign="top">
 * <td>%u</td>
 * <td>ISO-8601 numeric representation of the day of the week</td>
 * <td>1 (for Monday) though 7 (for Sunday)</td>
 * </tr>
 * <tr valign="top">
 * <td>%w</td>
 * <td>Numeric representation of the day of the week</td>
 * <td>0 (for Sunday) through 6 (for Saturday)</td>
 * </tr>
 * <tr valign="top">
 * Week</td>
 * <td>---</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>%U</td>
 * <td>Week number of the given year, starting with the first
 * Sunday as the first week</td>
 * <td>13 (for the 13th full week of the year)</td>
 * </tr>
 * <tr valign="top">
 * <td>%V</td>
 * <td>ISO-8601:1988 week number of the given year, starting with
 * the first week of the year with at least 4 weekdays, with Monday
 * being the start of the week</td>
 * <td>01 through 53 (where 53
 * accounts for an overlapping week)</td>
 * </tr>
 * <tr valign="top">
 * <td>%W</td>
 * <td>A numeric representation of the week of the year, starting
 * with the first Monday as the first week</td>
 * <td>46 (for the 46th week of the year beginning
 * with a Monday)</td>
 * </tr>
 * <tr valign="top">
 * Month</td>
 * <td>---</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>%b</td>
 * <td>Abbreviated month name, based on the locale</td>
 * <td>Jan through Dec</td>
 * </tr>
 * <tr valign="top">
 * <td>%B</td>
 * <td>Full month name, based on the locale</td>
 * <td>January through December</td>
 * </tr>
 * <tr valign="top">
 * <td>%h</td>
 * <td>Abbreviated month name, based on the locale (an alias of %b)</td>
 * <td>Jan through Dec</td>
 * </tr>
 * <tr valign="top">
 * <td>%m</td>
 * <td>Two digit representation of the month</td>
 * <td>01 (for January) through 12 (for December)</td>
 * </tr>
 * <tr valign="top">
 * Year</td>
 * <td>---</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>%C</td>
 * <td>Two digit representation of the century (year divided by 100, truncated to an integer)</td>
 * <td>19 for the 20th Century</td>
 * </tr>
 * <tr valign="top">
 * <td>%g</td>
 * <td>Two digit representation of the year going by ISO-8601:1988 standards (see %V)</td>
 * <td>Example: 09 for the week of January 6, 2009</td>
 * </tr>
 * <tr valign="top">
 * <td>%G</td>
 * <td>The full four-digit version of %g</td>
 * <td>Example: 2008 for the week of January 3, 2009</td>
 * </tr>
 * <tr valign="top">
 * <td>%y</td>
 * <td>Two digit representation of the year</td>
 * <td>Example: 09 for 2009, 79 for 1979</td>
 * </tr>
 * <tr valign="top">
 * <td>%Y</td>
 * <td>Four digit representation for the year</td>
 * <td>Example: 2038</td>
 * </tr>
 * <tr valign="top">
 * Time</td>
 * <td>---</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>%H</td>
 * <td>Two digit representation of the hour in 24-hour format</td>
 * <td>00 through 23</td>
 * </tr>
 * <tr valign="top">
 * <td>%I</td>
 * <td>Two digit representation of the hour in 12-hour format</td>
 * <td>01 through 12</td>
 * </tr>
 * <tr valign="top">
 * <td>%l (lower-case 'L')</td>
 * <td>Hour in 12-hour format, with a space preceeding single digits</td>
 * <td> 1 through 12</td>
 * </tr>
 * <tr valign="top">
 * <td>%M</td>
 * <td>Two digit representation of the minute</td>
 * <td>00 through 59</td>
 * </tr>
 * <tr valign="top">
 * <td>%p</td>
 * <td>UPPER-CASE 'AM' or 'PM' based on the given time</td>
 * <td>Example: AM for 00:31, PM for 22:23</td>
 * </tr>
 * <tr valign="top">
 * <td>%P</td>
 * <td>lower-case 'am' or 'pm' based on the given time</td>
 * <td>Example: am for 00:31, pm for 22:23</td>
 * </tr>
 * <tr valign="top">
 * <td>%r</td>
 * <td>Same as "%I:%M:%S %p"</td>
 * <td>Example: 09:34:17 PM for 21:34:17</td>
 * </tr>
 * <tr valign="top">
 * <td>%R</td>
 * <td>Same as "%H:%M"</td>
 * <td>Example: 00:35 for 12:35 AM, 16:44 for 4:44 PM</td>
 * </tr>
 * <tr valign="top">
 * <td>%S</td>
 * <td>Two digit representation of the second</td>
 * <td>00 through 59</td>
 * </tr>
 * <tr valign="top">
 * <td>%T</td>
 * <td>Same as "%H:%M:%S"</td>
 * <td>Example: 21:34:17 for 09:34:17 PM</td>
 * </tr>
 * <tr valign="top">
 * <td>%X</td>
 * <td>Preferred time representation based on locale, without the date</td>
 * <td>Example: 03:59:16 or 15:59:16</td>
 * </tr>
 * <tr valign="top">
 * <td>%z</td>
 * <td>Either the time zone offset from UTC or the abbreviation (depends
 * on operating system)</td>
 * <td>Example: -0500 or EST for Eastern Time</td>
 * </tr>
 * <tr valign="top">
 * <td>%Z</td>
 * <td>The time zone offset/abbreviation option NOT given by %z (depends
 * on operating system)</td>
 * <td>Example: -0500 or EST for Eastern Time</td>
 * </tr>
 * <tr valign="top">
 * Time and Date Stamps</td>
 * <td>---</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>%c</td>
 * <td>Preferred date and time stamp based on local</td>
 * <td>Example: Tue Feb 5 00:45:10 2009 for
 * February 5, 2009 at 12:45:10 AM</td>
 * </tr>
 * <tr valign="top">
 * <td>%D</td>
 * <td>Same as "%m/%d/%y"</td>
 * <td>Example: 02/05/09 for February 5, 2009</td>
 * </tr>
 * <tr valign="top">
 * <td>%F</td>
 * <td>Same as "%Y-%m-%d" (commonly used in database datestamps)</td>
 * <td>Example: 2009-02-05 for February 5, 2009</td>
 * </tr>
 * <tr valign="top">
 * <td>%s</td>
 * <td>Unix Epoch Time timestamp (same as the <b>time</b>
 * function)</td>
 * <td>Example: 305815200 for September 10, 1979 08:40:00 AM</td>
 * </tr>
 * <tr valign="top">
 * <td>%x</td>
 * <td>Preferred date representation based on locale, without the time</td>
 * <td>Example: 02/05/09 for February 5, 2009</td>
 * </tr>
 * <tr valign="top">
 * Miscellaneous</td>
 * <td>---</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>%n</td>
 * <td>A newline character ("\n")</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>%t</td>
 * <td>A Tab character ("\t")</td>
 * <td>---</td>
 * </tr>
 * <tr valign="top">
 * <td>%%</td>
 * <td>A literal percentage character ("%")</td>
 * <td>---</td>
 * </tr>
 * </table>
 * </p>
 * <p>
 * Maximum length of this parameter is 1023 characters.
 * </p>
 * Contrary to ISO-9899:1999, Sun Solaris starts with Sunday as 1.
 * As a result, %u may not function as described in this manual.
 * Windows only: The %e modifier
 * is not supported in the Windows implementation of this function. To achieve
 * this value, the %#d modifier can be used instead. The
 * example below illustrates how to write a cross platform compatible function.
 * Mac OS X only: The %P modifier
 * is not supported in the Mac OS X implementation of this function.
 * @param int $timestamp [optional]
 * @return string a string formatted according <i>format</i>
 * using the given <i>timestamp</i> or the current
 * local time if no timestamp is given. Month and weekday names and
 * other language-dependent strings respect the current locale set
 * with <b>setlocale</b>.
 * @jms-builtin
 */
function strftime ($format, $timestamp = 'time()') {}

/**
 * (PHP 4, PHP 5)<br/>
 * Format a GMT/UTC time/date according to locale settings
 * @link http://php.net/manual/en/function.gmstrftime.php
 * @param string $format <p>
 * See description in <b>strftime</b>.
 * </p>
 * @param int $timestamp [optional]
 * @return string a string formatted according to the given format string
 * using the given <i>timestamp</i> or the current
 * local time if no timestamp is given. Month and weekday names and
 * other language dependent strings respect the current locale set
 * with <b>setlocale</b>.
 * @jms-builtin
 */
function gmstrftime ($format, $timestamp = 'time()') {}

/**
 * (PHP 4, PHP 5)<br/>
 * Return current Unix timestamp
 * @link http://php.net/manual/en/function.time.php
 * @return int
 * @jms-builtin
 */
function time () {}

/**
 * (PHP 4, PHP 5)<br/>
 * Get the local time
 * @link http://php.net/manual/en/function.localtime.php
 * @param int $timestamp [optional]
 * @param bool $is_associative [optional] <p>
 * If set to <b>FALSE</b> or not supplied then the array is returned as a regular,
 * numerically indexed array. If the argument is set to <b>TRUE</b> then
 * <b>localtime</b> returns an associative array containing
 * all the different elements of the structure returned by the C
 * function call to localtime. The names of the different keys of
 * the associative array are as follows:
 * </p>
 * <p>
 * "tm_sec" - seconds, 0 to 59
 * @return array
 * @jms-builtin
 */
function localtime ($timestamp = 'time()', $is_associative = false) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Get date/time information
 * @link http://php.net/manual/en/function.getdate.php
 * @param int $timestamp [optional]
 * @return array an associative array of information related to
 * the <i>timestamp</i>. Elements from the returned
 * associative array are as follows:
 * </p>
 * <p>
 * <table>
 * Key elements of the returned associative array
 * <tr valign="top">
 * <td>Key</td>
 * <td>Description</td>
 * <td>Example returned values</td>
 * </tr>
 * <tr valign="top">
 * <td>"seconds"</td>
 * <td>Numeric representation of seconds</td>
 * <td>0 to 59</td>
 * </tr>
 * <tr valign="top">
 * <td>"minutes"</td>
 * <td>Numeric representation of minutes</td>
 * <td>0 to 59</td>
 * </tr>
 * <tr valign="top">
 * <td>"hours"</td>
 * <td>Numeric representation of hours</td>
 * <td>0 to 23</td>
 * </tr>
 * <tr valign="top">
 * <td>"mday"</td>
 * <td>Numeric representation of the day of the month</td>
 * <td>1 to 31</td>
 * </tr>
 * <tr valign="top">
 * <td>"wday"</td>
 * <td>Numeric representation of the day of the week</td>
 * <td>0 (for Sunday) through 6 (for Saturday)</td>
 * </tr>
 * <tr valign="top">
 * <td>"mon"</td>
 * <td>Numeric representation of a month</td>
 * <td>1 through 12</td>
 * </tr>
 * <tr valign="top">
 * <td>"year"</td>
 * <td>A full numeric representation of a year, 4 digits</td>
 * <td>Examples: 1999 or 2003</td>
 * </tr>
 * <tr valign="top">
 * <td>"yday"</td>
 * <td>Numeric representation of the day of the year</td>
 * <td>0 through 365</td>
 * </tr>
 * <tr valign="top">
 * <td>"weekday"</td>
 * <td>A full textual representation of the day of the week</td>
 * <td>Sunday through Saturday</td>
 * </tr>
 * <tr valign="top">
 * <td>"month"</td>
 * <td>A full textual representation of a month, such as January or March</td>
 * <td>January through December</td>
 * </tr>
 * <tr valign="top">
 * <td>0</td>
 * <td>
 * Seconds since the Unix Epoch, similar to the values returned by
 * <b>time</b> and used by <b>date</b>.
 * </td>
 * <td>
 * System Dependent, typically -2147483648 through
 * 2147483647.
 * </td>
 * </tr>
 * </table>
 * @jms-builtin
 */
function getdate ($timestamp = 'time()') {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Alias of <b>DateTime::__construct</b>
 * @link http://php.net/manual/en/function.date-create.php
 * @param $time [optional]
 * @param $object [optional]
 * @jms-builtin
 */
function date_create ($time, $object) {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Alias of <b>DateTime::createFromFormat</b>
 * @link http://php.net/manual/en/function.date-create-from-format.php
 * @param $format
 * @param $time
 * @param $object [optional]
 * @jms-builtin
 */
function date_create_from_format ($format, $time, $object) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Returns associative array with detailed info about given date
 * @link http://php.net/manual/en/function.date-parse.php
 * @param string $date <p>
 * Date in format accepted by <b>strtotime</b>.
 * </p>
 * @return array array with information about the parsed date
 * on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function date_parse ($date) {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Get info about given date formatted according to the specified format
 * @link http://php.net/manual/en/function.date-parse-from-format.php
 * @param string $format <p>
 * Format accepted by <b>DateTime::createFromFormat</b>.
 * </p>
 * @param string $date <p>
 * String representing the date.
 * </p>
 * @return array associative array with detailed info about given date.
 * @jms-builtin
 */
function date_parse_from_format ($format, $date) {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Alias of <b>DateTime::getLastErrors</b>
 * @link http://php.net/manual/en/function.date-get-last-errors.php
 * @jms-builtin
 */
function date_get_last_errors () {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Alias of <b>DateTime::format</b>
 * @link http://php.net/manual/en/function.date-format.php
 * @param $object
 * @param $format
 * @jms-builtin
 */
function date_format ($object, $format) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Alias of <b>DateTime::modify</b>
 * @link http://php.net/manual/en/function.date-modify.php
 * @param $object
 * @param $modify
 * @jms-builtin
 */
function date_modify ($object, $modify) {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Alias of <b>DateTime::add</b>
 * @link http://php.net/manual/en/function.date-add.php
 * @param $object
 * @param $interval
 * @jms-builtin
 */
function date_add ($object, $interval) {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Alias of <b>DateTime::sub</b>
 * @link http://php.net/manual/en/function.date-sub.php
 * @param $object
 * @param $interval
 * @jms-builtin
 */
function date_sub ($object, $interval) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Alias of <b>DateTime::getTimezone</b>
 * @link http://php.net/manual/en/function.date-timezone-get.php
 * @param $object
 * @jms-builtin
 */
function date_timezone_get ($object) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Alias of <b>DateTime::setTimezone</b>
 * @link http://php.net/manual/en/function.date-timezone-set.php
 * @param $object
 * @param $timezone
 * @jms-builtin
 */
function date_timezone_set ($object, $timezone) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Alias of <b>DateTime::getOffset</b>
 * @link http://php.net/manual/en/function.date-offset-get.php
 * @param $object
 * @jms-builtin
 */
function date_offset_get ($object) {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Alias of <b>DateTime::diff</b>
 * @link http://php.net/manual/en/function.date-diff.php
 * @param $object
 * @param $object2
 * @param $absolute [optional]
 * @jms-builtin
 */
function date_diff ($object, $object2, $absolute) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Alias of <b>DateTime::setTime</b>
 * @link http://php.net/manual/en/function.date-time-set.php
 * @param $object
 * @param $hour
 * @param $minute
 * @param $second [optional]
 * @jms-builtin
 */
function date_time_set ($object, $hour, $minute, $second) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Alias of <b>DateTime::setDate</b>
 * @link http://php.net/manual/en/function.date-date-set.php
 * @param $object
 * @param $year
 * @param $month
 * @param $day
 * @jms-builtin
 */
function date_date_set ($object, $year, $month, $day) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Alias of <b>DateTime::setISODate</b>
 * @link http://php.net/manual/en/function.date-isodate-set.php
 * @param $object
 * @param $year
 * @param $week
 * @param $day [optional]
 * @jms-builtin
 */
function date_isodate_set ($object, $year, $week, $day) {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Alias of <b>DateTime::setTimestamp</b>
 * @link http://php.net/manual/en/function.date-timestamp-set.php
 * @param $object
 * @param $unixtimestamp
 * @jms-builtin
 */
function date_timestamp_set ($object, $unixtimestamp) {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Alias of <b>DateTime::getTimestamp</b>
 * @link http://php.net/manual/en/function.date-timestamp-get.php
 * @param $object
 * @jms-builtin
 */
function date_timestamp_get ($object) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Alias of <b>DateTimeZone::__construct</b>
 * @link http://php.net/manual/en/function.timezone-open.php
 * @param $timezone
 * @jms-builtin
 */
function timezone_open ($timezone) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Alias of <b>DateTimeZone::getName</b>
 * @link http://php.net/manual/en/function.timezone-name-get.php
 * @param $object
 * @jms-builtin
 */
function timezone_name_get ($object) {}

/**
 * (PHP 5 &gt;= 5.1.3)<br/>
 * Returns the timezone name from abbreviation
 * @link http://php.net/manual/en/function.timezone-name-from-abbr.php
 * @param string $abbr <p>
 * Time zone abbreviation.
 * </p>
 * @param int $gmtOffset [optional] <p>
 * Offset from GMT in seconds. Defaults to -1 which means that first found
 * time zone corresponding to <i>abbr</i> is returned.
 * Otherwise exact offset is searched and only if not found then the first
 * time zone with any offset is returned.
 * </p>
 * @param int $isdst [optional] <p>
 * Daylight saving time indicator. Defaults to -1, which means that
 * whether the time zone has daylight saving or not is not taken into
 * consideration when searching. If this is set to 1, then the
 * <i>gmtOffset</i> is assumed to be an offset with
 * daylight saving in effect; if 0, then <i>gmtOffset</i>
 * is assumed to be an offset without daylight saving in effect. If
 * <i>abbr</i> doesn't exist then the time zone is
 * searched solely by the <i>gmtOffset</i> and
 * <i>isdst</i>.
 * </p>
 * @return string time zone name on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function timezone_name_from_abbr ($abbr, $gmtOffset = -1, $isdst = -1) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Alias of <b>DateTimeZone::getOffset</b>
 * @link http://php.net/manual/en/function.timezone-offset-get.php
 * @param $object
 * @param $datetime
 * @jms-builtin
 */
function timezone_offset_get ($object, $datetime) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Alias of <b>DateTimeZone::getTransitions</b>
 * @link http://php.net/manual/en/function.timezone-transitions-get.php
 * @param $object
 * @param $timestamp_begin [optional]
 * @param $timestamp_end [optional]
 * @jms-builtin
 */
function timezone_transitions_get ($object, $timestamp_begin, $timestamp_end) {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Alias of <b>DateTimeZone::getLocation</b>
 * @link http://php.net/manual/en/function.timezone-location-get.php
 * @param $object
 * @jms-builtin
 */
function timezone_location_get ($object) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Alias of <b>DateTimeZone::listIdentifiers</b>
 * @link http://php.net/manual/en/function.timezone-identifiers-list.php
 * @param $what [optional]
 * @param $country [optional]
 * @jms-builtin
 */
function timezone_identifiers_list ($what, $country) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Alias of <b>DateTimeZone::listAbbreviations</b>
 * @link http://php.net/manual/en/function.timezone-abbreviations-list.php
 * @jms-builtin
 */
function timezone_abbreviations_list () {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Gets the version of the timezonedb
 * @link http://php.net/manual/en/function.timezone-version-get.php
 * @return string a string.
 * @jms-builtin
 */
function timezone_version_get () {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Alias of <b>DateInterval::createFromDateString</b>
 * @link http://php.net/manual/en/function.date-interval-create-from-date-string.php
 * @param $time
 * @jms-builtin
 */
function date_interval_create_from_date_string ($time) {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Alias of <b>DateInterval::format</b>
 * @link http://php.net/manual/en/function.date-interval-format.php
 * @param $object
 * @param $format
 * @jms-builtin
 */
function date_interval_format ($object, $format) {}

/**
 * (PHP 5 &gt;= 5.1.0)<br/>
 * Sets the default timezone used by all date/time functions in a script
 * @link http://php.net/manual/en/function.date-default-timezone-set.php
 * @param string $timezone_identifier <p>
 * The timezone identifier, like UTC or
 * Europe/Lisbon. The list of valid identifiers is
 * available in the .
 * </p>
 * @return bool This function returns <b>FALSE</b> if the
 * <i>timezone_identifier</i> isn't valid, or <b>TRUE</b>
 * otherwise.
 * @jms-builtin
 */
function date_default_timezone_set ($timezone_identifier) {}

/**
 * (PHP 5 &gt;= 5.1.0)<br/>
 * Gets the default timezone used by all date/time functions in a script
 * @link http://php.net/manual/en/function.date-default-timezone-get.php
 * @return string a string.
 * @jms-builtin
 */
function date_default_timezone_get () {}

/**
 * (PHP 5)<br/>
 * Returns time of sunrise for a given day and location
 * @link http://php.net/manual/en/function.date-sunrise.php
 * @param int $timestamp <p>
 * The <i>timestamp</i> of the day from which the sunrise
 * time is taken.
 * </p>
 * @param int $format [optional] <p>
 * <table>
 * <i>format</i> constants
 * <tr valign="top">
 * <td>constant</td>
 * <td>description</td>
 * <td>example</td>
 * </tr>
 * <tr valign="top">
 * <td>SUNFUNCS_RET_STRING</td>
 * <td>returns the result as string</td>
 * <td>16:46</td>
 * </tr>
 * <tr valign="top">
 * <td>SUNFUNCS_RET_DOUBLE</td>
 * <td>returns the result as float</td>
 * <td>16.78243132</td>
 * </tr>
 * <tr valign="top">
 * <td>SUNFUNCS_RET_TIMESTAMP</td>
 * <td>returns the result as integer (timestamp)</td>
 * <td>1095034606</td>
 * </tr>
 * </table>
 * </p>
 * @param float $latitude [optional] <p>
 * Defaults to North, pass in a negative value for South.
 * See also: date.default_latitude
 * </p>
 * @param float $longitude [optional] <p>
 * Defaults to East, pass in a negative value for West.
 * See also: date.default_longitude
 * </p>
 * @param float $zenith [optional] <p>
 * Default: date.sunrise_zenith
 * </p>
 * @param float $gmt_offset [optional]
 * @return mixed the sunrise time in a specified <i>format</i> on
 * success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function date_sunrise ($timestamp, $format = 'SUNFUNCS_RET_STRING', $latitude = 'ini_get("date.default_latitude")', $longitude = 'ini_get("date.default_longitude")', $zenith = 'ini_get("date.sunrise_zenith")', $gmt_offset = 0) {}

/**
 * (PHP 5)<br/>
 * Returns time of sunset for a given day and location
 * @link http://php.net/manual/en/function.date-sunset.php
 * @param int $timestamp <p>
 * The <i>timestamp</i> of the day from which the sunset
 * time is taken.
 * </p>
 * @param int $format [optional] <p>
 * <table>
 * <i>format</i> constants
 * <tr valign="top">
 * <td>constant</td>
 * <td>description</td>
 * <td>example</td>
 * </tr>
 * <tr valign="top">
 * <td>SUNFUNCS_RET_STRING</td>
 * <td>returns the result as string</td>
 * <td>16:46</td>
 * </tr>
 * <tr valign="top">
 * <td>SUNFUNCS_RET_DOUBLE</td>
 * <td>returns the result as float</td>
 * <td>16.78243132</td>
 * </tr>
 * <tr valign="top">
 * <td>SUNFUNCS_RET_TIMESTAMP</td>
 * <td>returns the result as integer (timestamp)</td>
 * <td>1095034606</td>
 * </tr>
 * </table>
 * </p>
 * @param float $latitude [optional] <p>
 * Defaults to North, pass in a negative value for South.
 * See also: date.default_latitude
 * </p>
 * @param float $longitude [optional] <p>
 * Defaults to East, pass in a negative value for West.
 * See also: date.default_longitude
 * </p>
 * @param float $zenith [optional] <p>
 * Default: date.sunset_zenith
 * </p>
 * @param float $gmt_offset [optional]
 * @return mixed the sunset time in a specified <i>format</i> on
 * success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function date_sunset ($timestamp, $format = 'SUNFUNCS_RET_STRING', $latitude = 'ini_get("date.default_latitude")', $longitude = 'ini_get("date.default_longitude")', $zenith = 'ini_get("date.sunset_zenith")', $gmt_offset = 0) {}

/**
 * (PHP 5 &gt;= 5.1.2)<br/>
 * Returns an array with information about sunset/sunrise and twilight begin/end
 * @link http://php.net/manual/en/function.date-sun-info.php
 * @param int $time <p>
 * Timestamp.
 * </p>
 * @param float $latitude <p>
 * Latitude in degrees.
 * </p>
 * @param float $longitude <p>
 * Longitude in degrees.
 * </p>
 * @return array array on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function date_sun_info ($time, $latitude, $longitude) {}

define ('DATE_ATOM', "Y-m-d\TH:i:sP");
define ('DATE_COOKIE', "l, d-M-y H:i:s T");
define ('DATE_ISO8601', "Y-m-d\TH:i:sO");
define ('DATE_RFC822', "D, d M y H:i:s O");
define ('DATE_RFC850', "l, d-M-y H:i:s T");
define ('DATE_RFC1036', "D, d M y H:i:s O");
define ('DATE_RFC1123', "D, d M Y H:i:s O");
define ('DATE_RFC2822', "D, d M Y H:i:s O");
define ('DATE_RFC3339', "Y-m-d\TH:i:sP");
define ('DATE_RSS', "D, d M Y H:i:s O");
define ('DATE_W3C', "Y-m-d\TH:i:sP");

/**
 * Timestamp
 * @link http://php.net/manual/en/datetime.constants.php
 */
define ('SUNFUNCS_RET_TIMESTAMP', 0);

/**
 * Hours:minutes (example: 08:02)
 * @link http://php.net/manual/en/datetime.constants.php
 */
define ('SUNFUNCS_RET_STRING', 1);

/**
 * Hours as floating point number (example 8.75)
 * @link http://php.net/manual/en/datetime.constants.php
 */
define ('SUNFUNCS_RET_DOUBLE', 2);

// End of date v.5.4.3-4~precise+1
?>
