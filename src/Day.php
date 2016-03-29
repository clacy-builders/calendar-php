<?php
namespace ML_Express\Calendar;

class Day extends \DateTime
{
	public $title;
	public $link;

	/**
	 * Sets the Title.
	 *
	 * @param  string  $title
	 * @return Day
	 */
	public function setTitle($title)
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * Sets the Link.
	 *
	 * @param  string  $url
	 * @return Day
	 */
	public function setLink($url) {
		$this->link = $url;
		return $this;
	}

	/**
	 *
	 * @param  int  $years
	 * @return Day
	 */
	public function addYears($years)
	{
		return $this->modify(\sprintf('%+d', $years) . ' year');
	}

	/**
	 *
	 * @param  int  $months
	 * @return Day
	 */
	public function addMonths($months)
	{
		return $this->modify(\sprintf('%+d', $months) . ' month');
	}

	/**
	 *
	 * @param  int  $days
	 * @return Day
	 */
	public function addDays($days)
	{
		return $this->modify(\sprintf('%+d', $days) . ' day');
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
	public function formatLoc($format, $encoding = 'UTF-8')
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
	 * @return Day
	 */
	public function copy()
	{
		return clone $this;
	}

	/**
	 *
	 * @param  int|string  $day
	 * @param  int         $month
	 * @param  int         $year
	 * @return \ML_Express\Calendar\Day
	 */
	public static function create($day = null, $month = null, $year = null)
	{
		if (\is_string($day)) {
			return new Day($day);
		}
		$year = $year === null ? date('Y') : $year;
		$month = $month === null ? date('m') : $month;
		$day = $day === null ? date('d') : $day;
		return new Day("$year-$month-$day");
	}

	/**
	 * Creates a <code>Day</code> object for the easter date.
	 *
	 * @link http://php.net/manual/en/function.easter-days.php
	 *
	 * @param  int  $year  For example 2016
	 * @return \ML_Express\Calendar\Day
	 */
	public static function easter($year)
	{
		return Day::create(21, 3, $year)->addDays(easter_days($year));
	}
}