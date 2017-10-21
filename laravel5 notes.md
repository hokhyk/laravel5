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

Route::get('users/{id}', function (
Application $injectedApplication,
$thisIsActuallyTheRouteId,
Request $injectedRequest
) {
//
});

### optional route parameters
Route::get('users/{id?}', function ($id = 'fallbackId') {
//
});

### regular expression for routes matching:
And you can use regular expressions to define that a route should only match if a
parameter meets particular requirements.

Route::get('users/{id}', function ($id) {
//
})->where('id', '[0-9]+');

Route::get('users/{username}', function ($username) {
//
})->where('username', '[A-Za-z]+');

Route::get('posts/{id}/{slug}', function ($id, $slug) {
//
})->where(['id' => '[0-9]+', 'slug' => '[A-Za-z]+']);

## route names
<a href="<?php echo url('/'); ?>">

// app/Http/routes.php
Route::get('members/{id}', [
'as' => 'members.show',
'uses' => 'MembersController@show'
]);
// view file
<a href="<?php echo route('members.show', ['id' => 14]); ?>">

Route::get('/members/{id}/edit', [
'as' => 'members.edit',
function ($id) {
//
        
}
]);

### route() helper function  in view file:
Passing route parameters to the route() helper.
// view file
<a href="<?php echo route('members.show', ['id' => 14]); ?>">

There are a few different ways to pass these parameters. Let’s imagine a route defined
as users/{userId}/comments/{commentId}. If the user ID is 1 and the comment ID
is 2, let’s look at a few options we have available to us.
OPTION 1.
route('users.comments.show', [1, 2])
// http://myapp.com/users/1/comments/2
OPTION 2.
route('users.comments.show', ['userId' => 1, 'commentId' => 2])
// http://myapp.com/users/1/comments/2
OPTION 3.
route('users.comments.show', ['commentId' => 2, 'userId' => 1])
// http://myapp.com/users/1/comments/2
OPTION 4.
route('users.comments.show', ['userId' => 1, 'commentId' => 2, 'opt' => 'a'])
// http://myapp.com/users/1/comments/2?opt=a

## route groups
Route::group([], function () {
Route::get('hello', function () {
return 'Hello';
});
Route::get('world', function () {
return 'World';
});
});
The empty array that’s the first parameter, however, allows you to pass a variety of
configuration settings that will apply to the entire route group.

Route::group(['middleware' => 'auth'], function () {
Route::get('dashboard', function () {
return view('dashboard');
});
Route::get('account', function () {
return view('account');
});
});

### route group route prefix
Route::group(['prefix' => 'api'], function () {
Route::get('/', function () {
//
});
Route::get('users', function () {
//
});
});
Note that each prefixed group also has a / group that represents the root of the prefix
—in this example that’s /api.


















