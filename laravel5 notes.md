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

### array method. If you prefer non-fluent routing, you could pass an array of variables as the second
 parameter:
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
This will create a new file named MySampleController.php in app/Http/Control
lers.

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

#### Customizing the royte key for an Eloquent model (默认是查找ID column，通过重载getRouteKeyName()
这个函数修改成想要查找的字段。
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
## Form method spoofing
 （用html进行http请求伪造）
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
Illuminate\Http
\RedirectResponse. 
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

### Redirect with data  using  ->with
()
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

#### Redirect with form input
  (->withInput())
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

## testing routes 
### Writing a simple POST route test

// AssignmentTest.php

public function test_post_creates_new_assignment()

{

$this->post('/assignments', [

'title' => 'My great assignment'

]);

$this->seeInDatabase('assignments', [

'title' => 'My great assignment'

]);

}

### Writing a simple GET route test

// AssignmentTest.php

public function test_list_page_shows_all_assignments()

{

$assignment = Assignment::create([

'title' => 'My great assignment'

]);

$this->visit('assignments')

->andSee(['My great assignment']);

}

# blade templating
## {{ $variable }} is functionally equivalent to <?=
 htmlentities($variable) ?>
If you want to echo without the escaping, use {!!
 $variable !!} instead, it equals to <?= $variable ?>
## Using @{{ to distinguish between Blade {{ }} and other template language's {{ }}
Any {{ that’s prefaced with an @ will be ignored by Blade. 
Using @{{ to ask Blade to skip

// Parsed as Blade

{{ $bladeVariable }}

// @ removed, and echoed to the view directly

@{{ handlebarsVariable }}

### @if, @else, @elseif, and @endif

@if (count($talks) === 1)

There is one talk at this time period.

@elseif (count($talks) === 0)

There are no talks at this time period.

@else

There are {{ count($talks) }} talks at this time period.

@endif

### @unless and @endunless

@unless ($user->hasPaid())

You can complete your payment by switching to the payment tab.

@endunless

### @for and @endfor

@for ($i = 0; $i < $talk->slotsCount(); $i++)

The number is {{ $i }}

@endfor

### @foreach and @endforeach

@foreach ($talks as $talk)

• {{ $talk->title }} ({{ $talk->length }} minutes)

@endforeach

### @while

@while ($item = array_pop($items))

{{ $item->orSomething() }}<br>

@endwhile

### @forelse

@forelse ($talks as $talk)

• {{ $talk->title }} ({{ $talk->length }} minutes)

@empty

No talks this day.

@endforelse

### or
{{ $title or "Default" }} 
will echo the value of $title if it’s set, or “Default” if

not.

## template inheritance
### Blade layout
  @section/@show and @yield
<!-- resources/views/layouts/master.blade.php -->

<html>

<head>

<title>My Site | @yield('title', 'Home Page')</title>

</head>

<body>

<div class="container">

@yield('content')

</div>

@section('footerScripts')

<script src="app.js">

@show

</body>

</html>

### Extending a Blade Layout

<!-- resources/views/dashboard.blade.php -->

@extends('layouts.master')
@section('title', 'Dashboard')

@section('content')

Welcome to your application dashboard!

@endsection

@section('footerScripts')

@parent

<script src="dashboard.js">

@endsection

### @include: Including view partials with @include

<!-- resources/views/home.blade.php -->

<div class="content" data-page-name="{{ $pageName }}">

<p>Here's why you should sign up for our service: <strong>It's Great.</strong></p>

@include('sign-up-button', ['text' => 'See just how great it is'])

</div>

<!-- resources/views/sign-up-button.blade.php -->

<a class="button button--callout" data-page-name="{{ $pageName }}">

<i class="exclamation-icon"></i> {{ $text }}

</a>
### @each  Using view partials in a loop with @each

<!-- resources/views/sidebar.blade.php -->

<div class="sidebar">

@each('partials.module', $modules, 'module', 'partials.empty-module')
</div>

<!-- resources/views/partials/module.blade.php -->

<div class="sidebar-module">

<h1>{{ $module->title }}</h1>

</div>

<!-- resources/views/partials/empty-module.blade.php -->

<div class="sidebar-module">

No modules :(

</div>
Take a look at that @each syntax. The first parameter is the name of the view partial.

The second is the array or collection to iterate over. The third is the variable name

that each item will be passed to the view as. And the optional fourth parameter is the

view to show if the array or collection is empty.

# binding data to views using view composers
## Reminder on how to pass data to views
 using route definition
Route::get('passing-data-to-views', function () {

return view('dashboard')

->with('key', 'value');

});

## using view composers
### Sharing a variable globally
view()->share('posts', Post::recent());

You’ll likely place this code in some form of custom ViewComposerServiceProvider

(see ??? to learn more about Service Providers), but for now you could also just put it

in App\Providers\AppServiceProvider in the boot method.

Using view()→share() makes the variable accessible to every view in the entire appli‐

cation, however, so it might be overkill.

### Creating a Closure-based view composer

view()->composer('partials.sidebar', function ($view) {

$view->with('posts', Post::recent());

});

As you can see, we’ve defined the name of the view we want it shared with (parti

als.sidebar) in the first parameter and then passed a Closure to the second parame‐

ter; in the Closure, we’ve used $view→with() to share a variable, but now only with a

specific view.

Anywhere a view composer is binding to a particular view (like in
 the
Example which binds to partials.sidebar), you can also

pass an array of view names instead to bind to multiple views.

Or, you can use an asterisk in the view path: partials.*, or

tasks.*, or just *.

### Class-based view composers
let’s create the view composer class. There’s no formally defined place for view

composers to live, but the docs recommend App\Http\ViewComposers. So let’s create

App\Http\ViewComposers\RecentPostsComposer.
#### first step, to create A view composer

<?php namespace App\Http\ViewComposers;

use App\Post;

use Illuminate\Contracts\View\View;

class RecentPostsComposer

{

private $posts;

public function __construct(Post $posts)

{

$this->posts = $posts;

}

public function compose(View $view)

{

$view->with('posts', $this->posts->recent());

}

}

#### Registering a view composer in AppServiceProvider

view()->composer(

'partials.sidebar',

'App\Http\ViewComposers\RecentPostsComposer'

);

Note that this binding is the same as a Closure-based view composer, but instead of

passing a Closure, we’re passing the class name of our view composer. 

# service injection
There are three primary types of data we’re most likely to inject into a view: collec‐

tions of data to iterate over, single objects that you’re displaying on the page, and

services that generate data or views.
## sevice injection in route definition
With a service, the pattern will most likely look like the Example below, where we inject an

instance of the service into the route definition by type-hinting it in the route defini‐

tion’s method signature, and then pass it into the view.

Injecting services into a view via the route denition constructor


Route::get('injecting', function (AnalyticsService $analytics) {

return view('injecting')

->with('analytics', $analytics);

});

Using an injected analytics service in a view

<div class="finances-display">

{{ $analytics->getBalance() }} / {{ $analytics->getBudget() }}

</div>

## Injecting a service directly into a view

@inject('analytics', 'App\Services\Analytics')

<div class="finances-display">

{{ $analytics->getBalance() }} / {{ $analytics->getBudget() }}

</div>
The first parameter of @inject is the name of the variable you’re injecting, and the

second parameter is the class or interface that you want to inject an instance of. 
Just like view composers, Blade service injection makes it easy to make certain data or

functionality available to every instance of a view, without having to inject it via the

route definition every time.

# custom Blade directive
## Binding a custom Blade directive

// AppServiceProvider

public function boot()

{

Blade::directive('isGuest', function () {
return "<?php if (Auth::guest()): ?>";

});

}

## Creating a Blade directive with parameters

// Binding

Blade::directive('newlinesToBr', function ($expression) {

return "<?php echo nl2br{$expression}; ?>";

});

// In use

<p>@newlinesToBr($message->body)</p>

# Testing that a view displays certain content

// EventsTest.php

public function test_list_page_shows_all_events()

{

$event1 = factory(Event::class)->create();

$event2 = factory(Event::class)->create();

$this->visit('events')

->andSee($event1->title)

->andSee($event2->title);

}

# View Components 
## gulp and Elixir (laravel frontend assets management)
Laravel also provides a Gulp-based
 build system called Elixir and some conventions around non-PHP assets.
Elixir is at the core of the non-PHP frontend components.

### Compiling a Sass file in Gulp

var gulp = require('gulp'),

sass = require('gulp-ruby-sass'),

autoprefixer = require('gulp-autoprefixer'),

rename = require('gulp-rename'),

notify = require('gulp-notify'),

livereload = require('gulp-livereload'),

lr = require('tiny-lr'),

server = lr();

gulp.task('sass', function() {

return gulp.src('resources/assets/sass/app.scss')

.pipe(sass({

style: 'compressed',

sourcemap: true

}))

.pipe(autoprefixer('last 2 version', 'ie 9', 'ios 6'))

.pipe(gulp.dest('public/css'))

.pipe(rename({suffix: '.min'}))

.pipe(livereload(server))

.pipe(notify({

title: "Karani",

message: "Styles task complete."

}));

});

### Compiling a Sass file in Elixir

var elixir = require('laravel-elixir');

elixir(function(mix) {

mix.sass('app.scss');

});

### $ gulp --production
By default, Elixir doesn’t minify all the files it’s generating. But if you want to run the

build scripts in “production” mode, with all minification enabled, add the --
production flag.

### Compiling multiple les with Elixir

var elixir = require('laravel-elixir');

elixir(function(mix) {

mix.sass([

'app.scss',

'public.scss'

]);

});

### Disabling source maps in Elixir

var elixir = require('laravel-elixir');

elixir.config.sourcemaps = false;

elixir(function(mix) {

mix.sass('app.scss');

});

### Combining Stylesheets with Elixir

var elixir = require('laravel-elixir');

elixir(function(mix) {

// Combines all files from resources/assets/css and subfolders

mix.styles();

// Combines files from resources/assets/css

mix.styles([

'normalize.css',

'app.css'

]);

// Combines all styles from other directory

mix.stylesIn('resources/some/other/css/directory');

// Combines given styles from resources/assets/css

// and outputs to a custom directory

mix.styles([

'normalize.css',

'app.css'

], 'public/other/css/output.css');

// Combines given styles from custom directory

// and outputs to a custom directory

mix.styles([

'normalize.css',

'app.css'
], 'public/other/css/output.css', 'resources/some/other/css/directory');

});

### Combining JavaScript les with Elixir
any commands not provided with an output filename will generate to public/js/

all.js.

Combining JavaScript les with Elixir

var elixir = require('laravel-elixir');

elixir(function(mix) {

// Combines files from resources/assets/js

mix.scripts([

'jquery.js',

'app.js'

]);

// Combines all scripts from other directory

mix.scriptsIn('resources/some/other/js/directory');

// Combines given scripts from resources/assets/js

// and outputs to a custom directory

mix.scripts([

'jquery.js',

'app.js'

], 'public/other/js/output.js');

// Combines given scripts from custom directory

// and outputs to a custom directory

mix.scripts([

'jquery.js',

'app.js'

], 'public/other/js/output.js', 'resources/some/other/js/directory');

});

### Elixir generated versioning
Mix.version

var elixir = require('laravel-elixir');

elixir(function(mix) {

mix.version('public/css/all.css');

});
This will now generate a version of that file with a unique hash appended to it—

something like all-84fa1258.css.

Next, use the PHP elixir() helper in your views to refer to that file like in

the following Example:

Using the elixir() helper in views

<link rel="stylesheet" href="{{ elixir("css/all.css") }}">

### How does Elixir versioning work behind-the-scenes?

Elixir uses gulp-rev, which takes care of both appending the hash to the filenames,

and also generates a file named public/build/rev-manifest.json. This stores the

information the elixir() helper needs to find the generated file. Take a look at what

a sample rev-manifest.json looks like:

{

"css/all.css": "css/all-7f592e49.css"

}

### using Elixir for PHPUnit or PHPSpec testing
With Elixir it’s easy to run your PHPUnit or PHPSpec tests every time your test files

change.

You have two options, mix.phpUnit() and mix.phpSpec(), and each will run the

frameworks directly from the vendor folder, so you won’t have to do anything to

make them work.
If you add one of these methods to your Gulp file, you’ll find they only run once,

even if you’re using gulp watch. How do you get them to respond to changes in your

tests folder?

There’s a separate Gulp command for that: gulp tdd. This grabs just the test com‐

mands out of your Gulp file, whether phpUnit() or phpSpec(), listens to the appro‐

priate folder, and re-runs the test suite whenever any files change.

### Creating an Elixir extension

// Either in gulpfile.js, or in an external file and required in gulpfile.js

var gulp = require("gulp"),

shell = require("gulp-shell"),

elixir = require("laravel-elixir");

elixir.extend("log", function (message) {

new Task('log', function() {

return gulp.src('').pipe(shell('echo "' + message + '" >> file.log'));

})

.watch('./resources/some/files/**/*');

});

## Pagination
### Paginating database results Paginating a Query builder response
 (Eloquent ORM)
// PostsController

public function index()

{

return view('posts.index', ['posts' => DB::table('posts')->paginate(20)]);

}
The example defines that this route should return 20 posts per page, and will define

which page the current user is on based on their URL’s page query parameter, if it has

one. Eloquent models all have the same paginate() method.

### Manually creating paginators
If you’re not working with Eloquent or the Query Builder, or if you’re working with a

complex query (e.g. those using groupBy), you might find yourself needing to create a

paginator manually. Thankfully, you can do that with the 

Illuminate\Pagination
\Paginator    or the 
Illuminate\Pagination\LengthAwarePaginator classes.

The difference between the two classes is that Paginator will only provide previous

and next buttons, but no links to each page; LengthAwarePaginator needs to know

the length of the full result, so that it can generate links for each individual page.

## Message bags
Illuminate\Support\MessageBag 
is a class tasked with storing, categorizing, and

returning messages that are intended for the end user. It groups all messages by key,

which are likely to be something like errors and messags (@todo is that actually

right?), and provides convenience methods for getting all its stored messages or only

those for a particular key, and for outputting these messages in various formats.

### Manually creating and using MessageBag

$messages = [

'errors' => [

'Somethign went wrong with edit 1!'

],

'messages' => [

'Edit 2 was successful.'

]

];

$messagebag = new \Illuminate\Support\MessageBag($messages);

// Check for errors; if there are any, decorate and echo

if ($messagebag->has('errors')) {

echo '<ul id="errors">';

foreach ($messagebag->get('errors', '<li><b>:message</b></li>') as $error) {

echo $error;

}

echo '</ul>';

}

### Error bag snippet

// partials/errors.blade.php

@if ($errors->any())

<div class="alert alert-danger">

<ul>

@foreach ($errors as $error)

<li>{{ $error }}</li>

@endforeach

</ul>

</div>

@endif

## The string helpers and pluralization
Laravel has a series of helpers for manipulating strings. They’re available as methods

on the Str class (e.g. Str::plural(), but most also have a function shortcut (e.g.

str_plural()).

The Laravel documentation covers all of the string helpers in detail (TODO INSERT

LINK https://laravel.com/docs/5.1/helpers), but here are a few of the most-commonlyused helpers:

• e: a shortcut for html_entities

• starts_with, ends_with, str_contains: check a string (first parametr) to see if it

starts with, ends with, or contains another string (second parameter)

• str_is: checks whether a string (second parameter) matches a particular pattern

(first parameter)—for example, foo* will match foobar and foobaz

• str_slug: converts a string to a URL-type slug with hyphens

• str_plural(word, num), str_singular: pluralizes a word or singularizes it; Englishonly

### Localization
In Laravel, you’ll need to set an App locale at some point during the page load so the

localization helpers know which bucket of translations to pull from. You’ll do this

with App::setLocal($localeName), and you can run it in a service provider or a

route or wherever else.
You can define your fallback locale in config/app.php, where you should find a fall

back_local key.

#### Basic localization Basic use of trans()
 helper function.
Let’s assume we are using the en locale right now. Laravel will look for a file in resour
ces/lang/en/messages.php, which it will expect to return an array. It’ll look for a
 welcome key on that array, and if it exists, it’ll return its value.

// routes.php

Route::get('/en/welcome', function () {

App::setLocal('en');

return view('welcome');

});


// resources/lang/en/messages.php

return [

'welcome' => 'Welcome to our site!'

];

// resources/views/welcome.blade.php

{{ trans('messages.welcome') }}

#### Parameters in translations

prepending a word with a colon (:name) marks it as a placeholder

that can be replaced. 

// routes.php

Route::get('/en/welcome', function () {

App::setLocal('en');

return view('welcome');

});


// resources/lang/en/messages.php

return [

'welcome' => 'Welcome back, :name!'

];

// resources/views/welcome.blade.php

{{ trans('messages.welcome', ['name' => 'Jose']) }}

#### Pluralization in localization using trans_choice() helper method.
Dening a simple translation with an option for pluralization

// resources/lang/en/messages.php

return [

'task-deletion' => 'You have deleted a task|You have succesfully deleted tasks'

];

// resources/views/dashboard.blade.php

@if ($numTasksDeleted > 0)

{{ trans_choice('messages.task-deletion', $numTasksDeleted) }}

@endif

#### An example of Symfony’s Translation component

// resources/lang/es/messages.php

return [

'task-deletion' => "{0} You didn't manage to delete any tasks.|[1,4] You deleted a few tasks.|[5,I
];

# Collecting and Handling User Data

## the Request façade
The Request façade actualy exposes the entire Illuminate HTTP
 request object.
It

gives easy access to all of the ways users can give input to your site: POST, posted

JSON, GET (query parameters), and URL segments.

### Request::all()
Request::all() gives you an array containing all of the
 input the user has provided, from every source. 
Request::all()

// GET route form view at /get-route

<form method="post" action="/post-route?utm=12345">

{{ csrf_field() }}

<input type="text" name="firstName">

<input type="submit">

</form>

// POST route at /post-route

var_dump(Request::all());

// Outputs:

/**

* [

* '_token' => 'CSRF token here',

* 'firstName' => 'value',

* 'utm' => 12345

* ]

*/

### Request::except()

// POST route at /post-route

var_dump(Request::except('_token'));

// Outputs:

/**

* [

* 'firstName' => 'value',

* 'utm' => 12345

* ]

*/

### Request::only()

// POST route at /post-route

var_dump(Request::only(['firstName', 'utm']));
// Outputs:

/**

* [

* 'firstName' => 'value',

* 'utm' => 12345

* ]

*/

### Request::has()

// POST route at /post-route

if (Request::has('utm')) {

// Do some analytics work

}


### Request::exists
is the same as Request::has, exist it will return TRUE if the key
 exists.

### Request::input
allows you to get the value of
 just a single field. Note that the second parameter is the default value, so if the user
 hasn’t passed in a value, you can have a sensible (and non-breaking) fallback.
Request::input()

// POST route at /post-route

$userName = Request::get('name', '(anonymous)');

### Dot notation to access array values in user data

// GET route form view at /get-route

<form method="post" action="/post-route">

{{ csrf_field() }}

<input type="text" name="employees[0][firstName]">
<input type="text" name="employees[0][lastName]">

<input type="text" name="employees[1][firstName]">

<input type="text" name="employees[1][lastName]">

<input type="submit">

</form>

// POST route at /post-route

$employeeZeroFirstName = Request::input('employees.0.firstName');

$allLastNames = Request::input('employees.*.lastName');

$employeeOne = Request::input('employees.1');

// If forms filled out as "Jim" "Smith" "Bob" "Jones":

// $employeeZeroFirstName = 'Jim';

// $allLastNames = ['Smith', 'Jones'];

// $employeeOne = ['firstName' => 'Bob', 'lastName' => 'Jones']
### Getting data from JSON with Request::input
 
POST /post-route HTTP/1.1

Content-Type: application/json

{"firstName":"Joe","lastName":"Schmoe","spouse":{"firstName":"Jill","lastName":"Schmoe"}}

// post-route

$firstName = Request::input('firstName');

$spouseFirstname = Request::input('spouse.firstName');

### Request::json
Since Request::input is smart enough to pull user data from GET, POST, or JSON,

why would we even worry about using Request::json to get that data? There are

two possible reasons: First, to be more explicit to other programmers on your project

about where you’re expecting the data to come from. And second, if the POST doesnt
have the correct application/json headers, Request::input won’t pick it up as

JSON, but Request::json will.

### Façade namespaces, the request() global helper, and depencency injecting

Any time you’re using Façades inside of namespaced classes (e.g. controllers), you’ll

have to add the full Façade path to the import block at the top of your file (e.g. use

Illuminate\Support\Facades\Request).

Because of this, several of the Façades also have a companion that’s a global helper

function. Almost all provide two functions: First, if they’re run with no parameter,

they expose the same syntax as the façade (e.g. request()→has() is the same as

Request::has()), and second, they have a default behavior for when you pass them a

parameter (e.g. request('firstName') is a shortcut to request()→input('first

Name')).

Finally, with Request, you can also inject an instance of the Request object (learn

more in ???) into any controller method or Route Closure. Just typehint Illuminate

\Http\Request and you can then use all these same methods on that object instead—

e.g. $request→all() instead of Request::all(). Here’s what that typehint might look

like:

Route::post('form', function (Illuminate\Http\Request $request) {

var_dump($request->all());

});

### route data (URLs, route parameters, request objects.)
#### URLs  Request::seg
ments()
each group of characters

between / in a URL is called a segment. So, http://www.myapp.com/users/15/ has

two segments: users and 15.
Request::seg
ments() returns an array of all segments, and Request::segment($segmentId)

allows you to get the value of a single segment. Note that segments are returned on a

1-based index, so in the example above, Request::segment(1) would return users.

#### Getting URL details from route parameters

// routes.php

Route::get('users/{id}', function ($id) {

// If the user visits myapp.com/users/15/, $id will equal 15

});

### dealing with file uploads.
<form method="post" enctype="multipart/form-data">

{{ csrf_field() }}

<input type="text" name="name">

<input type="file" name="profile_picture">

<input type="submit">

</form>

// In controller/route Closure

var_dump(Request::all());

// Output:

// [

// "_token" => "token here"

// "name" => "asdf"

// "profile_picture" => UploadedFile {}

// ]

if (Request::hasFile('profile_picture')) {

var_dump(Request::file('profile_picture'));

}

// Output:

// UploadedFile (details)

#### Validating a file upload
if (

Request::hasFile('profile_picture') &&

Request::file('profile_picture')->isValid()

) {

//

}
#### UploadedFile class 
The UploadedFile class extends PHP’s native SplFileInfo with methods allowing

you to easily inspect and manipulate the file. This list isn’t exhaustive, but can give

you a taste of what you can do:

• guessExtension()

• getMimeType()

• move($directory, $newName = null)

• getClientOriginalName()
• getClientOriginalExtension()

• getClientMimeType()

• guessClientExtension()

• getClientSize()

• getError()

• isValid()


#### Common le upload workow

if (Request::hasFile('profile_picture')) {

$file = Request::file('profile_picture');

if (! $file->isValid()) {

// handle invalid state; likely redirect with an error message

}

$newFileName = Str::random(32) . '.' . $file->guessExtension();

$file->move('profile_picture_path_here', $newFileName);

Auth::user()->profile_picture = $newFileName;

Auth::user()->save();

}

#### <form method="post" enctype="multipart/form-data">
If you get null when you run Request::file, you might’ve forgot‐

ten to set the encoding type on your form. Make sure to add the

property enctype="multipart/form-data" on your form.

<form method="post" enctype="multipart/form-data">

## Validation
### Basic usage of controller validation

// app/Http/routes.php

<?php

Route::get('recipes/create', 'RecipesController@create');

Route::post('recipes', 'RecipesController@store');

// app/Http/Controllers/RecipesController.php

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class RecipesController extends Controller

{

public function create()

{

return view ('recipes.create');

}

public function store(Request $request)

{

$this->validate($request, [

'title' => 'required|unique:recipes|max:125',

'body' => 'required'

]);

// Recipe is valid; proceed to save it

}

}

### more on Laravel's validation rules
In our examples here (and in the docs) we’re using the “pipe” syn‐

tax: 'fieldname': 'rule|otherRule|anotherRule'. But you can

also use the array syntax to do the same thing: 'fieldname':

['rule', 'otherRule', 'anotherRule'].

There’s also the option for validating nested properties. These mat‐

ter if you use HTML’s array syntax, which allows you to, for exam‐

ple, have multiple “users” on an HTML form, each of which have a

name and email address. Here’s how you validate that:

$this->validate($request, [

'user.name' => 'required',

'user.email' => 'required|email',

]);

Finally, we don’t have enough space to cover every possible valida‐

tion rule here, but here are a few of the most common rules:

• required

• email

• alpha

• alpha dash

• alpha numeric

• between (date)

• exists (database)

• integer

• min

• max

• required if

• required unless

• same

• size

• unique (database)

### Manual validation

Route::get('recipes/create', function () {

return view ('recipes.create');

});

Route::post('recipes', function (Illuminate\Http\Request $request) {

$validator = Validator::make($request->all(), [

'title' => 'required|unique:recipes|max:125',

'body' => 'required'

]);

if ($validator->fails()) {

return redirect('recipes/create')

->withErrors($validator)

->withInput();

}

// Recipe is valid; proceed to save it

});

### Displaying validation error messages
Echo validation errors

@if (count($errors) > 0)

<ul id="errors">

@foreach ($errors->all() as $error)

<li>{{ $error }}</li>

@endforeach

</ul>

@endif

## Form Request (a Laravel specific way of dealing with user inputs data)
### creating a Form Request class
You can create a new Form Request using Artisan:

php artisan make:request CreateCommentRequest

You now have a Form Request object available at app/Http/Requests/CreateCommen
tRequest.php.

Sample Form Request

<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateCommentRequest extends Request

{

public function rules()

{

return [

'body' => 'required|max:1000'

];

}

public function authorize()

{

$blogPostId = $this->route('blogPost');

return BlogPost::where('id', $blogPostId)

->where('user_id', Auth::user()->id)->exists();

}

}

### using a form request in route definition
Using a Form Request

Route::post('comments', function (\App\Http\Requests\CreateCommentRequest $request) {

// Store comment

});

You might be wondering where we call the Form Request, but Laravel does it for us. It

validates the user input and authorizes their request. If the input is invalid, it’ll act

just like the in-controller validate method works, redirecting them to the previous

page with their input preserved and with the appropriate error messages passed

along. And if the user is not authenticated, Laravel will return a 403 (Forbidden)

error and not execute the route code.

## Eloquent model mass assignment
It’s a common pattern to pass the entirety of a form’s input directly to a database

model. In Laravel.

Route::post('posts', function () {

$newPost = Post::create(Request::all());

});

Eloquent has a concept called “mass assignment”, which allows you to either whitelist

fields that are fillable in this way (using the model’s $fillable property) or blacklist

fields that aren’t fillable (using the model’s $guarded property). 

Guarding an Eloquent model from mischevious mass assignment

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model

{

protected $guarded = ['author_id'];

}

### preventing script injection using {{ instead of {!!.
Any time you display content on a web page that was created by a user, you need to

guard from script injection.

If you use Laravel’s Blade

templating engine, the default “echo” syntax ({{ $stuffToEcho }}) already runs the

output through htmlentities() (PHP’s best way of making user content safe to

echo) automatically. You actually have to do extra work to get out of it, by using the

{!! $stuffToEcho !!} syntax.

### testing inputs
Testing that invalid input should be rejected

public function test_input_missing_a_title_is_rejected()

{

$this->post('posts', ['body' => 'This is the body of my post']);

$this->assertRedirectedTo('posts/create');

$this->assertSessionHasErrors();

$this->assertHasOldInput();

}

Testing that valid input should be processed

public function test_valid_input_should_create_a_post_in_the_database()

{

$this->post('posts', ['title' => 'Post Title', 'body' => 'This is the body']);

$this->seeInDatabase(['title' => 'Post Title']);

}

# Artisan and Tinker
## basic Artisan commands
### php artisan list
To get a list of all available Artisan commands, you can run php artisan list from the

project root.

### php artisan help commandName.

### php artisan clear
removes Laravel’s compiled class file, which is like an internal Laravel

cache; run this as a first resort when things are going wrong and you don’t know why.

### php artisan down/up
puts your application in “maintenance mode” in order for you to fix an error, run

migrations, or whatever else; up restores an application from maintenance mode.

### php artisan env
displays which environment Laravel is running at the moment; it’s the equivalent of

echoing app()->environment() in-app

### php artisan migrate 
runs all database migrations.

### php artisan optimize
optimizes your application for better performance by caching core PHP classes

into bootstrap/cache/compile.php.

### php artisan serve  --host xxx --port 4000
spins up a PHP server at localhost:8000 (you can customize the host and/or port

with --host and --port).

### php artisan tinker 
brings up the Tinker REPL, which we’ll cover later in this chapter.

#### Options

Before we cover the rest of the Artisan commands, let’s look at a few notable options you can

pass any time you run an Artisan command:

-q suppresses all output.

-v, -vv, and -vvv are the three levels of output verbosity (normal, verbose, and debug).

--no-interaction does not ask any interactive questions, so it won’t interrupt automated

processes running it.

--env allows you to define which environment the Artisan command should operate in

(e.g., local, production, etc.).

--version shows you which version of Laravel your application is running on.

You’ve probably guessed from looking at these options that Artisan commands are intended

to be used much like basic shell commands: you might run them manually, but they can also

function as a part of some automated process at some point.

For example, there are many automated deploy processes that might benefit from certain

Artisan commands. You might want to run php artisan optimize every time you deploy an

application. Flags like -q and --no-interaction ensure that your deploy scripts, not attended

by a human being, can keep running smoothly.

### grouped commands
#### app

This just contains app:name, which allows you to replace every instance of the default

top-level App\ namespace with a namespace of your choosing. For example: php

artisan app:name MyApplication.

#### auth

All we have here is auth:clear-resets, which flushes all of the expired password reset

tokens from the database.

#### cache

cache:clear clears the caches, and cache:table creates a database migration if you plan

to use the database cache driver.

#### config

config:cache caches your configuration settings for faster lookup; to clear the cache,

use config:clear.

#### db

db:seed seeds your database, if you have configured database seeders.

#### event

event:generate builds missing event and event listener files based on the definitions in

EventServiceProvider. We’ll learn more about events in Chapter 16.

#### key

key:generate creates a random application encryption key in your .env file.
     Only run php artisan key:generate once — the first time you set up the application in a new environment 
because this key is used to encrypt your data; if you change it after data has been stored, that data will all
 become inaccessible.
#### make

make:auth scaffolds out the views and corresponding routes for a landing page, a user

dashboard, and login and register pages.

All the rest of the make: actions create a single item, and have parameters that vary

accordingly. To learn more about any individual command’s parameters, use help to

read its documentation.

For example, you could run php artisan help make:migration and learn that you can

pass --create=tableNameHere to create a migration that already has the create table

syntax in the file, as shown here: php artisan make:migration create_posts_table --

create=posts

#### migrate

We saw a migrate command earlier to run our migrations, but here we can run all the

other migration-related commands. Create the migrations table (to keep track of the

migrations that are executed) with migrate:install, reset your migrations and start

from scratch with migrate:reset, reset your migrations and run them all again with

migrate:refresh, roll back just one migration with migrate:rollback, or check the

status of your migrations with migrate:status.

notifications

notifications:table generates a migration that creates the table for database

notifications.

#### queue

We’ll cover Laravel’s queues in Chapter 16, but the basic idea is that you can push jobs

up into remote queues to be executed one after another by a worker. This command

group provides all the tools you need to interact with your queues, like queue:listen to

start listening to a queue, queue:table to create a migration for database-backed queues,

and queue:flush to flush all failed queue jobs. There are quite a few more, which we’ll

learn about in Chapter 16.

#### route

If you run route:list, you’ll see the definitions of every route defined in the

application, including each route’s verb(s), path, name, controller/closure action, and

middleware. You can cache the route definitions for faster lookups with route:cache and

clear your cache with route:clear.

#### schedule

We’ll cover Laravel’s cron-like scheduler in Chapter 16, but in order for it to work, you

need to set the system cron to run schedule:run once a minute:

* * * * * php /home/myapp.com/artisan schedule:run >> /dev/null 2>&1

As you can see, this Artisan command is intended to be run regularly in order to power a

core Laravel service.

#### session

session:table creates a migration for applications using database-backed sessions.

#### storage

storage:link creates a symbolic link from public/storage to storage/app/public. This is

a common convention in Laravel apps, to make it easy to put user uploads (or other files

that commonly end up in storage/app) somewhere where they’ll be accessible at a public

URL.

#### vendor

Some Laravel-specific packages need to “publish” some of their assets, either so that

they can be served from your public directory or so that you can modify them. Either

way, these packages register these “publishable assets” with Laravel, and when you run

vendor:publish, it publishes them to their specified locations.

#### view

Laravel’s view rendering engine automatically caches your views. It usually does a good

job of handling its own cache invalidation, but if you ever notice it’s gotten stuck, run

view:clear to clear the cache.

## writing custom artisan commands
### php artisan
 make:command YourCommandName
generates a new Artisan command in
 app/Console/Commands /{YourCommandName}.php

### The default skeleton of an Artisan command

php artisan make:command WelcomeNewUsers --command=email:newusers
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class WelcomeNewUsers extends Command

{

/**

* The name and signature of the console command.

**

@var string

*/

protected $signature = 'email:newusers';

/**

* The console command description.

**

@var string

*/

protected $description = 'Command description';

/**

* Create a new command instance.

**

@return void

*/

public function __construct()

{

parent::__construct();

} /

**

* Execute the console command.

**

@return mixed

*/

public function handle()

{

//

}

}

### registering custom commands
There’s one step left to make this new command usable in your application: you need to

register it.

Open app/Console/Kernel.php. You’ll see an array of command class names under the

$commands property. To register your new command, add its class to this array. You can write

it out, or just use the ::class class name accessor on the class.
#### registering a new command in the console kernel

class Kernel extends ConsoleKernel

{

/**

* The Artisan commands provided by your application.

**

@var array

*/

protected $commands = [

\App\Console\Commands\WelcomeNewUsers::class,

];

#### WRITING CLOSURE-BASED COMMANDS
If you’d prefer to keep your command definition process simpler, you can write commands as closures instead of

classes by defining them in routes/console.php. Everything we discuss in this chapter will apply the same way, but
 you will just define and register the commands in a single step in that file:

// routes/console.php

Artisan::command(

'password:reset {userId} {--sendEmail}',

function ($userId, $sendEmail) {

// do something...

}

)

#### A sample command 
php artisan make:command WelcomeNewUsers --command=email:newusers
app/Console/Commands/WelcomeNewUsers.php

##### A sample Artisan command handle() method

...

class WelcomeNewUsers extends Command

{

public function handle()

{

User::signedUpThisWeek()->each(function ($user) {

Mail::send(

'emails.welcome',

['name' => $user->name],

function ($m) use ($user) {

$m->to($user->email)->subject('Welcome!');

}

);

});

}
Now every time you run php artisan email:newusers, this command will grab every user

that signed up this week and send them the welcome email.

If you would prefer injecting your mail and user dependencies instead of using facades, you

can typehint them in the command constructor, and Laravel’s container will inject them for

you when the command is instantiated.

##### The same command, refactored
 using dependency
 injection and extracting its behavior out to a service class.
...

class WelcomeNewUsers extends Command

{

public function __construct(UserMailer $userMailer)

{

parent::__construct();

$this->userMailer = $userMailer

} 
public function handle()

{

$this->userMailer->welcomeNewUsers();

}

### Arguments, required, optional, and/or with defaults

protected $signature = 'password:reset {userId} {--sendEmail}';

To define a required argument, surround it with braces:

password:reset {userId}

To make the argument optional, add a question mark:

password:reset {userId?}

To make it optional and provide a default, use:

password:reset {userId=1}

Options, required values, value defaults, and shortcuts

Options are similar to arguments, but they’re prefixed with -- and can be used with no value.

To add a basic option, surround it with braces:

password:reset {userId} {--sendEmail}

If your option requires a value, add an = to its signature:

password:reset {userId} {--password=}

And if you want to pass a default value, add it after the =:

password:reset {userId} {--queue=default}

Array arguments and array options

Both for arguments and for options, if you want to accept an array as input, use the *

character:

password:reset {userIds*}

password:reset {--ids=*}

#### Using array syntax with Artisan commands

// Argument

php artisan password:reset 1 2 3

// Option

php artisan password:reset --ids=1 --ids=2 --ids=3

#### ARRAY ARGUMENTS MUST BE THE LAST ARGUMENT

Since an array argument captures every parameter after its definition and adds them as array items, an array

argument has to be the last argument or option within an Artisan command’s signature.

#### Defining description text for Artisan arguments and options
: adding a colon : in the braces.
protected $signature = 'password:reset

{userId : The ID of the user}

{--sendEmail : Whether to send user an email}';

### getting artisan command's inputs using argument() and option()
#### arguments()
$this->argument() with no parameters returns an array of all arguments (the first array item

will be the command name). With a parameter passed, it’ll return the value of the argument

specified:

// with definition "password:reset {userId}":

php artisan password:reset 5

// $this->argument() returns this array

[

"command": "password:reset",

"userId': "5",

] /

/ $this->argument('userId') returns this string

"5"

#### option()
$this->option() with no parameters returns an array of all options, including some that will

by default be false or null. With a parameter, it’ll return the value of the option specified:

// with definition "password:reset {--userId=}":

php artisan password:reset --userId=5

// $this->option() returns this array

[

"userId" => "5"

"help" => false

"quiet" => false

"verbose" => false

"version" => false

"ansi" => false

"no-ansi" => false

"no-interaction" => false

"env" => null

] //

$this->option('userId') returns this string

"5

#### using argument() and option() in handle() function
public function handle()

{

// All arguments, including the command name

$arguments = $this->argument();

// Just the 'userId' argument

$userid = $this->argument('userId');

// All options, including some defaults like 'no-interaction' and 'env'

$options = $this->option();

// Just the 'sendEmail' option

$sendEmail = $this->option('sendEmail');

}

### getting artisan command input from command prompts
There are a few more ways to get user input from within your handle() code, and they all

involve prompting the user to enter information during the execution of your command:

ask()

Prompts the user to enter freeform text:

$email = $this->ask('What is your email address?');

secret()

Prompts the user to enter freeform text, but hides the typing with asterisks:

$password = $this->ask('What is the DB password?');

confirm()

Prompts the user for a yes/no answer, and returns a boolean:

if ($this->confirm('Do you want to truncate the tables?')) {

//

}

All answers except y or Y will be treated as a “no.”

anticipate()

Prompts the user to enter freeform text, and provides autocomplete suggestions. Still

allows the user to type whatever she wants:

$album = $this->anticipate('What is the best album ever?', [

"The Joshua Tree", "Pet Sounds", "What's Going On"

]);

choice()

Prompts the user to choose one of the provided options. The last parameter is the default

if the user doesn’t choose:

$winner = $this->choice(

'Who is the best football team?',

['Gators', 'Wolverines'],

0

);

Note that the final parameter, the default, should be the array key. Since we passed a

nonassociative array, the key for “Gators” is 0. You could also key your array, if you’d

prefer:
$winner = $this->choice(

'Who is the best football team?',

['gators' => 'Gators', 'wolverines' => 'Wolverines'],

'gators'

);

### artisan command output 
During the execution of your command, you might want to write messages to the user. The

most basic way to do this is to use $this->info() to output basic green text:

$this->info('Your command has run successfully.');

You also have available the comment() (orange), question() (highlighted teal), error()

(highlighted red), and line() (uncolored) methods to echo to the command line.

#### Outputting tables with Artisan commands

$headers = ['Name', 'Email'];

$data = [

['Dhriti', 'dhriti@amrit.com'],

['Moses', 'moses@gutierez.com']

];


// Or, you could get similar data from the database:

// $data = App\User::all(['name', 'email'])->toArray();


$this->table($headers, $data);

Sample output of an Artisan table

+---------+--------------------+

| Name | Email |

+---------+--------------------+

| Dhriti | dhriti@amrit.com |

| Moses | moses@gutierez.com |

+---------+--------------------+

#### outputting progress bar with Artisan commands
Sample Artisan progress bar

$totalUnits = 10;

$this->output->progressStart($totalUnits);

for ($i = 0; $i < $totalUnits; $i++) {

sleep(1);

$this->output->progressAdvance();

} $

this->output->progressFinish();

First, we informed the system how many “units” we needed to work

through. Maybe a unit is a user, and you have 350 users. The bar will then divide the entire

width it has available on your screen by 350, and increment it by 1/350th every time you run

progressAdvance(). Once you’re done, run progressFinish() so it knows it’s done

displaying the progress bar.

## Calling Artisan Commands in Normal Code
The easiest way is to use the Artisan facade. You can either call a command using

Artisan::call() (which will return the command’s exit code), or queue a command using

Artisan::queue().

#### Calling Artisan commands from other code

Route::get('test-artisan', function () {

$exitCode = Artisan::call('password:reset', [

'userId' => 15, '--sendEmail' => true

]);

});

As you can see, arguments are passed by keying to the argument name, and options with no

value can be passed true or false.

You can also call Artisan commands from other commands, using $this->call, (which is the

same as Artisan::call(), or $this->callSilent, which is the same but suppresses all

output). 
#### Calling Artisan commands from other Artisan commands

public function handle()

{

$this->callSilent('password:reset', [

'userId' => 15

]);

}
#### F
inally, you can inject an instance of the Illuminate\Contracts\Console\Kernel contract,

and use its call() method.

## Tinker the REPL (read-eval-print-loop) tool for laravel.
是一个 REPL (read-eval-print-loop)，REPL 指的是一个简单的、可交互式的编程环境，通过执行用户输入的命令，并将执行结果直接打印到命令行界面上来完成整个操作。
$ php artisan tinker 
crtl + c  退出

### Using Tinker

php artisan tinker

>>> $user = new App\User;

=> App\User: {}

>>> $user->email = 'matt@mattstauffer.co';

=> "matt@mattstauffer.co"

>>> $user->password = bcrypt('superSecret');

=> "$2y$10$TWPGBC7e8d1bvJ1q5kv.VDUGfYDnE9gANl4mleuB3htIY2dxcQfQ5"

>>> $user->save();

=> true

we created a new user, set some data, and saved it to the database.

### 通过提下命令轻松创建一个用户对象:
>>> App\Models\User::create(['name'=>'zp',
'email'=>'1356660191@qq.com','password'=>bcrypt('123456')])

### Psy Shell 
Tinker is powered by Psy Shell, so check that out to see what else you can do with Tinker.

## The Artisan facade or the Illuminate\Contracts\Console\Kernel contract instance injection
provides access to the Illuminate\Contracts\Console\Kernel contract,

so if you want to avoid using the facade in your code, you can instead inject an instance of that

and use its call() method, 

### Injecting the kernel instead of using the Artisan facade

use Illuminate\Contracts\Console\Kernel;

...

class NightlyCleanup extends Job

{

...

public function handle(Kernel $kernel)

{

// ... do other stuff

$kernel->call('logs:empty');

}

# Database and Eloquent

## using other database connections other than the default one.
With any service in Laravel that allows multiple “connections” — sessions can be backed by

the database or file storage, the cache can use Redis or Memcached, databases can use MySQL

or PostgreSQL — you can define multiple connections and also choose that a particular

connection will be the “default,” meaning it will be used any time you don’t explicitly ask for

a particular connection. Here’s how you ask for a specific connection, if you want to:


$users = DB::connection('secondary')->select('select * from users');

## Migration --define your database structure with codedriven migrations. 

### Laravel’s default “create users table” migration

<?php

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration

{

/**

* Run the migrations.

**

@return void

*/

public function up()

{

Schema::create('users', function (Blueprint $table) {

$table->increments('id');

$table->string('name');

$table->string('email')->unique();

$table->string('password', 60);

$table->rememberToken();

$table->timestamps();

});

} /

**

* Reverse the migrations.

**

@return void

*/

public function down()

{

Schema::drop('users');

}

}

up() tells the migration to
 create a new table named users with a few fields, and down() tells it to drop the users table.

### creating a migration using: php artisan make:migration the/name/of/the/migration
There are two flags you can optionally pass to this command. --create=table_name prefills

the migration with code designed to create a table named table_name, and --

table=_table_name_ just prefills the migration for modifications to an existing table. 

php artisan make:migration create_users_table

php artisan make:migration add_votes_to_users_table --table=users

php artisan make:migration create_users_table --create=users

#### creating a table
Schema::create('tablename', function (Blueprint $table) {

// Create columns here

});

#### creating columns
Schema::create('users', function (Blueprint $table) {

$table->string('name');

});

##### the simple field Blueprint methods:

integer(colName), tinyInteger(colName), smallInteger(colName),

mediumInteger(colName), bigInteger(colName)

Adds an INTEGER type column, or one of its many variations


string(colName, OPTIONAL length)

Adds a VARCHAR type column


binary(colName)

Adds a BLOB type column


boolean(colName)

Adds a BOOLEAN type column (a TINYINT(1) in MySQL)


char(colName, length)

Adds a CHAR column


datetime(colName)

Adds a DATETIME column


decimal(colName, precision, scale)

Adds a DECIMAL column, with precision and scale — e.g., decimal('amount', 5, 2)

specifies a precision of 5 and a scale of 2


double(colName, total digits, digits after decimal)

Adds a DOUBLE column — e.g., double('tolerance', 12, 8) specifies 12 digits long,

with 8 of those digits to the right of the decimal place, as in 7204.05691739


enum(colName, [choiceOne, choiceTwo])

Adds an ENUM column, with provided choices


float(colName)

Adds a FLOAT column (same as double in MySQL)


json(colName) and jsonb(colName)

Adds a JSON or JSONB column (or a TEXT column in Laravel 5.1)


text(colName), mediumText(colName), longText(colName)

Adds a TEXT column (or its various sizes)

time(colName)

Adds a TIME column


timestamp(colName)

Adds a TIMESTAMP column


uuid(colName)

Adds a UUID column (CHAR(36) in MySQL)


##### And these are the special (joined) Blueprint methods:

increments(colName) and bigIncrements(colName)

Add an unsigned incrementing INTEGER or BIG INTEGER primary key ID


timestamps() and nullableTimestamps()

Adds created_at and updated_at timestamp columns


rememberToken()

Adds a remember_token column (VARCHAR(100)) for user “remember me” tokens


softDeletes()

Adds a deleted_at timestamp for use with soft deletes


morphs(colName)

For a provided +colName+, adds an integer colName_id and a string colName_type (e.g.,

morphs('tag') adds integer tag_id and string tag_type); for use in polymorphic

relationship

##### Building fileds' extra properties
Schema::table('users', function (Blueprint $table) {

$table->string('email')->nullable()->after('last_name');

});

###### The following methods are used to set additional properties of a field:

nullable()

Allows NULL values to be inserted into this column


default('default content')

Specifies the default content for this column if no value is provided


unsigned()

Marks integer columns as unsigned


first() (MySQL only)

Places the column first in the column order


after(colName) (MySQL only)

Places the column after another column in the column order


unique()

Adds a UNIQUE index


primary()

Adds a primary key index


index()

Adds a basic index

Note that unique(), primary(), and index() can also be used outside of the fluent column

building context.

#### Dropping tables
Schema::drop('contacts');

#### Modifying columns

To modify a column, just write the code you would write to create the column as if it were

new, and then append a call to the change() method after it.
##### REQUIRED DEPENDENCY BEFORE MODIFYING COLUMNS

Before you modify any columns (or drop any columns in SQLite), you’ll need to add the 
doctrine/dbal package
 as a requirement in your composer.json,
and run composer update to bring it in.

Schema::table('users', function ($table) {

$table->string('name', 100)->change();

});

Schema::table('contacts', function ($table) {

$table->string('deleted_at')->nullable()->change();

});

Schema::table('contacts', function ($table)

{

$table->renameColumn('promoted', 'is_promoted');

});

Schema::table('contacts', function ($table)

{

$table->dropColumn('votes');

});

##### MODIFYING MULTIPLE COLUMNS AT ONCE in one migration
If you try to drop or modify multiple columns within a single migration closure and you are using SQLite, you’ll
 run into errors.

you don’t have to create a new migration for each. Instead, just create multiple calls to

Schema::table() within the up() method of your migration:

public function up()

{

Schema::table('contacts', function (Blueprint $table)

{

$table->dropColumn('is_promoted');

});

Schema::table('contacts', function (Blueprint $table)

{

$table->dropColumn('alternate_email');

});

}

### indexes and foreign keys
#### Adding column indexes in migrations

// after columns are created...

$table->primary('primary_id'); // Primary key; unnecessary if used increments()

$table->primary(['first_name', 'last_name']); // Composite keys

$table->unique('email'); // Unique index

$table->unique('email', 'optional_custom_index_name'); // Unique index

$table->index('amount'); // Basic index

$table->index('amount', 'optional_custom_index_name'); // Basic index

#### Removing column indexes in migrations

$table->dropPrimary('contacts_id_primary');

$table->dropUnique('contacts_email_unique');

$table->dropIndex('optional_custom_index_name');

// If you pass an array of column names to dropIndex, it will

// guess the index names for you based on the generation rules

$table->dropIndex(['email', 'amount']);

#### Adding and removing foreign keys

To add a foreign key that defines that a particular column references a column on another

table, Laravel’s syntax is simple and clear:

$table->foreign('user_id')->references('id')->on('users');
Here we’re adding a foreign index on the user_id column, showing that it references the id

column on the users table. 

##### If we want to specify foreign key constraints, we can do that too, with onDelete() and

onUpdate(). For example:

$table->foreign('user_id')

->references('id')

->on('users')

->onDelete('cascade');

##### To drop an index, we can either delete it by referencing its index name (which is

automatically generated by combining the names of the columns and tables being referenced):

$table->dropForeign('contacts_user_id_foreign');

or by passing it an array of the fields that it’s referencing on the local table:

$table->dropForeign(['user_id']);

### php artisan migrate
This command runs all “outstanding” migrations. Laravel keeps track of which migrations

you have run and which you haven’t.

#### php artisan migrate --seed

#### php artisan migrate:XXXX
You can also run any of the following commands:


migrate:install 
creates the database table that keeps track of which migrations you

have and haven’t run; this is run automatically when you run your migrations.


migrate:reset
rolls back every database migration you’ve run on this install.


migrate:refresh
rolls back every database migration you’ve run on this install, and then

runs every migration available. It’s the same as running migrate:reset and then migrate,

one after the other.


migrate:rollback
rolls back just the migrations that ran the last time you ran migrate,

or, with the added option --step=1, rolls back the number of migrations you specify.


migrate:status
shows a table listing every migration, with a Y or N next to each showing

whether or not it has run yet in this environment.

## seeding 
### to run a seeder
1. There are two primary ways to run the seeders: along with a migration, or separately.

To run a seeder along with a migration, just add --seed to any migration call:

php artisan migrate --seed

php artisan migrate:refresh --seed


2. And to run it independently:

php artisan db:seed

php artisan db:seed --class=VotesTableSeeder

This will run whatever you have defined in the run() methods of every seeder class (or just

the class you passed to --class).

### creating a seeder  
1. php artisan make:seeder ContactsTableSeeder
You’ll now see a ContactsTableSeeder class show up in the database/seeds directory. 

2. Before
 we edit it, let’s add it to the DatabaseSeeder class so it will run when we run our seeders:

// database/seeds/DatabaseSeeder.php

...

public function run()

{

$this->call(ContactsTableSeeder::class);

}

3. Now let’s edit the seeder itself. The simplest thing we can do there is manually insert a record

using the DB facade:

<?php

use Illuminate\Database\Seeder;

use Illuminate\Database\Eloquent\Model;

class ContactsTableSeeder extends Seeder

{

public function run()

{

DB::table('contacts')->insert([

'name' => 'Lupita Smith'

'email' => 'lupita@gmail.com',

]);

}

}

### Model factories
#### creating a model factory
Model factories are defined in database/factories/ModelFactory.php. Each factory has a name

and a definition of how to create a new instance of the defined class. The $factory->define()

method takes the factory name as the first parameter and a closure that’s run for each

generation as the second parameter.
1. defining the factory in database/factories/ModelFactory.php.

$factory->define(Contact::class, function (Faker\Generator $faker) {

return [

'name' => 'Lupita Smith',

'email' => 'lupita@gmail.com',

];

});

$factory->define(Contact::class, function (Faker\Generator $faker) {

return [

'name' => $faker->name,

'email' => $faker->email,

];

});

$factory->define(User::class, function (Faker\Generator $faker) {

return [

'name' => $faker->name,

];

});

$factory->define('users', function (Faker\Generator $faker) {

return [

'name' => $faker->name,

];

});

2. call the factory()
Now we can use the factory() global helper to create an instance of Contact in our seeding

and testing:

// Create one

$contact = factory(Contact::class)->create();

// Create many

factory(Contact::class, 20)->create();

#### using a model factory
There are two primary contexts in which we’ll use model factories: testing and seeding.
Using model factories

factory(Post::class)->create([

'title' => 'My greatest post ever'

]);


factory(User::class, 20)->create()->each(function ($u) use ($post) {

$post->comments()->save(factory(Comment::class)->make([

'user_id' => $u->id

]));

});

make() or
 create().

Both methods generate an instance of this class, using the definition in modelFactory.php. The

difference is that make() creates the instance but doesn’t (yet) save it to the database, whereas

create() saves it to the database instantly.

#### Defining multiple factory types for the same model

$factory->define(Contact::class, function (Faker\Generator $faker) {

return [

'name' => $faker->name,

'email' => $faker->email,

];

});

$factory->defineAs(Contact::class, 'vip', function (Faker\Generator $faker) {

return [

'name' => $faker->name,

'email' => $faker->email,

'vip' => true,

];

});

#### Extending a factory type

$factory->define(Contact::class, function (Faker\Generator $faker) {

return [

'name' => $faker->name,

'email' => $faker->email,

];

});


$factory->defineAs(

Contact::class,

'vip',

function (Faker\Generator $faker) use ($factory) {

$contact = $factory->raw(Contact::class);

return array_merge($contact, ['vip' => true]);

});


Now, let’s make a specific type:

$vip = factory(Contact::class, 'vip')->create();

$vips = factory(Contact::class, 'vip', 3)->create();


## query builder
At the core of every piece of Laravel’s database functionality is
 the query builder, a fluent interface for interacting with your database.

A fluent interface is one that primarily uses method chaining to provide a simpler API to the end user. Rather than

expecting all of the relevant data to be passed into either a constructor or a method call, fluent call chains can be built

gradually, with consecutive calls. Consider this comparison:

// Non-fluent:

$users = DB::select(['table' => 'users', 'where' => ['type' => 'donor']]);

// Fluent:

$users = DB::table('users')->where('type', 'donor')->get();

### Basic Usage of the DB Facade
he DB facade is used both for query builder chaining and
 for simpler raw queries.
Sample raw SQL and query builder usage

// basic statement

DB::statement('drop table users')

// raw select, and parameter binding

DB::select('select * from contacts where validated = ?', [true]);

// select using the fluent builder

$users = DB::table('users')->get();

// joins and other complex calls

DB::table('users')

->join('contacts', function ($join) {

$join->on('users.id', '=', 'contacts.user_id')

->where('contacts.type', 'donor');

})

->get();

### Raw SQL
1. the DB
 facade and the statement() method: DB::statement('SQL statement here').

2.  there are also specific methods for various common actions: select(), insert(),

update(), and delete(). These are still raw calls, but there are differences. First, using

update() and delete() will return the number of rows affected, whereas statement() won’t;

second, with these methods it’s clearer to future developers exactly what sort of statement

you’re making.

#### Raw selects

The simplest of the specific DB methods is select(). You can run it without any additional

parameters:

$users = DB::select('select * from users');


This will return a collection of stdClass objects.Collection is like a PHP array with superpowers, allowing you to run map(), filter(), reduce(), each(), and much
 more on your data. 
The DB facade returns an instance of
 Illuminate\Support\Collection and Eloquent returns an instance of Illuminate\Database\Eloquent\Collection,
 which extends Illuminate\Support\Collection with a few Eloquent-specific methods.

#### Parameter bindings and named bindings

Laravel’s database architecture allows for the use of PDO parameter binding, which protects

your queries from potential SQL attacks. Passing a parameter to a statement is as simple as

replacing the value in your statement with a ?, then adding the value to the second parameter

of your call:

$usersOfType = DB::select(

'select * from users where type = ?',

[$type]

);

You can also name those parameters for clarity:

$usersOfType = DB::select(

'select * from users where type = :type',

['type' => $userType]

);


#### Raw inserts

From here, the raw commands all look pretty much the same. Raw inserts look like this:

DB::insert(

'insert into contacts (name, email) values (?, ?)',

['sally', 'sally@me.com']

);


#### Raw updates

Updates look like this:

$countUpdated = DB::update(

'update contacts set status = ? where id = ?',

['donor', $id]

);


#### Raw deletes

And deletes look like this:

$countDeleted = DB::delete(

'delete from contacts where archived = ?',

[true]

);

### Chaining with the Query Builder
The methods can be
 split up into what I’ll call constraining methods, modifying methods, and ending/returning

methods.
#### Constraining methods

These methods take the query as it is and constrain it to return a smaller subset of possible

data:


1. select()

Allows you to choose which columns you’re selecting:

$emails = DB::table('contacts')

->select('email', 'email2 as second_email')

->get();

// Or

$emails = DB::table('contacts')

->select('email')

->addSelect('email2 as second_email')

->get();


2. where()

Allows you to limit the scope of what’s being returned using WHERE. By default, the

signature of the where() method is that it takes three parameters — the column, the

comparison operator, and the value:

$newContacts = DB::table('contact')

->where('created_at', '>', Carbon::now()->subDay())

->get();

However, if your comparison is =, which is the most common comparison, you can drop

the second operator: $vipContacts = DB::table('contacts')->where('vip',true)-

>get();.

If you want to combine where() statements, you can either chain them after each other, or

pass an array of arrays:

$newVips = DB::table('contacts')

->where('vip', true)

->where('created_at', '>', Carbon::now()

->subDay());

// Or

$newVips = DB::table('contacts')->where([

['vip', true],

['created_at', '>', Carbon::now()->subDay()],

]);


3. orWhere()

Creates simple OR WHERE statements:

$priorityContacts = DB::table('contacts')

->where('vip', true)

->orWhere('created_at', '>', Carbon::now()->subDay())

->get();

To create a more complex OR WHERE statement with multiple conditions, pass orWhere()

a closure:

$contacts = DB::table('contacts')

->where('vip', true)

->orWhere(function ($query) {

$query->where('created_at', '>', Carbon::now()->subDay())

->where('trial', false);

})

->get();

##### POTENTIAL CONFUSION WITH MULTIPLE WHERE AND
 ORWHERE CALLS
If you are using orWhere() calls in conjunction with multiple where() calls, you need to be very careful to ensure

the query is doing what you think it is. This isn’t because of any fault with Laravel, but because a query like the

following might not do what you expect:


$canEdit = DB::table('users')

->where('admin', true)

->orWhere('plan', 'premium')

->where('is_plan_owner', true)

->get();


SELECT * FROM users

WHERE admin = 1

OR plan = 'premium'

AND is_plan_owner = 1;


If you want to write SQL that says “if this OR (this and this),” which is clearly the intention in the previous
 example, you’ll want to pass a closure into the orWhere() call:


$canEdit = DB::table('users')

->where('admin', true)

->orWhere(function ($query) {

$query->where('plan', 'premium')

->where('is_plan_owner', true);

})

->get();


SELECT * FROM users

WHERE admin = 1

OR (plan = 'premium' AND is_plan_owner = 1);

4. whereBetween(colName, [low, high])

Allows you to scope a query to return only rows where a column is between two values

(inclusive of the two values):

$mediumDrinks = DB::table('drinks')

->whereBetween('size', [6, 12])

->get();


5. The same works for whereNotBetween(), but it will select the inverse.

whereIn(colName, [1, 2, 3])

Allows you to scope a query to return only rows where a column is in an explicitly

provided list of options: $closeBy = DB::table('contacts')->whereIn(state, [FL,

GA, AL])->get().

$closeBy = DB::table('contacts')->whereIn('state', ['FL', 'GA', 'AL'])->get()


6. The same works for whereNotIn(), but it will select the inverse.

whereNull(colName) and whereNotNull(colName)

Allow you to select only rows where a given column is NULL or is NOT NULL, respectively.


7. whereRaw()

Allows you to pass in a raw, unescaped string to be added after the WHERE statement:

$goofs = DB::table('contacts')->whereRaw('id = 12345')->get()

##### BEWARE OF SQL INJECT ION using whereRaw()
Any SQL queries passed to whereRaw() will not be escaped. Use this method carefully and infrequently; this is the

prime opportunity for SQL injection attacks in your app.

8. whereExists()

Allows you to select only rows that, when passed into a provided subquery, return at least

one row. Imagine you only want to get those users who have left at least one comment:

$commenters = DB::table('users')

->whereExists(function ($query) {

$query->select('id')

->from('comments')

->whereRaw('comments.user_id = users.id');

})

->get();


9. distinct()

Selects only distinct rows. Usually this is paired with select(), because if you use a

primary key, there will be no duplicated rows: $lastNames = DB::table('contacts')-

>select('last_name')->distinct()->get().

#### Modifying methods
These methods change the way the query’s results will be output, rather than just limiting its

results:

1. orderBy(colName, direction)

Orders the results. The second parameter may be either asc (the default) or desc:

$contacts = DB::table('contacts')

->orderBy('last_name', 'asc')

->get();


2. groupBy() and having() or havingRaw()

Groups your results by a column. Optionally, having() and havingRaw() allow you to

filter your results based on properties of the groups. For example, you could look for

only cities with at least 30 people in them:

$populousCities = DB::table('contacts')

->groupBy('city')

->havingRaw('count(contact_id) > 30')

->get();


3. skip() and take()

Most often used for pagination, these allow you to define how many rows to return and

how many to skip before starting the return — like a page number and a page size in a

pagination system:

$page4 = DB::table('contacts')->skip(30)->take(10)->get();


4. latest(colName) and oldest(colName)

Sort by the passed column (or created_at if no column name is passed) in descending

(latest()) or ascending (oldest()) order.


5. inRandomOrder()

Sorts the result randomly

#### Ending/returning methods

These methods stop the query chain and trigger the execution of the SQL query:


1. get()

Gets all results for the built query:

$contacts = DB::table('contacts')->get();

$vipContacts = DB::table('contacts')->where('vip', true)->get();


2. first() and firstOrFail()

Get only the first result — like get(), but with a LIMIT 1 added:

$newestContact = DB::table('contacts')

->orderBy('created_at', 'desc')

->first();


first()

Fails silently if there are no results, whereas firstOrFail() will throw an exception.

If you pass an array of column names to either method, they’ll return the data for just

those columns instead of all columns.


3. find(id) and findOrFail(id)

Like first(), but you pass in an ID value that corresponds to the primary key to look up.

find() fails silently if a row with that ID doesn’t exist, while findOrFail() will throw

an exception:

$contactFive = DB::table('contacts')->find(5);


4. value()

Plucks just the value from a single field from the first row. Like first(), but if you only

want a single column:

$newestContactEmail = DB::table('contacts')

->orderBy('created_at', 'desc')

->value('email');


5. count()

Returns an integer count of all of the matching results:

$countVips = DB::table('contacts')

->where('vip', true)

->count();


6. min() and max()

Return the minimum or maximum value of a particular column:

$highestCost = DB::table('orders')->max('amount');


7. sum() and avg()

Return the sum or average of all of the values in a particular column:

$averageCost = DB::table('orders')

->where('status', 'completed')

->avg('amount');

#### Writing raw queries inside query builder methods with DB::raw

We’ve already seen a few custom methods for raw statements — for example, select() has a

selectRaw() counterpart that allows you to pass in a string for the query builder to place after

the WHERE statement.

You can also, however, pass in the result of a DB::raw() call to almost any method in the

query builder to achieve the same result:

$contacts = DB::table('contacts')

->select(DB::raw('*, (score * 100) AS integer_score'))

->get();

#### joins
$users = DB::table('users')

->join('contacts', 'users.id', '=', 'contacts.user_id')

->select('users.*', 'contacts.name', 'contacts.status')

->get();


The join() method creates an inner join. You can also chain together multiple joins one after

another, or use leftJoin() to get a left join.


Finally, you can create more complex joins by passing a closure into the join() method:


DB::table('users')

->join('contacts', function ($join) {

$join

->on('users.id', '=', 'contacts.user_id')

->orOn('users.id', '=', 'contacts.proxy_user_id');

})

->get();

#### unions
You can union two queries together by creating them first and then using the union() or

unionAll() method to union them:


$first = DB::table('contacts')

->whereNull('first_name');

$contacts = DB::table('contacts')

->whereNull('last_name')

->union($first)

->get()

#### Inserts

The insert() method is pretty simple. Pass it an array to insert a single row or an array of

arrays to insert multiple rows, and use insertGetId() instead of insert() to get the

autoincrementing primary key ID back as a return:

$id = DB::table('contacts')->insertGetId([

'name' => 'Abe Thomas',

'email' => 'athomas1987@gmail.com',

]);

DB::table('contacts')->insert([

['name' => 'Tamika Johnson', 'email' => 'tamikaj@gmail.com'],

['name' => 'Jim Patterson', 'email' => 'james.patterson@hotmail.com'],

]);


#### Updates

Updates are also simple. Create your update query and, instead of get() or first(), just use

update() and pass it an array of parameters:

DB::table('contacts')

->where('points', '>', 100)

->update(['status' => 'vip']);

You can also quickly increment and decrement columns using the increment() and

decrement() methods. The first parameter of each is the column name, and the second is

(optionally) the number to increment/decrement by:

DB::table('contacts')->increment('tokens', 5);

DB::table('contacts')->decrement('tokens');


#### Deletes

Deletes are even simpler. Build your query and then end it with delete():

DB::table('users')

->where('last_login', '<', Carbon::now()->subYear())

->delete();

You can also truncate the table, which both deletes every row and also resets the

autoincrementing ID:

DB::table('contacts')->truncate();


#### JSON operations

If you have JSON columns, you can update or select rows based on aspects of the JSON

structure by using the arrow syntax to traverse children:

// Select all records where the "isAdmin" property of the "options"

// JSON column is set to true

DB::table('users')->where('options->isAdmin', true)->get();

// Update all records, setting the "verified" property

// of the "options" JSON column to true

DB::table('users')->update(['options->isVerified', true]);

This is a new feature from Laravel 5.3.

#### Transactions   DB::transaction(function ())
database transactions are a tool that allows you to wrap up a
 series of database queries to be performed in a batch, which you can choose to roll back,
 undoing the entire series of queries. Transactions are often used to ensure that all or none, but
 not some, of a series of related queries are performed — if one fails, the ORM will roll back
 the entire series of queries.

A simple database transaction

DB::transaction(function () use ($userId, $numVotes)

{

// Possibly failing DB query

DB::table('users')

->where('id', $userId)

->update(['votes' => $numVotes]);

// Caching query that we don't want to run if the above query fails

DB::table('votes')

->where('user_id', $userId)

->delete();

});

##### manually begin and end transactions: DB::beginTransaction(), DB::commit(), abort with DB::rollBack()
— this applies both for query
 builder queries and for Eloquent queries. Start with DB::beginTransaction(), end with
 DB::commit(), and abort with DB::rollBack().

## Eloquent
### Creating and Defining Eloquent Models : php artisan make:model Contact 
php artisan make:model Contact 
(If you want to automatically create a migration when you create your model, pass the -m or --migration flag:

php artisan make:model Contact --migration)

This command creates a model in app/Contact.php:

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model

{

//

}

#### Table name

The default behavior for table names is that Laravel “snake cases” and pluralizes your class

name, so SecondaryContact would access a table named secondary_contacts. If you’d like to

customize the name, set the $table property explicitly on the model:

protected $table = 'contacts_secondary';

#### Primary key

Laravel assumes, by default, that each table will have an autoincrementing integer primary

key, and it will be named id.

If you want to change the name of your primary key, change the $primaryKey property:

protected $primaryKey = 'contact_id';

And if you want to set it to be nonincrementing, use:

public $incrementing = false;

#### Timestamps

Eloquent expects every table to have created_at and updated_at timestamp columns. If your

table won’t have them, disable the $timestamps functionality:

public $timestamps = false;

You can customize the format Eloquent uses to store your timestamps to the database by

setting the $dateFormat class property to a custom string. The string will be parsed using

PHP’s date() syntax, so the following example will store the date as seconds since the Unix

epoch:

protected $dateFormat = 'U';

### Retrieving Data with Eloquent
Most of the time you pull data from your database with Eloquent, you’ll use static calls on

your Eloquent model.

Let’s start by getting everything:

$allContacts = Contact::all();

Let’s filter it a bit:

$vipContacts = Contact::where('vip', true)->get();

#### everything you
 can do with the query builder on the DB facade you can do on your Eloquent objects.

#### Get one record :first(), firstOrFail(), find(), or findOrFail()
Using an Eloquent OrFail() method in a controller method

// ContactController

public function show($contactId)

{

return view('contacts.show')

->with('contact', Contact::findOrFail($contactId));

} A

ny single return (first(), firstOrFail(), find(), or findOrFail()) will return an

instance of the Eloquent class. 

#### Get many  records: get()  all()
get() works with Eloquent just like it does in normal query builder calls — build a query and

call get() at the end to get the results:


$vipContacts = Contact::where('vip', true)->get();


However, there is an Eloquent-only method, all(), which you’ll often see people use when

they want to get an unfiltered list of all data in the table:


$contacts = Contact::all()

One thing that’s different about Eloquent’s get() method is that, prior to Laravel 5.3, it

returned an array instead of a collection. In 5.3 and later, they both return collections.

#### Chunking responses with chunk()
If you’ve ever needed to process a large amount (thousands or more) of records at a time,

you may have run into memory or locking issues. Laravel makes it possible to break your

requests into smaller pieces (chunks) and process them in batches, keeping the memory load

of your large request smaller. 

Chunking an Eloquent query to limit memory usage

Contact::chunk(100, function ($contacts) {

foreach ($contacts as $contact) {

// Do something with $contact

}

});


#### Aggregates

The aggregates that are available on the query builder are available on Eloquent queries as

well. For example:

$countVips = Contact::where('vip', true)->count();

$sumVotes = Contact::sum('votes');

$averageSkill = User::avg('skill_level');

### Inserts and Updates with Eloquent
#### inserts
Inserting an Eloquent record by creating a new instance

$contact = new Contact;

$contact->name = 'Ken Hirata';

$contact->email = 'ken@hirata.com';

$contact->save();


// or

$contact = new Contact([

'name' => 'Ken Hirata',

'email' => 'ken@hirata.com'

]);

$contact->save();

// or 
Inserting an Eloquent record by passing an array to create()

$contact = Contact::create([

'name' => 'Keahi Hale',

'email' => 'halek481@yahoo.com'

]);

#### updates
Updating an Eloquent record by updating an instance and saving

$contact = Contact::find(1);

$contact->email = 'natalie@parkfamily.com';

$contact->save();

Updating one or more Eloquent records by passing an array to the update()

method

Contact::where('created_at', '<', Carbon::now()->subYear())

->update(['longevity' => 'ancient']);

// or

$contact = Contact::find(1);

$contact->update(['longevity' => 'ancient']);

This method expects an array where each key is the column name and each value is the

column value.

#### Mass assignment
: $fillable or $guarded
We’ve looked at a few examples of how to pass arrays of values into Eloquent class methods.

However, none of these will actually work until you define which fields are “fillable” on the

model.

The goal of this is to protect (malicious) user input from accidentally setting new values on

fields you don’t want changed. 

Using Eloquent’s fillable or guarded properties to define mass-assignable fields

class Contact

{

protected $fillable = ['name', 'email'];

// or

protected $guarded = ['id', 'created_at', 'updated_at', 'owner_id'];

}

Updating an Eloquent model using the entirety of a request’s input

// ContactController

public function update(Contact $contact, Request $request)

{

$contact->update($request->all());

}

Note that nonfillable properties can still be changed by direct

assignment (e.g., $contact->password = 'abc';).

#### firstOrCreate() and firstOrNew()

Sometimes you want to to tell your application, “Get me an instance with these properties, or

if it doesn’t exist, create it.” This is where the firstOr*() methods come in.

The firstOrCreate() and firstOrNew() methods take an array of keys and values as their

first parameter:


$contact = Contact::firstOrCreate(['email' => 'luis.ramos@myacme.com']);

They’ll both look for and retrieve the first record matching those parameters, and if there are

no matching records, they’ll create an instance with those properties; firstOrCreate() will

persist that instance to the database and then return it, while firstOrNew() will return it

without saving it.

### Deleting with Eloquent
#### Normal deletes

The simplest way to delete an instance is to call the delete() method on the instance itself:

$contact = Contact::find(5);

$contact->delete();

However, if you only have the ID, there’s no reason to look up an instance just to delete it;

you can pass an ID or an array of IDs to the model’s destroy() method to delete them

directly:

Contact::destroy(1);

// or

Contact::destroy([1, 5, 7]);

Finally, you can delete all of the results of a query:

Contact::where('updated_at', '<', Carbon::now()->subYear())->delete();

#### Soft deletes
Soft deletes mark database rows as deleted without actually deleting them from the database.

This gives you the ability to inspect them later; to have records that show more than “no

information, deleted” when displaying historic information; and to allow your users (or

admins) to restore some or all data.

Eloquent’s soft delete functionality requires a deleted_at column to be added to the table.

Once you enable soft deletes on that Eloquent model, every query you ever write (unless you

explicitly include soft-deleted records) will be scoped to ignore soft-deleted rows.

##### don’t use soft deletes by default. Instead, use them when you need them, and when you do,

clean out old soft deletes as aggressively as you can. It’s a powerful tool, but not worth using unless you need it.

##### enable soft deletes 
You enable soft deletes by doing three things: 
1. adding the deleted_at column in a migration,

Migration to add the soft delete column to a table

Schema::table('contacts', function (Blueprint $table) {

$table->softDeletes();

});

2. importing the SoftDeletes trait in the model,
An Eloquent model with soft deletes enabled

<?php

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

3. and adding the deleted_at column to your
 $dates property. 
class Contact extends Model

{

use SoftDeletes; // use the trait

protected $dates = ['deleted_at']; // mark this column as a date

}

There’s a softDeletes() method available on the query builder to add the
 deleted_at column to a table.

Once you make these changes, every delete() and destroy() call will now set the deleted_at column on your row to be the current date and time instead of deleting that row. And all future queries will exclude that row as a result.

##### Querying with soft deletes
you can add soft-deleted items to a query:
$allHistoricContacts = Contact::withTrashed()->get();

you can use the trashed() method to see if a particular instance has been soft deleted:
if ($contact->trashed()) {
// do something
}

you can get only soft-deleted items:
$deletedContacts = Contact::onlyTrashed()->get();

##### Restoring soft-deleted entities
If you want to restore a soft-deleted item, you can run restore() on an instance or a query:
$contact->restore();
// or
Contact::onlyTrashed()->where('vip', true)->restore();

##### Force-deleting soft-deleted entities
You can delete a soft-deleted entity by calling forceDelete() on an entity or query:
$contact->forceDelete();
// or
Contact::onlyTrashed()->forceDelete();

## Scopes
Local and global scopes in Eloquent allow you to define prebuilt “scopes” (filters) that you
can use either every time a model is queried (“global”) or every time you query it with a
particular method chain (“local”).

### local scopes
1. $activeVips = Contact::activeVips()->get();

class Contact
{
public function scopeActiveVips($query)
{
return $query->where('vip', true)->where('trial', false);
}
To define a local scope, we add a method to the Eloquent class that begins with “scope” and
then contains the title-cased version of the scope name. This method is passed a query builder
and needs to return a query builder, 

2. You can also define scopes that accept parameters:

class Contact
{
public function scopeStatus($query, $status)
{
return $query->where('status', $status);
}

And you use them in the same way, just passing the parameter to the scope:
$friends = Contact::status('friend')->get();

### global scopes
There are two ways to define a global scope: using a closure or using an entire class. In each,
you’ll register the defined scope in the model’s boot() method. 
1. Adding a global scope using a closure
...
class Contact extends Model
{
protected static function boot()
{
parent::boot();
static::addGlobalScope('active', function (Builder $builder) {
$builder->where('active', true);
});
}

We just added a global scope, named active, and every query on this model will be
scoped to only rows with active set to true.

2. Creating a global scope class 
Create a class that implements Illuminate\Database\Eloquent\Scope, 
which means it will have an apply() method that takes an instance of a query builder and an instance of the model.

<?php
namespace App\Scopes;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ActiveScope implements Scope
{
public function apply(Builder $builder, Model $model)
{
return $builder->where('active', true);
}
}

To apply this scope to a model, once again override the parent’s boot() method and call addGlobalScope() on the class using static.

Applying a class-based global scope
<?php
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Model;
class Contact extends Model
{
protected static function boot()
{
parent::boot();
static::addGlobalScope(new ActiveScope);
}
}

#### Removing global scopes
There are three ways to remove a global scope, and all three use the withoutGlobalScope()
or withoutGlobalScopes() methods. 

1. If you’re removing a closure-based scope, the first parameter of that scope’s addGlobalScope() registration will be the key you used to enable it:
$allContacts = Contact::withoutGlobalScope('active')->get();

2. If you’re removing a single class-based global scope, you can pass the class name to withoutGlobalScope() or withoutGlobalScopes():
Contact::withoutGlobalScope(ActiveScope::class)->get();
Contact::withoutGlobalScopes([ActiveScope::class, VipScope::class])->get();

3. Or, you can just disable all global scopes for a query:
Contact::withoutGlobalScopes()->get();

### Customizing Field Interactions with Accessors, Mutators, and Attribute Casting (Eloquent setter, getter )
#### Accessors ( getters)
Accessors allow you to define custom attributes on your Eloquent models for when you are
reading data from the model instance. This may be because you want to change how a
particular column is output, or because you want to create a custom attribute that doesn’t exist
in the database table at all.
You define an accessor by writing a method on your model with the following structure:
get{PascalCasedPropertyName}Attribute. So, if your property name is first_name, the
accessor method would be named getFirstNameAttribute.

1. Decorating a preexisting column with Eloquent accessors
// Model definition:
class Contact extends Model
{
public function getNameAttribute($value)
{
return $value ?: '(No name provided)';
}
} //
Accessor usage:
$name = $contact->name;

2. Defining an attribute with no backing column using Eloquent accessors
// Model definition:
class Contact extends Model
{
public function getFullNameAttribute()
{
return $this->first_name . ' ' . $this->last_name;
}
} //
Accessor usage:
$fullName = $contact->full_name;

#### mutators ( setters)
Mutators work the same way as accessors, except they’re for determining how to process
setting the data instead of getting it.

You define a mutator by writing a method on your model with the following structure:
set{PascalCasedPropertyName}Attribute. So, if your property name is first_name, the
mutator method would be named setFirstNameAttribute.

1. Decorating setting the value of an attribute with Eloquent mutators

// Defining the mutator
class Order extends Model
{
public function setAmountAttribute($value)
{
$this->attributes['amount'] = $value > 0 ? $value : 0;
}
} //
Using the mutator
$order->amount = '15';

This reveals that the way mutators are expected to “set” data on the model is by setting it in
$this->attributes with the column name as the key.

2. Allowing for setting the value of a nonexistent attribute with Eloquent mutators

// Defining the mutator
class Order extends Model
{
public function setWorkgroupNameAttribute($workgroupName)
{
$this->attributes['email'] = "{$workgroupName}@ourcompany.com";
}
} 
// Using the mutator
$order->workgroup_name = 'jstott';

As you can probably guess, it’s relatively uncommon to create a mutator for a non-existent
column, because it can be confusing to set one property and have it change a different column
— but it is possible.

#### attribute casting   using protected $casts 
Possible attribute casting column types

Type                  Description
int|integer         Casts with PHP (int)
real|float|double   Casts with PHP (float)
string              Casts with PHP (string)
bool|boolean        Casts with PHP (bool)
object              Parses to/from JSON, as a stdClass object
array               Parses to/from JSON, as an array
collection          Parses to/from JSON, as a collection
date|datetime       Parses from database DATETIME to Carbon, and back
timestamp           Parses from database TIMESTAMP to Carbon, and back (can be used instead of Date mutators)

Using attribute casting on an Eloquent model
class Contact
{
protected $casts = [
'vip' => 'boolean',
'children_names' => 'array',
'birthday' => 'date',
];
}

#### Date mutators (Defining columns to be mutated as timestamps)
You can choose for particular columns to be mutated as timestamp columns by adding them to the dates array.

Defining columns to be mutated as timestamps
class Contact
{
protected $dates = [
'met_at'
];
}

By default, this array contains created_at and updated_at, so adding entries to dates just adds them to the list.
However, there’s no difference between adding columns to this list and adding them to $this->casts as timestamp, so this is becoming a bit of an unnecessary feature now that attribute casting can cast timestamps.

### Eloquent Collections
#### basic connections in Laravel (Illuminate\Support\Collection)
Laravel’s Collection objects (Illuminate\Support\Collection) are a little bit like arrays on
steroids. The methods they expose on array-like objects are so helpful that, once you’ve been
using them for a while, you’ll likely want to pull Illuminate into even non-Laravel projects
just for collections — which you can, with the Tightenco/Collect package.

#####  the simplest way to create a collection. 
Laravel also has a collect() helper.
$collection = collect([1, 2, 3]);

Now let’s say we want to filter out any even numbers:
$odds = $collection->reject(function ($item) {
return $item % 2 === 0;
});
Or what if we want to get a version of the array where each item is multiplied by 10? We can
do that as follows:
$multiplied = $collection->map(function ($item) {
return $item * 10;
});
We can even get only the evens, multiply them all by 10, and reduce them to a single number
by sum:
$sum = $collection
->filter(function ($item) {
return $item % 2 == 0;
})->map(function ($item) {
return $item * 10;
})->sum();

They provide the same functionality as native PHP methods like array_map() and array_reduce(), but you don’t have to memorize PHP’s unpredictable parameter order, and the method chaining syntax is endlessly more readable.

##### What Eloquent collections add (Illuminate\Database\Eloquent\Collection class)
Each Eloquent collection is a normal collection, but extended for the particular needs of a
collection of Eloquent results to represent database rows.
For example, every Eloquent collection has a method called modelKeys() that returns an array
of the primary keys of every instance in the collection. find($id) looks for an instance that
has the primary key of $id.
One additional feature available here is the ability to define that any given model should
return its results wrapped in a specific class of collection. So, if you want to add specific
methods to any collection of objects of the Order class — possibly related to summarizing the
financial details of your orders — you could create a custom OrderCollection that extends
the Illuminate\Database\Eloquent\Collection class, and then register it in your model.

1. Custom Collection classes for Eloquent models
...
class OrderCollection extends Collection
{
public function sumBillableAmount()
{
return $this->reduce(function ($carry, $order) {
return $carry + ($order->billable ? $order->amount : 0);
}, 0);
}
} .
..
class Order extends Model
{
public function newCollection(array $models = [])
{
return new OrderCollection($models);
}
Now, any time you get back a collection of Orders (e.g., from Order::all()) it’ll actually be
an instance of the OrderCollection class:
$orders = Order::all();
$billableAmount = $orders->sumBillableAmount();

### Eloquent Serialization
Serializing complex database records can be, well, complex, and this is one of the places
many ORMs fall short. Thankfully, you get two powerful methods for free with Eloquent:
toArray() and toJson(). Collections also have toArray() and toJson(), so all of these are
valid:
$contactArray = Contact::first()->toArray();
$contactJson = Contact::first()->toJson();
$contactsArray = Contact::all()->toArray();
$contactsJson = Contact::all()->toJson();

You can also cast an Eloquent instance or collection to a string ($string = (string)
$contact;), but both models and collections will just run toJson() and return the result.

1. Returning JSON from routes directly

// routes/web.php
Route::get('api/contacts', function () {
return Contact::all();
});
Route::get('api/contacts/{id}', function ($id) {
return Contact::findOrFail($id);
});

2. Hiding attributes from JSON

It’s very common to use JSON returns in APIs, and it’s very common to want to hide certain
attributes in these contexts, so Eloquent makes it easy to hide any attributes every time you
cast to JSON.

##### You can either blacklist attributes, hiding the ones you list: public $hidden, $visible, 

class Contact extends Model
{
public $hidden = ['password', 'remember_token'];

##### or whitelist attributes, showing only the ones you list:

class Contact extends Model
{
public $visible = ['name', 'email', 'status'];

##### This also works for relationships:

class User extends Model
{
public $hidden = ['contacts'];
public function contacts()
{
return $this->hasMany(Contact::class);
}

##### There might be times when you want to make an attribute visible just for a single call. That’s
possible, with the Eloquent method makeVisible():

$array = $user->makeVisible('remember_token')->toArray();

##### ADDING A GENERATED COLUMN TO ARRAY AND JSON OUTPUT: $appends
If you have created an accessor for a column that doesn’t exist — for example, our full_name column — add it to the $appends array on the model to add it to the array and JSON output:

class Contact extends Model
{
protected $appends = ['full_name'];

public function getFullNameAttribute()
{
return "{$this->first_name} {$this->last_name}";

### Eloquent Relationships
#### one to one  : $this->hasOne
Defining a one-to-one relationship
class Contact extends Model
{
public function phoneNumber()
{
return $this->hasOne(PhoneNumber::class);
}

How should this be defined in your database? Since we’ve defined that the Contact has one PhoneNumber, Eloquent expects that the table supporting the PhoneNumber class (likely phone_numbers) has a contact_id column on it. If you named it something different (for instance, owner_id), you’ll need to change your definition:

return $this->hasOne(PhoneNumber::class, 'owner_id');

Here’s how we access the phone number on a contact:

$contact = Contact::first();
$contactPhone = $contact->phoneNumber;

Notice that we define the method in the Example with phoneNumber(), but we access it with ->phoneNumber.
That’s the magic. You could also access it with ->phone_number. This will return a full Eloquent instance of the related PhoneNumber record.

##### Defining a one-to-one relationship’s inverse : $this->belongsTo
class PhoneNumber extends Model
{
public function contact()
{
return $this->belongsTo(Contact::class);
}
Then we access it the same way:
$contact = $phoneNumber->contact;

##### INSERTING RELATED ITEMS
Each relationship type has its own quirks for how to relate models, but here’s the core of how it works: pass an
instance to save(), or an array of instances to saveMany(). You can also pass properties to create() and it’ll
make a new instance for you:

$contact = Contact::first();
$phoneNumber = new PhoneNumber;
$phoneNumber->number = 8008675309;
$contact->phoneNumbers()->save($phoneNumber);

// or
$contact->phoneNumbers()->saveMany([
PhoneNumber::find(1),
PhoneNumber::find(2),
]);

// or
$contact->phoneNumbers()->create([
'number' => '+13138675309'
]);

#### one to many 
##### Defining a one-to-many relationship : $this->hasMany

class User extends Model
{
public function contacts()
{
return $this->hasMany(Contact::class);
}

Once again, this expects that the Contact model’s backing table (likely contacts) has a
user_id column on it. If it doesn’t, override it by passing the correct column name as the
second parameter of hasMany().
We can get a user’s contacts as follows:
$user = User::first();
$usersContacts = $user->contacts;
Just like with one to one, we use the name of the relationship method and call it as if it were a
property instead of a method. However, this method returns a collection instead of a model
instance. And this is a normal Eloquent collection, so you can have all sorts of fun with it:

$donors = $user->contacts->filter(function ($contact) {
return $contact->status == 'donor';
});

$lifetimeValue = $contact->orders->reduce(function ($carry, $order) {
return $carry + $order->amount;
}, 0);

#####  Defining a one-to-many relationship’s inverse :$this->belongsTo
class Contact extends Model
{
public function user()
{
return $this->belongsTo(User::class);
}
And just like one to one, we can access the User from the Contact:
$userName = $contact->user->name;

##### ATTACHING AND DETACHING RELATED ITEMS FROM THE ATTACHED ITEM (this illustrates how reverse save is used on belongsTo )
Most of the time we attach related items by running save() on the parent and passing in the related item, 
as in 
$user->contacts()->save($contact). 

But if you want to perform the behaviors on the attached (“child”) item,
you can use associate() and dissociate() on the method that returns the belongsTo():

$contact = Contact::first();
$contact->user()->associate(User::first());
$contact->save();
// and later
$contact->user()->dissociate();
$contact->save();

##### Using relationships as query builders
Until now, we’ve taken the method name (e.g., contacts()) and called it as if were a property
(e.g., $user->contacts). What happens if we call it as a method? Instead of processing the
relationship, it will return a prescoped query builder.

$donors = $user->contacts()->where('status', 'donor')->get();

##### Selecting only records that have a related item: has()
You can also choose to select only records that meet particular criteria with regard to their
related items using has():
$postsWithComments = Post::has('comments')->get();

You can also adjust the criteria further:
$postsWithManyComments = Post::has('comments', '>=', 5)->get();

You can nest the criteria:
$usersWithPhoneBooks = User::has('contacts.phoneNumbers')->get();

And finally, you can write custom queries on the related items:
// Gets all contacts with a phone number containing the string "867-5309"
$jennyIGotYourNumber = Contact::whereHas('phoneNumbers', function ($query) {
$query->where('number', 'like', '%867-5309%');

#### has many through : $this->hasManyThrough
Defining a has-many-through relationship
class User extends Model
{
public function phoneNumbers()
{
return $this->hasManyThrough(PhoneNumber::class, Contact::class);
}
You’d access this relationship using $user->phone_numbers, and as always you can customize
the relationship key on the intermediate model (with the third parameter of
hasmanyThrough()) and the relationship key on the distant model (with the fourth parameter).

### many to many 
#### Defining a many-to-many relationship :$this->belongsToMany
class User extends Model
{
public function contacts()
{
return $this->belongsToMany(Contact::class);
}
}

Defining a many-to-many relationship’s inverse
class Contact extends Model
{
public function users()
{
return $this->belongsToMany(User::class);
}
}

#### pivot table and its conventional name rule.
many-to-many relationships rely on a pivot table that connects the two. The
conventional naming of this table is done by placing the two singular table names together,
ordered alphabetically, and separating them by an underscore.
So, since we’re linking users and contacts, our pivot table should be named contacts_users
(if you’d like to customize the table name, pass it as the second parameter to the
belongsToMany() methods). It needs two columns: contact_id and user_id.
Just like with hasMany(), we get access to a collection of the related items, but this time it’s
from both sides

Accessing the related items from both sides of a many-to-many relationship
$user = User::first();
$user->contacts->each(function ($contact) {
// do something
});
$contact = Contact::first();
$contact->users->each(function ($user) {
// do something
});
$donors = $user->contacts()->where('status', 'donor')->get();

#### UNIQUE ASPECTS OF ATTACHING AND DETACHING MANY-TO-MANY RELATED ITEMS
Since your pivot table can have its own properties, you need to be able to set those properties when you’re attaching a
many-to-many related item. You can do that by passing an array as the second parameter to save():
$user = User::first();
$contact = Contact::first();
$user->contacts()->save($contact, ['status' => 'donor']);
Additionally, you can use attach() and detach() and, instead of passing in an instance of a related item, you can just
pass an ID. They work just the same as save(), but can also accept an array of IDs without you needing to rename the
method to something like attachMany():
$user = User::first();
$user->contacts()->attach(1);
$user->contacts()->attach(2, ['status' => 'donor']);
$user->contacts()->attach([1, 2, 3]);
$user->contacts()->attach([
1 => ['status' => 'donor'],
2,
3
]);
$user->contacts()->detach(1);
$user->contacts()->detach([1, 2]);
$user->contacts()->detach(); // Detaches all contacts
You can also use updateExistingPivot() to make changes just to the pivot record:
$user->contacts()->updateExistingPivot($contactId, [
'status' => 'inactive'
]);
And if you’d like to replace the current relationships, effectively detaching all previous relationships and attaching new
ones, you can pass an array to sync():
$user->contacts()->sync([1, 2, 3]);
$user->contacts()->sync([
1 => ['status' => 'donor'],
2,
3
]);

#### Getting data from the pivot table
One thing that’s unique about many to many is that it’s our first relationship that has a pivot
table. The less data you have on a pivot table, the better, but there are some cases where it’s
valuable to store information on your pivot table — for example, you might want to store a
created_at field to see when this relationship was created.

##### defining columns  in pivot table using withPivot(), withTimestamps()
In order to store these fields, you have to add them to the relationship definition, You can define specific fields using withPivot() or add created_at and updated_at timestamps using withTimestamps().

Adding fields to a pivot record
public function contacts()
{
return $this->belongsToMany(Contact::class)
->withTimestamps()
->withPivot('status', 'preferred_greeting');
}

##### Getting data from a related item’s pivot entry
$user = User::first();
$user->contacts->each(function ($contact) {
echo sprintf(
'Contact associated with this user at: %s',
$contact->pivot->created_at
);
});

### Polymorphic relationship  $this->morphsTo()  $this->morphMany
Remember, our polymorphic relationship is where we have multiple Eloquent classes
corresponding to the same relationship. We’re going to use Stars (like favorites) right now. A
user can star both Contacts and Events, and that’s where the name polymorphic comes from:
a single interface to objects of multiple types.

we’ll need three tables, and three models: Star, Contact, and Event (and, of course, User,
but we’ll get there in a second). The contacts and events tables will just be as they normally
are, and the stars table will contain an id field, a starrable_id, and a starrable_type. For
each Star, you’ll be defining which “type” (e.g., Contact or Event) and which ID of that type
(e.g., 1) it is.

Creating the models for a polymorphic starring system
class Star extends Model
{
public function starrable()
{
return $this->morphsTo();
}
}

class Contact extends Model
{
public function stars()
{
return $this->morphMany(Star::class, 'starrable');
}
} 

class Event extends Model
{
public function stars()
{
return $this->morphMany(Star::class, 'starrable');
}
}

So, how do we create a Star?
$contact = Contact::first();
$contact->stars()->create();
It’s that easy. The Contact is now starred.
In order to find all of the Stars on a given Contact, we call the stars() method:

Retrieving the instances of a polymorphic relationship
$contact = Contact::first();
$contact->stars->each(function ($star) {
// Do stuff
});
If we have an instance of Star, we can get its target by calling the method we used to define its
morphTo(), which in this context is starrable(). 

Retrieving the target of polymorphic instance
$stars = Star::all();
$stars->each(function ($star) {
var_dump($star->starrable); // An instance of Contact or Event
});
Finally, you might be wondering, “What if I care who starred this contact?” That’s a great
question; of course you do. It’s as simple as adding user_id to your stars table, and then
setting up that a User has many Stars and a Star belongs to_ one User — a one-to-many
relationship.The stars table becomes almost a pivot table between your User
and your Contacts and Events.

Extending a polymorphic system to differentiate by user
class Star extends Model
{
public function starrable()
{
return $this->morphsTo;
} 

public function user()
{
return $this->belongsTo(User::class);
}
}

class User extends Model
{
public function stars()
{
return $this->hasMany(Star::class);
}
}

That’s it! You can now run $star->user or $user->stars to find a list of a User’s Stars or to
find the starring User from a Star. Also, when you create a new Star, you’ll now want to pass
the User:

$user = User::first();
$event = Event::first();
$event->stars()->create(['user_id' => $user->id]);

### Many to many polymorphic  $this->morphToMany  $this->morphedByMany
The most complex and least common of the relationship types, many-to-many polymorphic
relationships are like polymorphic relationships, except instead of being one to many they’re
many to many.

The most common example for this relationship type is the tag, so I’ll keep it safe and use that
as our example. Let’s imagine you want to be able to tag Contacts and Events. The uniqueness
of many-to-many polymorphism is that it’s many to many: each tag may be applied to
multiple items, and each tagged item might have multiple tags. And to add to that, it’s
polymorphic: tags can be related to items of several different types. For the database, we’ll
start with the normal structure of the polymorphic relationship but also add a pivot table.
This means we’ll need a contacts table, an events table, and a tags table, all shaped like
normal with an ID and whatever properties you want, and a new taggables table, which will
have a tag_id, a taggable_id, and a taggable_type. Each entry into the taggables table will
relate a tag with one of the taggable content types.

Defining a polymorphic many-to-many relationship
class Contact extends Model
{
public function tags()
{
return $this->morphToMany(Tag::class, 'taggable');
}
} 

class Event extends Model
{
public function tags()
{
return $this->morphToMany(Tag::class, 'taggable');
}
}

class Tag extends Model
{
public function contacts()
{
return $this->morphedByMany(Contact::class, 'taggable');
}

public function events()
{
return $this->morphedByMany(Event::class, 'taggable');
}
}

Here’s how to create your first tag:
$tag = Tag::firstOrCreate(['name' => 'likes-cheese']);
$contact = Contact::first();
$contact->tags()->attach($tag->id);

Accessing the related items from both sides of a many-to-many polymorphic
relationship
$contact = Contact::first();
$contact->tags->each(function ($tag) {
// Do stuff
});
$tag = Tag::first();
$tag->contacts->each(function ($contact) {
// Do stuff
});

### Child Records Updating Parent Record Timestamps :
#### using protected $touches
any Eloquent models by default will have created_at and updated_at timestamps.
Eloquent will set the updated_at timestamp automatically any time you make any changes to a record.

When a related item either belongsTo() or belongsToMany() another item, it might be
valuable to mark the other item as updated any time the related item is updated. For example,
if a PhoneNumber is updated, the Contact it’s connected to should be marked as having been
updated as well.

Updating a parent record any time the child record is updated
class PhoneNumber extends Model
{
protected $touches = ['contact'];
public function contact()
{
return $this->belongsTo(Contact::class);
}

#### Eager loading (to avoid the datbase load so-called N+1 problem)
The problem with lazy loading is that it can introduce significant database load (often the N+1 problem).
If you are loading a model instance and you know you’ll be working with its relationships,
you can instead choose to “eager-load” one or many of its sets of related items:
$contacts = Contact::with('phoneNumbers')->get();
Using the with() method with a retrieval gets all of the items related to the pulled item(s), and
as you can see in this example, you pass it the name of the method the relationship is defined by.

When we use eager loading, instead of pulling the related items one at a time when they’re requested (selecting one phone number each time a foreach loop runs), we have a single query to pull the initial items (selecting all contacts) and a second query to pull all their related items (selecting all phone numbers owned by the contacts we just pulled).

You can eager-load multiple relationships by passing multiple parameters to the with() call:

$contacts = Contact::with('phoneNumbers', 'addresses')->get();

And you can nest eager loading to eager-load the relationships of relationships:

$authors = Author::with('posts.comments')->get();


##### Constraining eager loads
If you want to eager-load a relationship, but not all of the items, you can pass a closure to

with() to define exactly which related items to eager-load:
$contacts = Contact::with(['addresses' => function ($query) {
$query->where('mailable', true);
}])->get();

##### Lazy eager loading
I know it sounds crazy, because we just defined eager loading as sort of the opposite of lazy
loading, but sometimes you don’t know you want to perform an eager-load query until after
the initial instances have been pulled. You can still perform an eager load after the fact, with
lazy eager loading:
$contacts = Contact::all();
if ($showPhoneNumbers) {
$contacts->load('phoneNumbers');
}

##### Eager loading only the count
If you want to eager-load relationships but only so you can have access to the count of items
in each relationship, you can try withCount():
$authors = Author::withCount('posts')->get();
// adds a "posts_count" integer to each Author with a count of that
// Author's number of related posts


### Eloquent Events
Eloquent models fire events out into the void of your application every time certain actions
happen, regardless of whether you’re listening. If you’re familiar with pub/sub, it’s this same
model.

Here’s a quick rundown of binding a listener to when a new Contact is created. We’re going
to bind it in the boot() method of AppServiceProvider, and let’s imagine we’re notifying a
third-party service every time we create a new Contact.
Example 8-51. Binding a listener to an Eloquent event
class AppServiceProvider extends ServiceProvider
{
public function boot()
{
$thirdPartyService = new SomeThirdPartyService;
Contact::creating(function ($contact) use ($thirdPartyService) {
try {
$thirdPartyService->addContact($contact);
} catch (Exception $e) {
Log::error('Failed adding contact to ThirdPartyService; cancelled.');
return false;
}
});
}
We can see a few things in Example 8-51. First, we use Modelname::eventName() as the
method, and pass it a closure. The closure gets access to the model instance that is being
operated on. Second, we’re going to need to define this listener in a service provider
somewhere. And third, if we return false, the operation will cancel and the save() or
update() will be cancelled.
Here are the events that every Eloquent model fires:
creating
created
updating
updated
saving
saved
deleting
deleted
restoring
restored
Most of these should be pretty clear, except possibly restoring and restored, which fire
when you’re restoring a soft-deleted row. Also, saving is fired for both creating and
updating and saved is fired for both created and updated.

# User Authentication and Authorization
## The User Model and Migration
We are able to fill out the name, email, and password properties when
creating a new user, and the password and remember_token properties are excluded when
outputting the user as JSON.

<?php
// App\User
namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable
{
use Notifiable;
/*** The attributes that are mass assignable.
**
@var array
*/
protected $fillable = [
'name', 'email', 'password',
];

/*** The attributes excluded from the model's JSON form.
**
@var array
*/
protected $hidden = [
'password', 'remember_token',
];
}

<?php
// Illuminate\Foundation\Auth\User
namespace Illuminate\Foundation\Auth;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
class User extends Model implements
AuthenticatableContract,
AuthorizableContract,
CanResetPasswordContract
{
use Authenticatable, Authorizable, CanResetPassword;
}

### CONTRACTS AND INTERFACES
You may have noticed that sometimes I write the word “contract” and sometimes “interface,” and that almost all of the interfaces in Laravel are under the Contracts namespace. A PHP interface is essentially an agreement between two classes that one of the classes will “behave” a certain way. It’s a bit like a contract between them, and thinking about it as a contract gives a bit more inherent meaning to the name than calling it an interface does.
In the end, though, they’re the same thing: an agreement that a class will provide certain methods with a certain signature. On a related note, the Illuminate\Contracts namespace contains a group of interfaces that Laravel components implement and typehint. This makes it easy to develop similar components that implement the same interfaces and swap them into your application in place of the stock Illuminate components. When the Laravel core and components typehint a mailer, for example, they don’t typehint the Mailer class. Instead, they typehint the Mailer contract (interface), making it easy to provide your own mailer. To learn more about how to do this, take a look at Chapter 11.

## Using the auth() Global Helper and the Auth Facade
The auth() global helper is the easiest way to interact with the status of the authenticated user
throughout your app. You can also inject an instance of Illuminate\Auth\AuthManager and
get the same functionality, or use the Auth facade.

The most common usages are to check whether a user is logged in (auth()->check() returns
true if the current user is logged in; auth()->guest() returns true if the user is not logged
in) and to get the currently logged-in user (use auth()->user(), or auth()->id() for just the
ID; both return null if no user is logged in).

Sample usage of the auth() global helper in a controller
public function dashboard()
{
if (auth()->guest()) {
return redirect('sign-up');
} r
eturn view('dashboard')
->with('user', auth()->user());
}

## The Auth Controllers in the Auth-namespaced controllers: RegisterController, LoginController,ResetPasswordController, and ForgotPasswordController.

### RegisterController
The controller itself just contains a few hooks that the traits will call at given points. That
makes it easy to customize a few common behaviors without having to dig deeply into the
code that makes it all work.
The $redirectTo property defines where users will be redirected after registration. The
validator() method defines how to validate registrations. And the create() method defines
how to create a new user based on an incoming registration. 

Laravel’s default RegisterController
...
class RegisterController extends Controller
{
use RegistersUsers;
protected $redirectTo = '/home';
...
protected function validator(array $data)
{
return Validator::make($data, [
'name' => 'required|max:255',
'email' => 'required|email|max:255|unique:users',
'password' => 'required|min:6|confirmed',
]);
} p
rotected function create(array $data)
{
return User::create([
'name' => $data['name'],
'email' => $data['email'],
'password' => bcrypt($data['password']),
]);
}
}

#### RegistersUsers trait
The RegistersUsers trait, which the RegisterController imports, handles a few primary
functions for the registration process. First, it shows users the registration form view, with the
showRegistrationForm() method. If you want new users to register with a view other than
auth.register you can override the showRegistrationForm() method in your
RegisterController.
Next, it handles the POST of the registration form with the register() method. This method
passes the user’s registration input to the validator from the validator() method of your
RegisterController, and then on to the create() method.

And finally, the redirectPath() method (pulled in via the RedirectsUsers trait) defines
where users should be redirected after a successful registration. You can define this URI with
the redirectTo property on your controller, or you can override the redirectPath() method
and return whatever you want.

If you want this trait to use a different auth guard than the default (you’ll learn more about
guards in “Guards”), you can override the auth() method and have it return whichever guard
you’d like.

### LoginController

#### AuthenticatesUsers traint
The AuthenticatesUsers trait is responsible for showing users the login form, validating
their logins, throttling failed logins, handling logouts, and redirecting users after a successful
login.

The showLoginForm() method defaults to showing the user the auth.login view, but you can
override it if you’d like it to use a different view.
The login() method accepts the POST from the login form. It validates the request using the
validateLogin() method, which you can override if you’d like to customize the validation. It
then hooks into the functionality of the ThrottlesLogins trait, which we’ll cover shortly, to
reject users with too many failed logins. And finally, it redirects the user, either to her
intended path (if the user was redirected to the login page when attempting to visit a page
within the app) or to the path defined by the redirectPath() method, which returns your
$redirectTo property.
The trait calls the empty authenticated() method after a successful login, so if you’d like to
perform any sort of behavior in response to a successful login, just override this method in
your LoginController.
There’s a username() method that defines which of your users columns is the “username”;
this defaults to email but you can change that by overwriting the username() method in your
controller to return the name of your username column.
And, like in the RegistersUsers trait, you can override the guard() method to define which
auth guard (more on that in “Guards”) this controller should use.

#### ThrottlesLogins trait
The ThrottlesLogins trait is an interface to Laravel’s Illuminate\Cache\RateLimiter class,
which is a utility to rate-limit any event using the cache. This trait applies rate limiting to user
logins, limiting users from using the login form if they’ve had too many failed logins within
a certain amount of time. This functionality does not exist in Laravel 5.1.
If you import the ThrottlesLogins trait, all of its methods are protected, which means they
can’t actually be accessed as routes. Instead, the AuthenticatesUsers trait looks to see whether
you’ve imported the ThrottlesLogins trait, and if so, it’ll attach its functionality to your
logins without any work on your part. Since the default LoginController imports both, you’ll
get this functionality for free if you use the auth scaffold.
ThrottlesLogins limits any given combination of username and IP address to 5 attempts per
60 seconds. Using the cache, it increments the “failed login” count of a given username/IP
address combination, and if any user reaches 5 failed login attempts within 60 seconds, it
redirects that user back to the login page with an appropriate error until the 60 seconds is
over.

#### ResetPasswordController
The ResetPasswordController simply pulls in the ResetsPasswords trait. This trait provides
validation and access to basic password reset views, and then uses an instance of Laravel’s
PasswordBroker class (or anything else implementing the PasswordBroker interface, if you
choose to write your own) to handle sending password reset emails and actually resetting the
passwords.
Just like the other traits we’ve covered, it handles showing the reset password view
(showResetForm() shows the auth.passwords.reset view), and the POST request that is sent
from that view (reset() validates and sends the appropriate response). The resetPassword()
method actually resets the password, and you can customize the broker with broker() and the
auth guard with guard().
If you’re interested in customizing any of this behavior, just override the specific method you
want to customize in your controller.

#### ForgotPasswordController
The ForgotPasswordController simply pulls in the SendsPasswordResetEmails trait. It shows
the auth.passwords.email form with the showLinkRequestForm() method, and handles the
POST of that form with the sendResetLinkEmail() method. You can customize the broker with
the broker() method.

### Auth::routes()
Now that we have the auth controllers providing some methods for a series of pre-defined
routes, we’ll want our users to actually be able to hit those routes. We could add all these
routes manually to routes/web.php, but there’s already a convenience tool for that, called
Auth::routes():
// routes/web.php
Auth::routes();


    public static function routes()
    {
        static::$app->make('router')->auth();
    }
    
// Illuminate/Routing/Router.php

The routes provided by Auth::routes()

   public function auth()
    {
        // Authentication Routes...
        $this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
        $this->post('login', 'Auth\LoginController@login');
        $this->post('logout', 'Auth\LoginController@logout')->name('logout');

        // Registration Routes...
        $this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
        $this->post('register', 'Auth\RegisterController@register');

        // Password Reset Routes...
        $this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
        $this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
        $this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
        $this->post('password/reset', 'Auth\ResetPasswordController@reset');
    }

### The Auth Scaffold
At this point you have a migration, a model, controllers, and routes for your authentication
system. But what about your views?
Laravel handles that by providing an auth scaffold (new in Laravel 5.2), which is intended to
be run on a new application and provide you with even more skeleton code to get your auth
system running quickly.
The auth scaffold takes care of adding Auth::routes() to your routes file, adds a view for
each route, and creates a HomeController to serve as the landing page for logged-in users; it
also routes to the index() method of HomeController at the /home URI.
#### php artisan make:auth 
the following files will be made available to you:

app/Http/Controllers/HomeController.php
resources/views/auth/login.blade.php
resources/views/auth/register.blade.php
resources/views/auth/passwords/email.blade.php
resources/views/auth/passwords/reset.blade.php
resources/views/layouts/app.blade.php
resources/views/home.blade.php

#### Let’s review quickly the steps from new site to full authentication system:
laravel new MyApp
cd MyApp
php artisan make:auth
php artisan migrate

That’s it. Run those commands, and you will have a landing page and a bootstrap-based user
registration, login, logout, and password reset system, with a basic landing page for all
authenticated users.

### “Remember Me”
1. Attempting a user authentication
if (auth()->attempt([
'email' => request()->input('email'),
'password' => request()->input('password')
])) {
// Handle the successful login
}

2. Attempting a user authentication with a “remember me” checkbox check
if (auth()->attempt([
'email' => request()->input('email'),
'password' => request()->input('password')
]), request()->has('remember')) {
// Handle the successful login
}
You can see that we checked whether the input has a remember property, which will return a
boolean. This allows our users to decide if they want to be remembered with a checkbox in
the login form.

And later, if you need to manually check whether the current user was authenticated by a
remember token, there’s a method for that: auth()->viaRemember() returns a boolean
indicating whether or not the current user authenticated via a remember token. This will allow
you to prevent certain higher-sensitivity features from being accessible by remember token,
and you can require users to reenter their passwords.

### Manually Authenticating Users
The most common case for user authentication is that you’ll allow the users to provide their
credentials, and then use auth()->attempt() to see whether the provided credentials match
any real users. If so, you log them in.
But sometimes there are contexts where it’s valuable for you to be able to choose to log a user
in on your own. For example, you may want to allow admin users to switch users.

1. There are two methods that make this possible. First, you can just pass a user ID:
auth()->loginUsingId(5);

2. Second, you can pass a User object (or any other object that implements the
Illuminate\Contracts\Auth\Authenticatable contract):
auth()->login($user);

## Auth Middleware
Laravel comes with the route Auth middleware out of the box.
Route middleware defined in App\Http\Kernel:
protected $routeMiddleware = [
'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
'can' => \Illuminate\Auth\Middleware\Authorize::class,
'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
];
Three of the default route middleware are authentication-related: auth restricts route access to
authenticated users, auth.basic restricts access to authenticated users using HTTP Basic
Authentication, and guest restricts access to unauthenticated users. can is used for authorizing
user access to given routes.

It’s most common to use auth for your authenticated-user-only sections and guest for any
routes you don’t want authenticated users to see (like the login form). auth.basic is a much
less commonly used middleware for authenticating via request headers.

### Sample routes protected by auth middleware
Route::group(['middleware' => 'auth'], function () {
Route::get('account', 'AccountController@dashboard');
});
Route::get('login', 'Auth\LoginController@getLogin')->middleware('guest');






















