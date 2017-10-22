# routes
## understanding HTTP Verbs
Here’s the quick rundown: 
GET requests a resource and 
HEAD asks for a headers-only version of the GET,
POST creates a resource, 
PUT overwrites a resources and 
PATCH modifies a resource, 
DELETE deletes a resource, and 
OPTIONS asks the server which verbs are allowed at this URL.
TRACE and 
CONNECT.
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

# controllers
It may be tempting to cram all of the application’s logic into the controllers, but it’s
better to think of controllers as the traffic cop that routes HTTP requests around
your application. Since there are other ways requests can come into your application
—cron jobs, “Artisan” command line calls, queue jobs, etc.--it’s wise to not rely on
controllers for much behavior. This means a controller’s primary job is to capture the
intent of an HTTP Request and pass it onto the rest of the application.
## generate a controller by Artisan
php artisan make:controller MySampleController
This will create a new file named MySampleController.php in app/Http/Controllers.

## get user input for controllers
### using Facade  Input::get(fieldName)
// TasksController.php
...
public function store()
{
$task = new Task;
$task->title = Input::get('title');
$task->description = Input::get('description');
$task->save();
return redirect('tasks');
}
### using \Illuminate\Http\Request object  ( dependency injection in controller)
Controller method injection via typehinting
// TasksController.php
...
public function store(\Illuminate\Http\Request $request)
{
$task = new Task;
$task->title = $request->input('title');
$task->description = $request->input('description');
$task->save();
return redirect('tasks');
}

## resource controllers  for RESTFUL/CRUD rules 
For each, you can see the HTTP Verb, the URL, the controller method name, and the
“name”:
Verb   URL                 Controller-method    Name            Description
GET    /tasks                index            tasks.index       Show all tasks
GET    /tasks/create         create           tasks.create      Show the create task form
POST   /tasks                store            tasks.store       Accept form submission from the create task form
GET    /tasks/{photo}        show             tasks.show        Show one task
GET    /tasks/{photo}/edit   edit             tasks.edit        Edit one task
PUT/PATCH /tasks/{photo}  update              tasks.update      Accept form submission from the edit task form
DELETE /tasks/{photo}     destroy             tasks.destroy     Delete one task

## resource controller binding with routes  (resource route)
Resource controller binding
// app/Http/routes.php
Route::resource('tasks', 'TasksController');

### to show all routes defined for current app:
php artisan route:list

## route model binding
### implicit route model binding
Route::get('conferences/{conference}', function (Conference $conference) {
return view('conferences.show')->with('conference', $conference);
});
Because the route parameter ({conference}) is the same as the method parameter
($conference), and the method parameter is type-hinted with a Conference model
(Conference $conference), Laravel sees this as a route model binding. Every time
this rout is visited, the applicaiton will assume that whatever is passed into the URL
in place of {conference} is an ID that should be used to look up a Conference, and
then that resulting model instance will be passed in to your Closure or controller
method.

#### Customizing the royte key for an Eloquent model (默认是查找ID column，通过重载getRouteKeyName()这个函数修改成想要查找的字段。
Any time an Eloquent model is looked up via a URL segment (usu‐
ally because of route model binding), the default column Eloquent
will look it up by is its primary key (ID).
To change the column your Eloquent model uses as its URL
lookup, add a method to your model named getRouteKeyName:
public function getRouteKeyName()
{
return 'slug';
}
Now, a URL like conferences/{conference} will expect to get the slug instead of the
ID, and will perform its lookups accordingly

### explicit/custom route model binding （例子说明了怎么將路由的资源参数绑定到自定义的Eloquent Model上。即怎么修改route service provider实现將route resource自动注入到controller。）
To manually configure Route Model bindings, go to the boot() method of
App\Providers\RouteServiceProvider and add a line like in the example.
Adding a Route Model Binding
public function boot(Router $router)
{
parent::boot($router);
$router->model('event', Conference::class);
}
You’ve now defined that whenever a route has a parameter in its definition named
{event}, the route resolve will return an instance of the Conference class with the ID
of that URL parameter.
Using an explicit Route Model Binding
Route::get('events/{event}', function (Conference $event) {
return view('events.show')->with('event', $event);
});

# Form method spoofing & CSRF
## Form method spoofing （用html进行http请求伪造）
To inform Laravel that the form you’re currently submitting should be treated as
something other than POST, add a hidden variable named _method with the value of
either PUT, PATCH, or DELETE, and Laravel will match and route that form submission
_as if it were actually a request with that verb._
Example 3-34. Form method spoong
<form action="/tasks/5" method="POST">
<input type="hidden" name="_method" value="DELETE">
</form>
The form in Example 3-34, since it’s passing Laravel the method of “DELETE,” will
match routes defined with Route::delete but not those with Route::post.

## enable CSRF (cross site request forgery) protection in laravel
By default, every route in Laravel except “read-only” routes (those using GET, HEAD, or
OPTIONS) are protected against Cross-Site Request Forgery by requiring a token (in
the form of an input named _token) be passed along with each request. This token is
generated at the start of every session, and every non-read-only route compares the
submitted _token against the session token.
### In HTML forms, add the _token input to each of your submissions.
<form action="/tasks/5" method="POST">
<input type="hidden" name="_method" value="DELETE">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>
### In JavaScript applications, 
it’s a bit more work, but not much. The most common solution, for sites using jQuery, is to store the token in a meta tag on every page.
Storing the CSRF token in a meta tag
<meta name="csrf-token" content="{{ csrf_token() }}">
Storing the token in a meta tag makes it easy to globally bind that to the correct
HTTP header, which you can do once globally for all jQuery requests.
Globally binding a jQuery header for CSRF
$.ajaxSetup({
headers: {
'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});
Laravel will check the X-CSRF-TOKEN on every request and valid tokens passed there
will mark the CSRF protection as satisfied. If not, you will be likely running into the dreaded TokenMismatchException.

# Redirects
There are three common responses that you’ll return from your controller methods
or route Closures: views, redirects, and errors. 
There are two common ways to generate a redirect; we’ll use the Façades here, but
you may prefer the global helper(/Illuminate/Foundation/helpers.php).Both are create an instance of 
Illuminate\Http\RedirectResponse. 
## three ways to return a redirect
Route::get('redirect-with-facade', function () {
return Redirect::to('auth/login');
});
Route::get('redirect-with-helper', function () {
return redirect()->to('auth/login');
});
Route::get('redirect-with-helper-shortcut', function () {
return redirect('auth/login');
});

if (! function_exists('redirect')) {
    /**
     * Get an instance of the redirector.
     *
     * @param  string|null  $to
     * @param  int     $status
     * @param  array   $headers
     * @param  bool    $secure
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    function redirect($to = null, $status = 302, $headers = [], $secure = null)
    {
        if (is_null($to)) {
            return app('redirect');
        }

        return app('redirect')->to($to, $status, $headers, $secure);
    }
}

## Redirect options
### Redirect to
The method signature for the to() method for redirects looks like this:

function to($to = null, $status = 302, $headers = [], $secure = null)

$to is a valid internal URI; $status is the HTTP status (defaulting to 301 FOUND);
$headers allows you to define which HTTP headers to send along with your redirect;
and $secure allows you to override the default choice of http vs https (which is nor‐
mally set based on your current request URL).

### Redirect route
The route() method is the same as the to() method, but rather than point to a par‐
ticular URI, it points to a particular route name.
Example 3-39. Redirect route
Route::get('redirect', function () {
return Redirect::route('conferences.index');
});
Note that, since some route names require parameters, its parameter order is a little
bit different; it has an optional second parameter for the route parameters:
function route($to = null, $parameters = [], $status = 302, $headers = [])

Redirect route with parameters
Route::get('redirect', function () {
return Redirect::route('conferences.show', ['conference' => 99]);
});

### Redirect back
Because of some of the built-in conveniences of Laravel’s session implementation,
your application will always have a knowledge of what the user’s previously-visited
page was. That opens up the opportunity for a Redirect::back() redirect, which
simply redirects the user to whatever page they came from.

### Redirect::guest()   Redirect::intendend() 
 it captures the cur‐
rent URL in a query parameter named “url.intended” for use later. You would use this
to redirect a user away from their current URL with the intent for them to return
after authentication.

### other redirect methods (vendor/laravel/framework/src/Illuminate/Routing/Redirector.php)
 home() redirects to a route named home
• refresh() redirects to the same page the user is currently on
• away() allows for redirecting to an external URL without the default URL valida‐
tion
• secure() is like to() with the secure parameter set to true
• action() allows you to like to a controller and method like this: action(MyCon
troller@myMethod)

### Redirect with data  using  ->with()
When you’re redirecting the user to a different page, you often want to pass certain
data along with them. You could manually flash the data to the session, but Laravel
has some convenience methods to help you with that.

Route::get('redirect-with-key-value', function () {
return Redirect::to('dashboard')
->with('error', true);
});
Route::get('redirect-with-array', function () {
return Redirect::to('dashboard')
->with(['error' => true, 'message' => 'Whoops!']);
});

#### Redirect with form input  (->withInput())
You can also redirect with the form input flashed; this is most common in the case of
a validation error, where you want to send the user back to the form they just came
from.

Route::get('form', function () {
return view('form');
});
Route::post('form', function () {
return Redirect::to('form')
->withInput()
->with(['error' => true, 'message' => 'Whoops!']);
});

The easiest way to get the flashed Input that was passed with withInput is the old()
helper, which can be used to get all old input (old()) or just the value for a particular
key (old(username)). You’ll commonly see this in views, which allows this HTML to
be used both on the “create” and the “edit” view for this form:

<input name="username" value="<?=
old('username', 'Default username instructions here');
?>">
#### Redirect with form validation errors (withErrors())
Route::post('form', function () {
$validator = Validator::make($request->all), $this->validationRules);
if ($validator->fails()) {
return Redirect::to('form')
->withErrors($validator)
->withInput();
}
});

### redirect and then Abort
After returning views and redirects, the most common way to exit a route is to abort.
There’s a globally available abort() method which optionally takes an HTTP status
code, a message, and a headers array as parameters.

403 Forbidden Abort
Route::post('something-you-cant-do', function () {
abort(403, 'You cannot do that!');
});

### custom responses other than returning views, redirects, errors.
There are a few other options available for us to return, so let’s go over the most com‐
mon responses after views, redirects, and errors. Just like with redirects, you can
either use the Response Façade or the response() helper to run these methods on.
#### Response make
If you want to create an HTTP Response manually, just pass your data into the first
parameter of Response::make(): 

return Response::make(Hello, World!). 

Once again, the second parameter is the HTTP status code and the third is your headers.
#### Response json/jsonp
To create a JSON-encoded HTTP response manually, pass your JSON-able content
(arrays, collections, or whatever else) to the json() method: 

return Response::json(User::all());

. It’s just like make(), except it json_encodes your
content and sets the appropriate headers.
#### Response download
To send a downloadable file, pass either a string filename or a SplFileInfo instance
to download(), with an optional second parameter of the filename: 

return Response::download(file501751.pdf, myFile.pdf);



