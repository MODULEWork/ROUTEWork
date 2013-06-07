<?php
namespace MODULEWork;
include 'router.php';

Route::tar();

Route::get('/', function()
{
	?>
	<!DOCTYPE html>
	<html lang="en-US">
		<head>
			<meta charset="utf-8">
				<title>HOME</title>
				<link rel="stylesheet" href="">
				<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
				<!--[if lt IE 9]>
					<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
				<![endif]-->
		</head>
		
		<body>
			<h1>HOME</h1>
			<p>Cool stuff</p>
		</body>
		
	</html>
	<?php
});

Route::get('user', function() {
	?>
	<!DOCTYPE html>
	<html lang="en-US">
		<head>
			<meta charset="utf-8">
				<title>USER</title>
				<link rel="stylesheet" href="">
				<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
				<!--[if lt IE 9]>
					<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
				<![endif]-->
		</head>
		
		<body>
			<h1>USER</h1>
			<form method="POST" action="/user/post">
				<input type="text" name="input" />
				<input type="submit" value="Send" />
			</form>
		</body>
		
	</html>
	<?php
});

Route::post('user/post', function() {
	echo $_POST['input'];
});


Route::get('user/(:any)/show', function($name) {
	echo "Name: ", $name;
});

Route::get('user/id/(:num)', function($id) {
	echo "ID: ", $id;
});

Route::_404(function($uri) {
	echo "<h1> 404 - NOT FOUND</h1>";
	echo "<p> The request address: '" . $uri . "' was not found on the server.</p>";
});