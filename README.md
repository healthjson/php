Health
######

![Travis CI](https://travis-ci.org/potfur/health-json.svg?branch=master "Travis CI")


Lets say there is a service that needs to be checked periodically if is running.
Simplest solution would be to _ping_ main site and if it responds with `200` everything is fine.

But, in reality service can not connect to database and response is served from outdated cache.
Other case would be when everything works but mailing service is unreachable.
 
And this is where **Health** comes on stage.
**Health** is an implementation of [Health JSON Schema](healthjson.org) which standardises responses structure for monitoring endpoints.


# How to use

Create instance of `Health` where all services requiring monitoring will be registered

```php
use Health\Health;
use Health\Service\CallableService;

$health = new Health(
	'some-app',  // application name
	'healthy-server.com',  // host name
	'1.2.3',  // currently deployed version
	new \DateTime('2016-12-05T12:45:11+00:00')  // deployment date
);

$health->addService(
    'database',  // service group
    new CallableService(
        'postgres', // service name
        function () use ($pdo) { $pdo->exec('SELECT 1'); }, // validating function
        true // true if service is essential
    )
);
```

When all services were registered, `Health` can create status snapshots.
Such snapshot can be used to expose service health as simple _true/false_ endpoint:

```php
$state = $health->state();
$state->isHealthy();  // returns true if all services are working
$state->summary(); // returns array with detailed information about all registered services
```

Both methods can be filtered for all services or essential only.
