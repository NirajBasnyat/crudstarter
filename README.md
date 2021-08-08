## Crud and API Generator Package

[![Stars](	https://img.shields.io/github/stars/NirajBasnyat/crudstarter)](https://github.com/NirajBasnyat/crudstarter/stargazers)
[![Issues](https://img.shields.io/github/issues/NirajBasnyat/crudstarter)](https://github.com/NirajBasnyat/crudstarter/issues)
![License](https://img.shields.io/github/license/NirajBasnyat/crudstarter)
![Packagist Downloads](https://shields.api-test.nl/packagist/dt/niraj/crudstarter)


### Package which let's you automate tedious CRUD Operations.

## Requirements
```
Laravel Version: >= 8.0
PHP Version: >= 7.1
Composer: >= 2.0
```

## Installation
```
composer require niraj/crudstarter --dev
```

```
php artisan vendor:publish --tag=crud-stub
```


## Usage

- To generate CRUD

 ``php artisan gen:crud {ModelName} ``

- To generate API

 ``php artisan gen:api {ModelName} ``

 - To delete CRUD Files

 ``php artisan del:crud {ModelName} ``

- To delete API Files

 ``php artisan del:api {ModelName} ``

> Example:  To generate Post CRUD ``php artisan gen:crud Post ``


## What will be generated !

These will let you generate
- CRUD **[ Model, Controller, Blade Files, Request, Migration ]** with **Feature Test Skeleton!**
- API  **[ ApiController, ApiRequest, ApiResource ]** with **Feature Test Skeleton!**

 > **Note:** Model, Factory, Migration can be also generated for API if needed.




## Customizations

- You can easily customize everything to your need by simply changing stubs files present in crud-stub folder present in resources/crud-stub

## Notes
- Though Files will be generated automatically, You will need to add migrations and FormRequest data.

- You may have to easily customize blade files according to your dashboard template.
Which Can be done easily.

- HAPPY CODING :metal: 
