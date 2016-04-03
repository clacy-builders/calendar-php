<?php
namespace ML_Express\Calendar\Tests;

require_once __DIR__ . '/../allIncl.php';

use ML_Express\Calendar\DateTime;

class DateTimeTest extends \PHPUnit_Framework_TestCase
{
	public function testAddDays()
	{
		$actual = DateTime::create(1, 1, 2016)->addDays(-1)->addDays(2);
		$expected = DateTime::create(2, 1, 2016);
		$this->assertEquals($expected, $actual);
	}

	public function testAddMonths()
	{
		$actual = DateTime::create(31, 1, 2016)->addMonths(1)->addMonths(-2);
		$expected = DateTime::create(2, 1, 2016);
		$this->assertEquals($expected, $actual);
	}

	public function testAddYears()
	{
		$actual = DateTime::create(29, 2, 2016)->addYears(1)->addYears(-2);
		$expected = DateTime::create(1, 3, 2015);
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @dataProvider workdayProvider
	 */
	public function testWorkday($date, $expected)
	{
		$actual = DateTime::create($date)->workday();
		$expected = DateTime::create($expected);
		$this->assertEquals($expected, $actual);
	}

	public function workdayProvider()
	{
		return [['2016-03-04', '2016-03-04'],
				['2016-03-05', '2016-03-04'],
				['2016-03-06', '2016-03-07'],
				['2016-03-07', '2016-03-07']];
	}

	/**
	 * @dataProvider localizedProvider
	 */
	public function testLocalized($format, $expected)
	{
		$actual = DateTime::create(1, 4, 2016)->localized($format);
		$this->assertSame($expected, $actual);
	}

	public function localizedProvider()
	{
		return [['%a', 'Fri'],
				['%A', 'Friday'],
				['%d', '01'],
				['%#d', '1'],
				['%b', 'Apr'],
				['%B', 'April'],
				['%m', '04'],
				['%#m', '4'],
				['%Y', '2016'],
				['%y', '16']];
	}

	public function testCopy()
	{
		$this->assertEquals(DateTime::create(29, 3, 2016)->copy(), new DateTime('2016-03-29'));
	}

	/**
	 * @dataProvider createProvider
	 */
	public function testCreate($day, $month, $year, $expected)
	{
		$actual = DateTime::create($day, $month, $year);
		$this->assertEquals($expected, $actual);
	}

	public function createProvider()
	{
		return [[null, null, null, new DateTime(date('Y-m-d'))],
				[1, null, null, new DateTime(date('Y-m-01'))],
				[1, 2, null, new DateTime(date('Y-02-01'))],
				[2, 3, 2016, new DateTime('2016-03-02')],
				['2016-01-02', null, null, new DateTime('2016-01-02')],
				[new \DateTime('2016-01-02'), null, null, new DateTime('2016-01-02')],
				[new DateTime('2016-01-02'), null, null, new DateTime('2016-01-02')]
		];
	}

	/**
	 * @dataProvider easterProvider
	 */
	public function testEaster($year, $expected)
	{
		$actual = DateTime::easter($year);
		$this->assertEquals($expected, $actual);
	}

	public function easterProvider()
	{
		return [[2014, DateTime::create(20, 4, 2014)],
				[2015, DateTime::create(05, 4, 2015)],
				[2016, DateTime::create(27, 3, 2016)],
				[2017, DateTime::create(16, 4, 2017)]];
	}
}