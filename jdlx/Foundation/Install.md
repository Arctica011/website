###### Package
Add the package by loading it through composer.

```
"require": {
    ...
    
    "jdlx/jdlx": "*"
    
}

 "repositories": [
        ...
       
        {
            "type": "path",
            "url": "./jdlx"
        }
    ],
```

###### API Version

Register the Facade
```php
 // app/Http/kernel.php
 // Add api midleware
 
   protected $middlewareGroups = [
        'api' => [
            SetApiVersion::class,
          ],
    ];
    
// app/Config/app.php
  
  'providers' => [
        ...
        App\Library\ApiVersion\Provider\ApiVersionServiceProvider::class
    ]

 // change base path for swagger
 // app/Http/Documentation/Server
 
/**
 * @OA\Server(
 *     description="API V1",
 *     url="/api/v1"
 * )
 */

// Add the option to the router
// routes/api.php
$apiRoutes = function () {
    // define routes
};
Route::group(['prefix' => '{version?}', 'where' => ['version' => 'v[0-9]+']], $apiRoutes);

```


