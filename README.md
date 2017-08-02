# DiggyRouter

[![Latest Stable Version](https://poser.pugx.org/webcretaire/diggy-router/version)](https://packagist.org/packages/webcretaire/diggy-router)
[![Total Downloads](https://poser.pugx.org/webcretaire/diggy-router/downloads)](https://packagist.org/packages/webcretaire/diggy-router)
[![License](https://poser.pugx.org/webcretaire/diggy-router/license)](https://packagist.org/packages/webcretaire/diggy-router)


Simple routing component for php

## Installation
Currently the only way of installing is via composer :
```bash
composer require webcretaire/diggy-router
```

## Usage
First you have to register the routes you want to use in a YAML file with the following structure :
````yaml
routes:
  # First Route
  - uri: '/addressOfYourPage'
    controller: 'Name\Of\A\Class'
    action: 'nameOfTheFunctionToCall'
  # Second Route
  - uri: '/addressOfYourSecondPage'
    controller: 'Name\Of\A\Class'
    action: 'nameOfTheFunctionToCall'
  # ...
````

Note that the "action" parameter is optionnal, if it is not provided the router will try to call a "render()" function with no parameters

Then create a new router :
````php
$router = new DiggyRouter\Router()
````
Load your routes into the router :
````php
$router->loadRoutes("path/to/your/routing.yml");
````
Use the router to call the correct function in the correct controller according to the requested URI :
````php
$router->handleRequest();
````
By default the router tries to find a route that matches the requested URI which is stored in 
```$_SERVER['REQUEST_URI']```, but you can specify the URI to use by passing it to the function :
````php
$router->handleRequest($customURI);
````
