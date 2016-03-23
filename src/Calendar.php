<?php
namespace ML_Express\Calendar;

use ML_Express\Calendar\Day;

class Calendar
{
	const CHARACTER_ENCODING = 'UTF-8';

	protected $from, $till;
	protected $firstWeekday;
	protected $dayFormat;
	protected $monthFormat;
	protected $yearFormat;
	protected $weekdayFormat;

	protected $weekdays;
	protected $buildWeekdays;

	/**
	 * The constructor.
	 *
	 * You may use the static <code>span</code> method instead.
	 *
	 * @param  Day|string  $from  First day which should be shown on the calendar.<br>
	 *                            A string of the format <code>Y-m-d</code> or a
	 *                            <code>DateTime</code> object.
	 * @param  Day|string  $till  Day after the last day which should be shown on the calendar.
	 */
	public function __construct($from, $till)
	{
		$this->from = \is_string($from) ? new Day($from) : $from;
		$this->till = \is_string($till) ? new Day($till) : $till;
		$this->setDayFormat()->setMonthFormat()->setYearFormat()->setWeekdayFormat();
		$this->setFirstWeekday();
	}

	/**
	 * Returns a <code>Calendar</code> object.
	 *
	 * @param  Day|string  $from  First day which should be shown on the calendar.<br>
	 *                            A string of the format <code>Y-m-d</code> or a
	 *                            <code>DateTime</code> object.
	 * @param  Day|string  $till  Day after the last day which should be shown on the calendar.
	 * @return Calendar
	 */
	public static function span($from, $till)
	{
		return new Calendar($from, $till);
	}

	/**
	 * Returns a <code>Calendar</code> object for the current or a given year.
	 *
	 * @param  int  $year  The current year if omitted.
	 * @return Calendar
	 */
	public static function year($year = null)
	{
		if (\is_null($year)) {
			$year = date('Y');
		}
		$from = new Day("$year-01-01");
		$year++;
		$till = new Day("$year-01-01");
		return new Calendar($from, $till);
	}

	/**
	 * Returns a <code>Calendar</code> object for the current or a given month.
	 *
	 * @param  int  $month  The current month if omitted.
	 * @param  int  $year   The current year if omitted.
	 * @return Calendar
	 */
	public static function month($month = null, $year = null)
	{
		if (\is_null($month)) {
			$month = date('m');
		}
		if (\is_null($year)) {
			$year = date('Y');
		}
		$from = new Day("$year-$month-01");
		$till = $from->copy()->modify('+1 month');
		return new Calendar($from, $till);
	}

	/**
	 * Returns a <code>Calendar</code> object for multiple months.
	 *
	 * @param  int  $delta  -2: The current or given month and the previous two months.<br>
	 *                      +2: The current or given month and the next two months.
	 * @param  int  $month  The current month if omitted.
	 * @param  int  $year   The current year if omitted.
	 * @return Calendar
	 */
	public static function months($delta = 0, $month = null, $year = null)
	{
		$calendar = self::month($month, $year);
		if ($delta > 0) {
			$calendar->till->addMonths($delta);
		}
		else if ($delta < 0) {
			$calendar->from->addMonths($delta);
		}
		return $calendar;
	}

	/**
	 * Sets the format for day labels.
	 *
	 * @param  string  $format  <code>%d</code> or <code>%#d</code>
	 *                          (<code>%e</code> doesn't work on Windows).
	 * @return Calendar
	 */
	public function setDayFormat($format = '%#d')
	{
		$this->dayFormat = $format;
		return $this;
	}

	/**
	 * Sets the format for month labels.
	 *
	 * @param  string  $format  <code>%b</code>, <code>%B</code> or <code>%m</code>,
	 *                          also in combination with <code>%Y</code> or <code>%y</code>.
	 * @return Calendar
	 */
	public function setMonthFormat($format = '%B')
	{
		$this->monthFormat = $format;
		return $this;
	}

	/**
	 * Sets the format for year labels.
	 *
	 * @param  string  $format  <code>%Y</code>, <code>%y</code> or an empty string.
	 * @return Calendar
	 */
	public function setYearFormat($format = '%Y')
	{
		$this->yearFormat = $format;
		return $this;
	}

	/**
	 * Sets the format for weekday labels.
	 *
	 * @param  string  $format  Neither a format string containing <code>%a</code> or
	 *                          <code>%A</code> or an array starting with Monday, for example:
	 *                          <code>['M', 'T', 'W', 'T', 'F', 'S', 'S']</code>.
	 * @return Calendar
	 */
	public function setWeekdayFormat($format = '%a')
	{
		$this->weekdayFormat = $format;
		$this->buildWeekdays = true;
		return $this;
	}

	/**
	 * Sets the first day of the week in a calendar page view.
	 *
	 * @link http://unicode.org/repos/cldr/trunk/common/supplemental/supplementalData.xml
	 *
	 * @param  int|string  $firstWeekday  0 (for Monday) through 6 (for Sunday)
	 *                                    or an ISO 3166 country code for example
	 *                                    <code>BR</code> for Brazil, <code>SE</code> for Sweden.
	 * @return Calendar
	 */
	public function setFirstWeekday($firstWeekday = 0)
	{
		$this->firstWeekday = is_string($firstWeekday)
				? self::firstWeekday($firstWeekday)
				: $firstWeekday;
		$this->buildWeekdays = true;
		return $this;
	}

	/**
	 * The bais to generate output.
	 *
	 * Keys: <code>weekdays</code>, <code>years</code><br>
	 * Keys for <code>years</code> items:
	 * <code>time</code>, <code>label</code>, <code>months</code><br>
	 * Keys for <code>months</code> items:
	 * <code>time</code>, <code>label</code>, <code>weeks</code><br>
	 * Keys for <code>weeks</code> items:
	 * <code>time</code>, <code>label</code>, <code>days</code><br>
	 * Keys for <code>days</code> items:
	 * <code>time</code>, <code>label</code>
	 *
	 * @return array
	 */
	public function buildArray()
	{
		$this->buildWeekdays();
		$array = array(
			'weekdays' => $this->weekdays,
			'years' => []
		);
		$day = $this->from->copy();
		$first = true;
		$yi = $mi = $wi = $di = -1;
		while ($day != $this->till) {
			$iso = $day->format('Y-m-d');
			$wd = ((int) $day->format('N') - $this->firstWeekday + 6) % 7;
			$d = (int) $day->format('d');
			$m = $day->format('m');
			$t = $day->format('t');
			$y = $day->format('Y');
			$w = $day->format('o-\WW');
			// new year?
			if (($d == 1 && $m == 1) || $first) {
				$mi = -1; $wi = -1;
				$array['years'][++$yi] = array(
					'time' => $y,
					'label' => $day->formatLoc($this->yearFormat),
					'months' =>[]
				);
			}
			// new month or first day?
			if ($d == 1 || $first) {
				$wi = -1;
				$array['years'][$yi]['months'][++$mi] = array(
					'time' => "$y-$m",
					'label' => $day->formatLoc($this->monthFormat)
				);
			}
			// new week
			if ($wd == 0 || $d == 1 || $first) {
				$week = [];
				if ($this->firstWeekday == 0) {
					$week['time'] = $w;
					$week['label'] = $day->format('W');
				}
				$array['years'][$yi]['months'][$mi]['weeks'][++$wi] = $week;
			}
			// day
			$array['years'][$yi]['months'][$mi]['weeks'][$wi]['days'][] = array(
				'time' => $iso,
				'label' => $day->formatLoc($this->dayFormat)
			);
			$day->addDays(1);
			$first = false;
		}
		return $array;
	}

	private static function firstWeekday($countryCode)
	{
		$countryCode = strtoupper($countryCode);
		$territories = array(
			array(
				'AD', 'AI', 'AL', 'AM', 'AN', 'AT', 'AX', 'AZ', 'BA', 'BE', 'BG', 'BM',
				'BN', 'BY', 'CH', 'CL', 'CM', 'CR', 'CY', 'CZ', 'DE', 'DK', 'EC', 'EE',
				'ES', 'FI', 'FJ', 'FO', 'FR', 'GB', 'GE', 'GF', 'GP', 'GR', 'HR', 'HU',
				'IS', 'IT', 'KG', 'KZ', 'LB', 'LI', 'LK', 'LT', 'LU', 'LV', 'MC', 'MD',
				'ME', 'MK', 'MN', 'MQ', 'MY', 'NL', 'NO', 'PL', 'PT', 'RE', 'RO', 'RS',
				'RU', 'SE', 'SI', 'SK', 'SM', 'TJ', 'TM', 'TR', 'UA', 'UY', 'UZ', 'VA',
				'VN', 'XK'
			),
			array(), array(), array(), array('BD', 'MV'),
			array(
				'AE', 'AF', 'BH', 'DJ', 'DZ', 'EG', 'IQ', 'IR', 'JO', 'KW', 'LY', 'MA',
				'OM', 'QA', 'SD', 'SY'
			),
			array(
				'AG', 'AR', 'AS', 'AU', 'BR', 'BS', 'BT', 'BW', 'BZ', 'CA', 'CN', 'CO',
				'DM', 'DO', 'ET', 'GT', 'GU', 'HK', 'HN', 'ID', 'IE', 'IL', 'IN', 'JM',
				'JP', 'KE', 'KH', 'KR', 'LA', 'MH', 'MM', 'MO', 'MT', 'MX', 'MZ', 'NI',
				'NP', 'NZ', 'PA', 'PE', 'PH', 'PK', 'PR', 'PY', 'SA', 'SG', 'SV', 'TH',
				'TN', 'TT', 'TW', 'UM', 'US', 'VE', 'VI', 'WS', 'YE', 'ZA', 'ZW'
			)
		);
		foreach ($territories as $i => $territory) {
			if (in_array($countryCode, $territory)) return $i;
		}
		return 0;
	}

	private function buildWeekdays()
	{
		if ($this->buildWeekdays) {
			$this->weekdays = [];
			$day = (new Day('2014-01-06'))->addDays($this->firstWeekday);
			for ($i = 0; $i < 7; $i++) {
				$index = \strtolower($day->format('D'));
				if (\is_array($this->weekdayFormat)) {
					$this->weekdays[$index] = $this->weekdayFormat[($i + $this->firstWeekday) % 7];
				}
				else {
					$this->weekdays[$index] = $day->formatLoc(
							$this->weekdayFormat, self::CHARACTER_ENCODING);
				}
				$day->addDays(1);
			}
			$this->buildWeekdays = false;
		}
		return $this;
	}
}