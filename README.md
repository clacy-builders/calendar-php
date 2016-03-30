# ml-express/calendar

## Installation

This library requires PHP 5.4 or newer.

Add the following to your project's `composer.json` file:
```json
{
    "minimum-stability": "dev",
    "require": {
        "ml-express/calendar": "dev-master@dev"
    }
}
```


Run `composer install` or `composer update`.

## Basic Usage

### The `Calendar` class

```php
<?php
require_once 'vendor/autoload.php';

use \ML_Express\Calendar\Calendar;
use \ML_Express\Calendar\Day;

setlocale(LC_TIME, 'de');
$easter = Day::easter(2016);
$calendar = Calendar::month(5, 2016)
        ->setMonthFormat('%b %Y')
        ->setFirstWeekday('DE')
        ->addEntries([
                Day::create(1, 5, 2016)->setTitle('Tag der Arbeit'),
                $easter->copy()->addDays(39)->setTitle('Christi Himmelfahrt'),
                $easter->copy()->addDays(50)->setTitle('Pfingstmontag'),
                $easter->copy()->addDays(60)->setTitle('Fronleichnam')]);
print json_encode($calendar->buildArray(), JSON_PRETTY_PRINT);

```

The generated JSON text:

```json
{
    "weekdays": {
        "mon": "Mo",
        "tue": "Di",
        "wed": "Mi",
        "thu": "Do",
        "fri": "Fr",
        "sat": "Sa",
        "sun": "So"
    },
    "years": [
        {
            "time": "2016",
            "label": "2016",
            "months": [
                {
                    "time": "2016-05",
                    "label": "Mai 2016",
                    "month": "05",
                    "weeks": [
                        {
                            "time": "2016-W17",
                            "label": "17",
                            "leading": 6,
                            "days": [
                                {
                                    "time": "2016-05-01",
                                    "label": "1",
                                    "weekday": "sun",
                                    "entries": [
                                        {
                                            "class": "holiday",
                                            "title": "Tag der Arbeit"
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "time": "2016-W18",
                            "label": "18",
                            "days": [
                                {
                                    "time": "2016-05-02",
                                    "label": "2",
                                    "weekday": "mon"
                                },
                                {
                                    "time": "2016-05-03",
                                    "label": "3",
                                    "weekday": "tue"
                                },
                                {
                                    "time": "2016-05-04",
                                    "label": "4",
                                    "weekday": "wed"
                                },
                                {
                                    "time": "2016-05-05",
                                    "label": "5",
                                    "weekday": "thu",
                                    "entries": [
                                        {
                                            "class": "holiday",
                                            "title": "Christi Himmelfahrt"
                                        }
                                    ]
                                },
                                {
                                    "time": "2016-05-06",
                                    "label": "6",
                                    "weekday": "fri"
                                },
                                {
                                    "time": "2016-05-07",
                                    "label": "7",
                                    "weekday": "sat"
                                },
                                {
                                    "time": "2016-05-08",

…

```

See also: https://github.com/ml-express/html5-express-php/wiki/Calendar


### The `Day` class

```php
require_once 'vendor/autoload.php';
use \ML_Express\Calendar\Day;
```


#### Constructor

`Day` extends the standard PHP class `DateTime`.
Assuming that current date is 2016-03-29:
```php
$days[] = new Day('2016-03-29');
$days[] = new Day();
$days[] = new Day('first day of next month');
```

The result:

```
2016-03-29
2016-03-30
2016-04-01
```


#### Factory Methods
Assuming that current date is 2016-03-29:
```php
$days[] = Day::create(29, 3, 2016);
$days[] = Day::create(29, 3);
$days[] = Day::create(29);
$days[] = Day::create();
$days[] = Day::create('2016-05-01');
$days[] = Day::create('last day of previous month');
$days[] = Day::easter(2016);
```

The result:

```
2016-03-29
2016-03-29
2016-03-29
2016-03-30
2016-05-01
2016-02-29
2016-03-27
```


#### Modify Dates
```php
$days[] = Day::create('2016-03-29')->addYears(2);
$days[] = Day::create('2016-03-29')->addMonths(-2);
$days[] = Day::create('2016-03-29')->addDays(3);
$days[] = Day::create('2016-03-26')->workday();
```

The result:

```
2018-03-29
2016-01-29
2016-04-01
2016-03-25
```


#### Clone with the `copy` method
```php
$easter = Day::easter(2016);
$pentecost = $easter->copy()->addDays(49);
```

The result:

```
2016-03-27
2016-05-15
```


#### Set title and link
```php
$day = Day::create('2015-12-03')
        ->setTitle('PHP 7.0 released')
        ->setLink('http://php.net/manual/en/migration70.new-features.php');
```

The result:

```
2015-12-03    PHP 7.0 released
              http://php.net/manual/en/migration70.new-features.php
```


#### The `formatLoc` method
```php
setlocale(LC_TIME, 'de');
$date = Day::create('2016-06-05');
print $date->formatLoc('%A, %#d. %B %Y');
```

The result:

```
Sonntag, 5. Juni 2016
```


