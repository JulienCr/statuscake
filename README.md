#StatusCake API wrapper

StatusCake is a monitoring service that monitors website/servers over HTTP/TCP/PING.

This project wraps most endpoints StatusCake has to offer.

## Install

Via Composer

``` bash
composer require JulienCr/statuscake
```

## Usage
Setting up the client:

``` php
$statusCake = new StatusCake\Client(`USER', 'TOKEN');
```

Checking if the credentials are valid:

``` php
$statusCake->validAccount(); // return true or false
```

Getting [basic account info](https://www.statuscake.com/api/The%20Basics/Confirm%20Authentication.md#render):

``` php
$statusCake->account();
```

### Tests

These are actually your monitors, each test would represent a website you're monitoring.

Each returned test will be parsed into a class `StatusCake\Test`, all the properties are documented in there, be sure to check it out.

#### Creating a test

To create a test you need to initialise `StatusCake\Test`. There are just a few properties that are actually required; websiteName, websiteURL, testType.

``` php
$test = new StatusCake\Test();

// Required parameters
$test->websiteName = 'HappyDemon';
$test->websiteURL = 'http://happydemon.xyz';
$test->testType = StatusCake\Test::TYPE_HTTP;

// I'm a little impatient, so I'd like more frequest checks (every 2 minutes)
$test->checkRate = 120;

// Save everything
$statusCake->updateTest($test);
```

#### Updating a test
Updating is done in the same way:

``` php
// I've changed my mind, let's check every 5 minutes
$test->checkRate = 300;

// Save the change
$statusCake->updateTest($test);
```

#### Deleting a test

``` php
$statusCake->deleteTest($test);
```

#### Retrieving tests

Retrieving a list of existing tests is simple:

``` php
$tests = $statusCake->getTests();
```

#### Retrieving a test's period

A [period](https://www.statuscake.com/api/Period%20Data/Get%20Period%20Data.md#render) of data is two time stamps in which status has remained the same.

``` php
$periods = $test->getPeriods();
```

## Retrieving a test's performance data

Retrieves a list of [checks](https://www.statuscake.com/api/Performance%20Data/Get%20All%20Data.md#render) performed for the current site (this statusCake users with a premium account).  

``` php
$periods = $test->getPerformance();
```

#### Retrieve a test's previously sent alerts

Retrieves a list of [alerts](https://www.statuscake.com/api/Alerts/Get%20Sent%20Alerts.md#render) that have, previously, been sent.

``` php
$periods = $test->getAlerts();
```

### Contact Groups

One of the properties tests could need assigned is contact groups. You can retrieve and manage them through the API as wel.

The same principles as Tests are applied here, meaning every contact group will be loaded in a `StatusCake\ContactGroup` class (check the class definition for more info on the properties).  

A few helper functions are included to work with email addresses.

#### Creating a contact group

``` php
$contactGroup = new StatusCake\ContactGroup();

// The only required parameter
$contactGroup->groupName = 'personal mail';

// let's add an email to get notifications
$contactGroup->addEmail('maxim.kerstens@gmail.com');

// Save everything
$statusCake->updateContactGroup($contactGroup);
```

#### Updating a contact group

``` php
// I've changed my mind, different email
$contactGroup->removeEmail('maxim.kerstens@gmail.com');
$contactGroup->addEmail('maxim@happydemon.xyz');

// Which is the same as doing
$contactGroup->email = ['maxim@happydemon.xyz']

// Save everything
$statusCake->updateContactGroup($contactGroup);
```


#### Deleting a contact group

``` php
$statusCake->deleteContactGroup($contactGroup);
```
