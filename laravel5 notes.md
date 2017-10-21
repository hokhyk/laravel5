# routes
## route verbs
Route::get('/', function () {
return 'Hello, World!';
});
Route::post('/', function () {});
Route::put('/', function () {});
Route::delete('/', function () {});
Route::any('/', function () {});
Route::match(['get', 'post'], '/', function () {});

Route::get('/', 'WelcomeController@index');
## route parameters 
relationship between route parameters/closure/controller method parameters:
The only thing that defines which route parameter matches with which method parameter is that they are in the same order (left to right), excluding injected dependencies.

Route::get('users/{id}/friends', function ($id) {
//
});

### optional route parameters
Route::get('users/{id?}', function ($id = 'fallbackId') {
//
});

