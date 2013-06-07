ROUTEWork
==========

This is a really easy to use routing class. You may know this from large framworks. But this time it is standalone!

Installation
-------------

* Place the ```router.php``` file into your application folder
* Place the ```.htaccess``` file into you root-site folder, adjust the ```RewriteBase``` as needed
* include it ```include 'router.php';```
* initate it:    
```Route::tar()``` <- I know not my best joke :D    
      


HOWTO & Examples
---------------------------
Now you can start defining some routes like this:

    Route::get('/', function() {
          echo "Home";
    });

This will display ```Home``` everytime you visit your url. Make sure that you always define this route.

But what about other routes? Let' s say we want to setup a basic site.
We need a ```contact```, ```about``` and ```projects``` page.

    Route::get('contact', function() {
          ?>
          <html>
          <head>
              <title>Contact us!</title>
          </head>
          <body>
               <h1>Contact Us!</h1>
              <form action="contact" method="POST">
                  <input type="text" name="name" />
                  <input type="submit" value="Send us your name!" />
              </form action="contact" method="POST">
          </body>
          </html>
          <?php
    });

Note that we exit out of PHP in order to write some HTML markup and we do **NOT** add any trailing or forwarding slashes to our route!

    Route::get('about/(:any)', function($name) {
          ?>
          <html>
          <head>
              <title>About us!</title>
          </head>
          <body>
               <h1>About Us!</h1>
               <p>Staff: <?php echo $name; ?></p>
          </body>
          </html>
          <?php
    });

On the about page users can view the profiles of different employers by appending a name like this to the url ```/bob```. In the route we define a wildcard ```(:any)``` which allows any character and makes it available  through the ```$names``` variable we passed to the closure. There are two types of wildcards available:

* ```(:any)``` Any character combination
* ```(:num)``` Numbers only    

Now use this knowledge:

    Route::get('project/(:num)', function($id) {
         switch($id):
           case: '1'
                $project = 'MODULEWork';
           default:
                $project = 'Not Found';
          ?>
          <html>
          <head>
              <title>PROJECT | <?php echo $project; ?></title>
          </head>
          <body>
               <h1><?php echo $project; ?></h1>
               <p>Interesting information...</p>
          </body>
          </html>
          <?php
    });

You can see you can only pass an integer to this route and it the project name is generated based on this id.

You may have seen that we have created a form on our contact page. If we would submit it now we would see a blank page. Why? The ```contact``` route is defined. However it is defined as a ```get``` route. This route will only respond to HTTP GET request and not POST. In order to grap the info of the form we need to define a route which responds to ```POST``` requests.

ROUTEWork is RESTful
------------------------------

You can define routes for 4 types of HTTP request methods:

* ```GET``` with this method: ```get()```
* ```POST``` with this method: ```post()```
* ```PUT``` with this method: ```put()```
* ```DELETE``` with this method: ```delete()```

All methods have the same syntax!

NOT - FOUND
------------------

Maybe you may wonder how you can show your user that he accessed a page which does not exists.

**ROUTEWork** makes this as easy as pie.
Simply add this to the bottom of your **all** your route definitions!

    Route::_404(function($uri) {
       echo "<h1>404 - NOT FOUND</h1>";
       echo "<p>Your requested page: ", $uri, "could not be found...</p>";
    });

Make sure to have this at the very bottom since it will responde to every uri a user enters.

Routes are called from top to bottom. This means if you define a rule twice the first one triggers only!
