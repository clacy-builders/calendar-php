<?php
namespace ML_Express\Calendar\Tests;

require_once __DIR__ . '/../allIncl.php';

use ML_Express\Calendar\Day;

class DayTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider firstOfThisYearProvider
	 */
	public function testFirstOfThisYear($delta, $expected)
	{
		$actual = Day::FirstOfThisYear($delta);
		$this->assertEquals($expected, $actual);
	}

	public function firstOfThisYearProvider()
	{
		$year = date('Y');
		return array(
			[0, new Day($year . '-01-01')],
			[1, new Day((1 + $year) . '-01-01')],
			[2, new Day((2 + $year) . '-01-01')],
			[-1, new Day((-1 + $year) . '-01-01')],
			[-2, new Day((-2 + $year) . '-01-01')]
		);
	}

	/**
	 * @dataProvider firstOfThisMonthProvider
	 */
	public function testFirstOfThisMonth($delta, $expected)
	{
		$actual = Day::FirstOfThisMonth($delta);
		$this->assertEquals($expected, $actual);
	}

	public function firstOfThisMonthProvider()
	{
		return array(
			[0, new Day(date('Y-m-01'))],
			[1, (new Day(date('Y-m-01')))->modify('+1 month')],
			[2, (new Day(date('Y-m-01')))->modify('+2 month')],
			[-1, (new Day(date('Y-m-01')))->modify('-1 month')],
			[-2, (new Day(date('Y-m-01')))->modify('-2 month')]
		);
	}

	/**
	 * @dataProvider createDayListProvider
	 */
	public function xtestCreateDayList($state, $expected)
	{
		$defs = '[
			{"type": "fixed", "date": "04-01", "title": "FooDay", "states": ["foo"], "regional": ["foo"]},
			{"type": "fixed", "date": "04-01", "title": "BarDay", "states": ["bar"], "regional": ["bar"]},
			{"type": "fixed", "date": "04-01", "title": "BazDay", "states": ["baz"], "regional": ["baz"]},
			{"type": "modified", "date": "04-01", "modify": "next sunday", "title": "PHP Day", "states": ["foo", "bar"]},
			{"type": "easter-dependent", "delta": -2, "title": "PHP Day"}
		]';
		$actual = Day::createDayList(\json_decode($defs, true), 2016, $state);
		$this->assertEquals($expected, $actual);
	}

	public function createDayListProvider()
	{
		return array(
			array(
				null, array(
					'2016-04-01' => array(
						(new Day('2016-04-01'))->setTitle('FooDay')->setStates(['foo'], ['foo']),
						(new Day('2016-04-01'))->setTitle('BarDay')->setStates(['bar'], ['bar']),
						(new Day('2016-04-01'))->setTitle('BazDay')->setStates(['baz'], ['baz'])
					),
					'2016-04-03' => (new Day('2016-04-03'))->setTitle('PHP Day')->setStates(['foo', 'bar']),
					'2016-03-25' => (new Day('2016-03-25'))->setTitle('PHP Day')
				)
			),
			array(
				'foo', array(
					'2016-04-01' => (new Day('2016-04-01'))->setTitle('FooDay')->setStates(['foo'], ['foo']),
					'2016-04-03' => (new Day('2016-04-03'))->setTitle('PHP Day')->setStates(['foo', 'bar']),
					'2016-03-25' => (new Day('2016-03-25'))->setTitle('PHP Day')
				)
			),
			array(
				'whatever', array(
					'2016-03-25' => (new Day('2016-03-25'))->setTitle('PHP Day')
				)
			)
		);
	}
}