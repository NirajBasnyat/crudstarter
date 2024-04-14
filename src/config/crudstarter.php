<?php

return [

    /*
     * Fields that will be treated as image fields
     *
     * example: php artisan gen:crud Post --fields="avatar:str icon:str"
     * here avatar field will be generated as image but icon will be text field
     */

    'image_fields' => ['image', 'images', 'img', 'pic', 'pics', 'picture', 'pictures', 'avatar', 'photo', 'photos', 'gallery', 'logo', 'logos', 'favicon', 'favicons', 'banner', 'banner_image'],

    /*
     * Default validation rule
     *
     * option is required or nullable
     */

    'default_validation' => 'required',

    /*
     * Determines how many columns each row in the form should contain. This affects the layout of the form, making it more organized and visually appealing.
     *
     * If you set this to 2, each row in the form will contain two fields, assuming a 12-column grid layout. This means each field will span 6 columns (12 / 2 = 6).
     */

    'cols_per_row' => 1,

    /*
    * Image Field is required or not
    *
    * option is true or false
    */

    'image_required' => false,

    /*
    * Default Fallback Image Path
    *
    * default_image_type can be 'url' (ex: https://picsum.photos/id/237/200/300) or 'asset' (ex: uploaded-images/my-images/hero_image.png)
    *
    * DO NOT user asset() here just use path
    */
    'default_image_type' => 'url',

    'default_image_path' => "https://upload.wikimedia.org/wikipedia/commons/a/ac/No_image_available.svg",

    /*
    * Default Route
    *
    * make sure the given route exists
    */

    'crud_route' => 'web.php',
    'api_route' => 'api.php'

];
