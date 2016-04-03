<?php
namespace ML_Express\Calendar;

class DateTime extends \DateTime
{
	/**
	 *
	 * @param  int  $years
	 * @return DateTime
	 */
	public function addYears($years)
	{
		return $this->modify(\sprintf('%+d', $years) . ' year');
	}

	/**
	 *
	 * @param  int  $months
	 * @return DateTime
	 */
	public function addMonths($months)
	{
		return $this->modify(\sprintf('%+d', $months) . ' month');
	}

	/**
	 *
	 * @param  int  $days
	 * @return DateTime
	 */
	public function addDays($days)
	{
		return $this->modify(\sprintf('%+d', $days) . ' day');
	}

	/**
	 * Returns the nearest workday.
	 *
	 * @return DateTime
	 */
	public function workday()
	{
		$weekday = $this->format('N');
		if ($weekday == 7) return $this->addDays(1);
		if ($weekday == 6) return $this->addDays(-1);
		return $this;
	}

	/**
	 * Returns a string representation according to locale settings.
	 *
	 * @link http://php.net/manual/en/function.strftime.php
	 * @link http://php.net/manual/en/class.datetime.php
	 *
	 * @param  string  $format    A format string containing specifiers like <code>%a</code>,
	 *                            <code>%B</code> etc.
	 * @param  string  $encoding  For example 'UTF-8', 'ISO-8859-1'.
	 * @return string
	 */
	public function localized($format, $encoding = 'UTF-8')
	{
		$str = strftime($format, $this->getTimestamp());
		if ($encoding == 'UTF-8') {
			$str = utf8_encode($str);
		}
		return $str;
	}

	/**
	 * Returns a clone.
	 *
	 * @return DateTime
	 */
	public function copy()
	{
		return clone $this;
	}

	/**
	 *
	 * @param  mixed  $day
	 * @param  int    $month
	 * @param  int    $year
	 * @return DateTime
	 */
	public static function create($day = null, $month = null, $year = null)
	{
		if (\is_string($day)) {
			return new DateTime($day);
		}
		if ($day instanceof \DateTime) {
			return new DateTime($day->format('Y-m-d'));
		}
		$year = $year === null ? date('Y') : $year;
		$month = $month === null ? date('m') : $month;
		$day = $day === null ? date('d') : $day;
		return new DateTime("$year-$month-$day");
	}

	/**
	 * Creates a <code>DateTime</code> object for the easter date.
	 *
	 * @link http://php.net/manual/en/function.easter-days.php
	 *
	 * @param  int  $year  For example 2016
	 * @return DateTime
	 */
	public static function easter($year)
	{
		return DateTime::create(21, 3, $year)->addDays(easter_days($year));
	}
}