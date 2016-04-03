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
use \ML_Express\Calendar\DateTime;

setlocale(LC_TIME, 'de');
$easter = DateTime::easter(2016);
$calendar = Calendar::month(5, 2016)
        ->setMonthFormat('%b %Y')
        ->setFirstWeekday('DE')
        ->addEntry('2016-05-01', 'Tag der Arbeit')
        ->addEntry($easter->copy()->addDays(39), 'Christi Himmelfahrt')
        ->addEntry($easter->copy()->addDays(50), 'Pfingstmontag')
        ->addEntry($easter->copy()->addDays(60), 'Fronleichnam');
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


### The `DateTime` class

```php
require_once 'vendor/autoload.php';
use \ML_Express\Calendar\DateTime;
```


#### Constructor

`DateTime` extends the standard PHP class
DateTime.

Assuming that current date is
Sun, 03 Apr 2016 15:00:49 +0200:
```php
$days[] = new DateTime('2016-03-29');
$days[] = new DateTime();
$days[] = new DateTime('first day of next month');
```

The result:

```
Tue, 29 Mar 2016 00:00:00 +0200
Sun, 03 Apr 2016 15:00:49 +0200
Sun, 01 May 2016 15:00:49 +0200
```


#### Factory Methods
Assuming that current date is
Sun, 03 Apr 2016 15:00:49 +0200:
```php
$days[] = DateTime::create(29, 3, 2016);
$days[] = DateTime::create(29, 3);
$days[] = DateTime::create(29);
$days[] = DateTime::create();
$days[] = DateTime::create('2016-05-01');
$days[] = DateTime::create('last day of previous month');
$days[] = DateTime::easter(2016);
```

The result:

```
Tue, 29 Mar 2016 00:00:00 +0200
Tue, 29 Mar 2016 00:00:00 +0200
Fri, 29 Apr 2016 00:00:00 +0200
Sun, 03 Apr 2016 00:00:00 +0200
Sun, 01 May 2016 00:00:00 +0200
Thu, 31 Mar 2016 15:00:49 +0200
Sun, 27 Mar 2016 00:00:00 +0100
```


#### Modify Dates
```php
$days[] = DateTime::create('2016-03-29')->addYears(2);
$days[] = DateTime::create('2016-03-29')->addMonths(-2);
$days[] = DateTime::create('2016-03-29')->addDays(3);
$days[] = DateTime::create('2016-04-01')->workday();
$days[] = DateTime::create('2016-04-02')->workday();
$days[] = DateTime::create('2016-04-03')->workday();
$days[] = DateTime::create('2016-04-04')->workday();
```

The result:

```
Thu, 29 Mar 2018 00:00:00 +0200
Fri, 29 Jan 2016 00:00:00 +0100
Fri, 01 Apr 2016 00:00:00 +0200
Fri, 01 Apr 2016 00:00:00 +0200
Fri, 01 Apr 2016 00:00:00 +0200
Mon, 04 Apr 2016 00:00:00 +0200
Mon, 04 Apr 2016 00:00:00 +0200
```


#### Clone with the `copy` method
```php
$easter = DateTime::easter(2016);
$pentecost = $easter->copy()->addDays(49);
```

The result:

```
Sun, 27 Mar 2016 00:00:00 +0100
Sun, 15 May 2016 00:00:00 +0200
```


#### The `localized` method
This method returns a string representation according to locale settings.
http://php.net/manual/en/function.strftime.php lists the specifiers you can use
in the format string.
```php
setlocale(LC_TIME, 'de');
$date = DateTime::create('2016-06-05');
print $date->localized('%A, %#d. %B %Y');
```

The result:

```
Sonntag, 5. Juni 2016
```