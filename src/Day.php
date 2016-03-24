<?php
namespace ML_Express\Calendar;

class Day extends \DateTime
{
	public $title;
	public $link;
	public $states = [];
	public $regional = [];

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
	 */
	public function setLink($url) {
		$this->link = $url;
		return $this;
	}

	/**
	 *
	 * @param  string[]  $states
	 * @param  string[]  $regional
	 * @return Day
	 */
	public function setStates($states = [], $regional = [])
	{
		$this->states = $states;
		$this->regional = $regional;
		return $this;
	}

	/**
	 *
	 * @param  string  $state
	 * @return boolean
	 */
	public function inState($state)
	{
		return empty($state) || empty($this->states) || \in_array($state, $this->states);
	}

	/**
	 *
	 * @param  string  $state
	 * @return boolean
	 */
	public function isRegional($state)
	{
		return \in_array($state, $this->regional);
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
	 * @param  string     $format    A format string containing specifiers like <code>%a</code>,
	 *                               <code>%B</code> etc.
	 * @param  string     $encoding  For example 'UTF-8', 'ISO-8859-1'.
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
	 * @param  int  $delta
	 * @return Day
	 */
	public static function FirstOfThisYear($delta = 0)
	{
		return (new Day(date('Y-01-01')))->addYears($delta);
	}

	public static function FirstOfThisMonth($delta = 0)
	{
		return (new Day(date('Y-m-01')))->addMonths($delta);
	}

	/**
	 * Creates a <code>Day</code> object for the easter date.
	 *
	 * @link http://php.net/manual/en/function.easter-days.php
	 *
	 * @param  int    $year    For example 2016
	 * @return \ML_Express\Calendar\Day
	 */
	public static function easter($year)
	{
		return (new Day("$year-03-21"))->add(new \DateInterval('P' . easter_days($year) . 'D'));
	}
}