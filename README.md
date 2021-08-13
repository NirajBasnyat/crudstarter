
## Crud and API Generator Package

[![Stars](	https://img.shields.io/github/stars/NirajBasnyat/crudstarter)](https://github.com/NirajBasnyat/crudstarter/stargazers)
[![Issues](https://img.shields.io/github/issues/NirajBasnyat/crudstarter)](https://github.com/NirajBasnyat/crudstarter/issues)
![License](https://img.shields.io/github/license/NirajBasnyat/crudstarter)
![Packagist Downloads](https://shields.api-test.nl/packagist/dt/niraj/crudstarter)

### Package which lets you automate tedious CRUD Operations.

## Requirements
```
Laravel Version: >= 8.0
PHP Version: >= 7.3
Composer: >= 2.0
```

## Installation
```
composer require niraj/crudstarter --dev
```
```
php artisan vendor:publish --tag=crud-stub
```

## Package Usage

### Basic Usage

- To generate CRUD

 ``php artisan gen:crud {ModelName} ``

- To generate API

 ``php artisan gen:api {ModelName} ``

 - To delete CRUD Files

 ``php artisan del:crud {ModelName} ``

- To delete API Files

 ``php artisan del:api {ModelName} ``

> Example:  To generate Post CRUD ``php artisan gen:crud Post ``

### Adding Fields
You can add fields in ``gen`` commands which auto fills **model, migration, request and api resources**

To add fields we use
 ``--fields="field_name1:data_type1{space}field_name2:data_type2"``

> Example:  To generate Post CRUD with fields 
> 
> ``php artisan gen:crud Post --fields="name:str description:text count:int status:bool"``

### Field Data Type
some short hands for convenience are provided i.e instead of **``unsignedInteger``** we can use  **``uint``**  instead while defining fields

| Data type Name| Short Hand For      |
| ----------- | -----------------     |
| inc		  | increments            |
| int         | integer               |
| uint        | unsignedInteger       |
| tinyint     | tinyInteger           |
| utinyint    | unsignedTinyInteger   |
| smallint    | smallInteger          |
| usmallint   | unsignedSmallInteger  |
| mediumint   | mediumInteger         |
| umediumint  | unsignedMediumInteger |
| bigint      | bigInteger            |
| ubigint     | unsignedBigInteger    |
| txt         | text                  |
| tinytext    | tinyText              |
| mediumtext  | mediumText            |
| longtext    | longText              |
| bool        | boolean               |
| fid         | foreignId             |

> **Note**: For other data types like **``date, enum, decimal, uuid``** etc can typed as it is.

## What will be generated !

- ### CRUD 
  -**[ Model, Controller, Blade Files, Request, Migration ]** with **Feature Test Skeleton!**
  
- ###  API  

  -**[ ApiController,  ApiRequest,  ApiResource ]** with **Feature Test Skeleton!**

 > **Note:** Model, Factory, Migration can be also generated for API if needed.

## Customization

- You can easily customize everything to your need by simply changing stubs files present in crud-stub folder present in resources/crud-stub

## Notes
- Though Files will be generated automatically, You will need to add migrations and Form Request data.

- You may have to easily customize blade files according to your dashboard template.
Which Can be done easily.

- HAPPY CODING :metal: 
