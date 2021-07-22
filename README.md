## First,Install the package through composer 
```shell
composer require geeksltd/pangolin.symfony "dev-master" --dev
```

## Enable pangolin bundle in your config/bundles.php file

### Simply add this PangolinBundle class to the array as bellow.Pangolin bundle only is available on dev and test environment.

```php
// config/bundles.php
return [
    Geeks\Pangolin\PangolinBundle::class => ['dev' => true, 'test' => true],
];
```

### This Project contains controllers and routes. you must enable pangolin routes in your main routes.yaml file

```yaml
# config/routes.yaml
pangolin_bundle:
    resource: '@PangolinBundle/Resources/config/routes.yaml'
```

# Available apis 

## 1 ) /cmd/db-restart
### This api drops and recreates the current the database and runs all migrations and fixtures.
Note : Make sure your database name contains "*.temp" at the end.Otherwise, you will get error.


## 2 ) /cmd/get-db-changes
### Get the latest executed sql queries. all the queries will be wiped out once the api is called 
