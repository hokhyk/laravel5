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

## route names  (also there can be route group names)
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

### route group sub-domain routing  (对于开启子域名解析的网站需要进行设置子域名路由）
#### First, to present different
sections of the application (or entirely differently applications) to different subdo‐
mains:
Route::group(['domain' => 'api.myapp.com'], function () {
Route::get('/', function () {
//
});
});
#### And second, to set part of the subdomain as a parameter—most often used in cases of
multitenancy (think Slack or Harvest, where each company gets their own subdo‐
main like tighten.slack.co).
Route::group(['domain' => '{account}.myapp.com'], function () {
Route::get('/', function ($account) {
//
});
Route::get('users/{id}', function ($account, $id) {
//
});
});

### route group namespace prefix
// App\Http\Controllers\ControllerA
Route::get('/', 'ControllerA@index');
Route::group(['namespace' => 'API'], function () {
// App\Http\Controllers\API\ControllerB
Route::get('/', 'ControllerB@index');
});

### Route group name prefix
Route::group(['as' => 'users.', 'prefix' => 'users'], function () {
   Route::group(['as' => 'comments.', 'prefix' => 'comments'], function () {
// Route name will be users.comments.show
Route::get('{id}', ['as' => 'show', function () {
//
}]);
});
});
# VIEWS
In Laravel, there are two formats of view you can use out of the box: Blade or PHP. The difference is in the filename: about.php will be rendered with the
PHP engine, and about.blade.php will be rendered with the Blade engine.

   There are also three different ways to do view(). For now, just con‐
cern yourself with view(), but if you ever see View::make(), it’s the
same thing, and you could also inject the Illuminate\View\View
Factory if you prefer.

Route::get('/', function () {
return view('home');
});
This code above looks for a view in resources/views/home.blade.php or
resources/views/home.php, and loads its contents and parses any inline PHP or
control structures until you have just the view’s output. 
## Passing variables to views
### chain method
    Route::get('tasks', function () {
return view('tasks.index')
->with('tasks', Task::all());
This Closure loads the resources/views/tasks/index.blade.php or resources/
views/tasks/index.php view and passes it a single variable named tasks, which
contains the result of the Task::all() method, which is a database query.

### array method. If you prefer non-fluent routing, you could pass an array of variables as the second parameter:
Route::get('tasks', function () {
return view('tasks.index', ['tasks' => Task::all()]);
});

## View Composers and sharing variables with every view
Sometimes it can become a hassle to pass the same variables over and over. There
may be a variable that you want accessible to every view in the site, or to a certain
class of views or a certain included sub-view—for example, all views related to tasks,
or the header partial.
It’s possible to share certain variables with every template or just certain templates,
like in the following code:
view()->share('variableName', 'variableValue');















