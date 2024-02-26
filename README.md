![crudstarter_small](https://user-images.githubusercontent.com/34785562/213905472-858273a7-5f49-4261-b23b-80b2e9e78778.gif)

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

> ``php artisan gen:crud Post --fields="name:str description:text count:int by_admin:bool is_published:select:options=published,pending"``

> **Note:** Please run auto-alignment (code-formatting) command in your ide/text-editor in generated blade files [eg: `` Ctrl+Alt+Shift+L`` in phpstorm].

### Adding Relationships
to add this functionality simply add ``--relations="your code here"`` in gen command

> Example:  To generate Profile CRUD with relations (hasOne: Account, hasMany: Blogs and belongTo: User)   

> ``php artisan gen:crud Profile --fields="name:str user_id:fid" --relations="haso:account hasm:blogs belt:user"``

|Relation Name| Short Hand For        |
| ----------- | -----------------     |
| haso		  | hasOne                |
| hasm        | hasMany               |
| belt        | belongsTo             |
| belm        | belongsToMany         |

> **Note**: you can use ``hasMany``, ``belongsTo`` etc directly in --relations command if you feel comfortable and it currently only supports these 4 common relations type.

### Adding Soft-Deleting fuctionality
to add this functionality simply add ``--softDelete`` in gen command

> Example:  To generate Post CRUD with soft deletes

> ``php artisan gen:crud Post --fields="name:str description:text" --softDelete``

### Adding File Upload Helper
To add helper trait simply add ``--addFileTrait``
> Example:  
>  ``php artisan gen:crud Profile --fields="name:str avatar:str" --addFileTrait``

> **Note:** You only need to add it one time. No need to specify it on next command!
#### How to use trait helper
- Add helper trait in controller ``use  FileUploadTrait;``
- Add line to upload file ``$this->fileUpload($modelName, 'fieldName', 'folder-name', false);``
> **Note:**
> - `false` corresponds to deleting image.
> -  make sure to run php artisan storage:link
> - make sure to import trait at the top

> Usage Example:  going with Profile Model above

**For Storing Profile Avatar**
 ```php
public function store(ProfileRequest $request) { 

	$profile = Profile::create($request->except('avatar')); 
	
	if ($request->hasFile('avatar')) {
		   $this->fileUpload($profile, 'avatar', 'profile-image', false); 
	} 
	
	return redirect()->route('profiles.index')->with('message', 'Profile Created Successfully!'); 
}
```
**For Updating Profile Avatar**
```php
public function update(ProfileRequest $request, Profile $profile) { 

	$profile->update($request->except('avatar')); 
	
	if ($request->hasFile('avatar')) {
		if (!is_null($profile->avatar)) {
			$this->fileUpload($profile, 'avatar', 'profile-image', true);
		}
		$this->fileUpload($profile, 'avatar', 'profile-image', false);
	} 
	
	return redirect()->route('profiles.index')->with('message', 'Profile Created Successfully!'); 
}
``` 
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
- You may have to easily customize blade files according to your dashboard template.
  Which Can be done easily.

- HAPPY CODING :metal: 
