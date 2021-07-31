## Crud and API Generator Package

### This package provides an artisan command to generate a basic crud

composer install command: 
```
composer require niraj/crudstarter
```

## 
# Let's Generate !

## What will be generated

These will let you generate
- CRUD **[ Model, Controller, Blade File, Request, Factory, Migration ]** with **Feature Test!**
- API  **[ ApiController, ApiRequest, ApiResource ]** with **Feature Test!**
 > **Note:** Model, Factory, Migration can be also generated for API if needed.


## How To Start
- Clone the repo
- type **php artisan** inside your app terminal you will see two custom commands ie **gen:api** and **gen:crud**
- To generate CRUD ``php artisan gen:crud {ModelName} ``
- To generate API ``php artisan gen:api {ModelName} ``

	``[eg: gen:crud Post or gen:api Admin]``

## Customizations

- You can easily customize everything to your need by simply changing stubs files present in stub folder.
 > **Note:** It will need to add your own migrations fields, validations in request.
