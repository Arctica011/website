<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::any('{all}', function () {  if(config('app.env') !== "production"){
    $connection = @fsockopen('localhost', 3000);
    if (is_resource($connection)) {
        fclose($connection);
        return view('develop');
    }
}
    return view('production');
})
    ->where('all', '^((?!api).)*');

