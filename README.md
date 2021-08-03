## Make sure to install these packages first
```shell
composer req annotation api

```


## First,Install the package through composer 
```shell
composer require geeksltd/pangolin.symfony --dev
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


### Enable Debug menu in your symfony application

```code
 {% include '@Pangolin/debug.html.twig' %}
```


# Available apis 

## 1 ) /cmd/db-restart
### This api drops and recreates the current the database and runs all migrations and fixtures.
Note : Make sure your database name contains "*.temp" at the end.Otherwise, you will get error.

Response Examples :
```json
{
  "message": "All operations successfully completed",
  "status": true
}
```
Or
```json
{
  "message": "Database name does not contain .temp suffix",
  "status": false
}
```


## 2 ) /cmd/get-db-changes
### Get the latest executed sql queries. all the queries will be wiped out once the api is called 
Response Examples :
```json
{
  "data": [
    {
      "log": "INSERT INTO product (title, code, type) VALUES ('product_name', 'code1', 'car')"
    },
    {
      "log": "INSERT INTO user (title) VALUES ('ranger')"
    }
  ]
}
```
Or
```json
{
  "data": null
}
```


## 3 ) /cmd/db-run-changes
### This api expects a xml request and runs all the queries from DataBaseCommand attribute
Response Examples :
```json
{
"message": "All queries have been executed successfully",
"status": true
}
```

## 3 ) /cmd/outlook
### This route displays a list of available mails in the database based on interface implemented by an entity 


