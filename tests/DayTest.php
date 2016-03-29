<?php
namespace ML_Express\Calendar\Tests;

require_once __DIR__ . '/../allIncl.php';

use ML_Express\Calendar\Day;

class DayTest extends \PHPUnit_Framework_TestCase
{
	public function testSetTitle()
	{
		$actual = Day::create(1, 1, 2016)->setTitle('Packagist');
		$this->assertEquals('Packagist', $actual->title);
	}

	public function testSetLink()
	{
		$actual = Day::create(1, 1, 2016)->setLink('https://packagist.org/');
		$this->assertEquals('https://packagist.org/', $actual->link);
	}

	public function testAddDays()
	{
		$actual = Day::create(1, 1, 2016)->addDays(-1)->addDays(2);
		$expected = Day::create(2, 1, 2016);
		$this->assertEquals($expected, $actual);
	}

	public function testAddMonths()
	{
		$actual = Day::create(31, 1, 2016)->addMonths(1)->addMonths(-2);
		$expected = Day::create(2, 1, 2016);
		$this->assertEquals($expected, $actual);
	}

	public function testAddYears()
	{
		$actual = Day::create(29, 2, 2016)->addYears(1)->addYears(-2);
		$expected = Day::create(1, 3, 2015);
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @dataProvider formatLocProvider
	 */
	public function testFormatLoc($format, $expected)
	{
		$actual = Day::create(1, 4, 2016)->formatLoc($format);
		$this->assertSame($expected, $actual);
	}

	public function formatLocProvider()
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
		$this->assertEquals(Day::create(29, 3, 2016)->copy(), new Day('2016-03-29'));
	}

	/**
	 * @dataProvider createProvider
	 */
	public function testCreate($day, $month, $year, $expected)
	{
		$actual = Day::create($day, $month, $year);
		$this->assertEquals($expected, $actual);
	}

	public function createProvider()
	{
		return [[null, null, null, new Day(date('Y-m-d'))],
				[1, null, null, new Day(date('Y-m-01'))],
				[1, 2, null, new Day(date('Y-02-01'))],
				[2, 3, 2016, new Day('2016-03-02')],
				['2016-01-02', null, null, new Day('2016-01-02')]
		];
	}

	/**
	 * @dataProvider easterProvider
	 */
	public function testEaster($year, $expected)
	{
		$actual = Day::easter($year);
		$this->assertEquals($expected, $actual);
	}

	public function easterProvider()
	{
		return [[2014, Day::create(20, 4, 2014)],
				[2015, Day::create(05, 4, 2015)],
				[2016, Day::create(27, 3, 2016)],
				[2017, Day::create(16, 4, 2017)]];
	}
}