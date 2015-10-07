# Grocery Site Scraper

Console application that scrapes the grocery site in order to obtain a JSON array containing informations of all the products in the page.

The application has been developed and tested with:

* PHP 5.5.14
* PHPUnit 3.7.29
* Composer

## How to run the application

### Dependencies loading

`composer install`

### Run

`php application.php scrapeGrocerySite`

## How to run tests

`phpunit --bootstrap vendor/autoload.php tests`
