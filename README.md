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

### The `Day` class

#### Constructor

`Day` extends the standard PHP class `DateTime`:

```php
use \ML_Express\Calendar\Day;

$day = new Day('2016-03-29');
$day = new Day();             // today
```


#### Factory Methods
Assuming, current date is 2016-03-29
```php
$day = Day::FirstOfThisYear();    // 2016-01-01
$day = Day::FirstOfThisYear(1);   // 2017-01-01
$day = Day::FirstOfThisMonth();   // 2016-03-01
$day = Day::FirstOfThisMonth(-1); // 2016-02-01
$day = Day::easter(2016);         // 2016-03-27
```


#### Modify Dates
```php
$day = (new Day('2016-03-29'))->addYears(2);   // 2018-03-29
$day = (new Day('2016-03-29'))->addMonths(-2); // 2016-01-29
$day = (new Day('2016-03-29'))->addDays(3);    // 2018-04-01
```


#### Clone with the `copy` method
```php
$easter = Day::easter(2016);
$pentecost = $easter->copy()->addDays(49);
```


#### Set title and link
```php
$day = (new Day('2015-12-03'))
        ->setTitle('PHP 7.0 released')
        ->setLink('http://php.net/manual/en/migration70.new-features.php');
```


### The `Calendar` class
See https://github.com/ml-express/html5-express-php/wiki/Calendar