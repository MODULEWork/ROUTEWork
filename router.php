<?php
namespace MODULEWork;
/*===================================================
*
*
*
* Name: ROUTEWork
* Version: 1.0
* License: Apache 2.0
* Author: Christian Gärtner
* Author URL: http://christiangaertner.github.io
* Project URL: https://github.com/ChristianGaertner/MODULEWork
* Description: This is basic RESTful router for PHP.
*
*
*
===================================================*/


/**
* Router
* @author ChristianGaertner <christiangaertner.film@googlemail.com>
*/
class Route
{
	/**
	 * The current uri seperated as array
	 * @var array
	 */
	protected static $uri;
	/**
	 * The current HTTP request method
	 * @var string
	 */
	public static $rest;
	/**
	 * The current scriptname, used for filtering the uri
	 * @var string
	 */
	protected static $scriptname;
	/**
	 * Will be true after a route was found and serverd
	 * @var boolean
	 */
	public static $served;

	/**
	 * Initializes the routing process and setups essential variables
	 */
	public static function tar()
	{
		static::$served = false;
		static::$uri = explode('/', preg_replace('{/$}', '', $_SERVER['REQUEST_URI']));
		static::$scriptname = explode('/', $_SERVER['SCRIPT_NAME']);
		static::$rest = $_SERVER['REQUEST_METHOD'];

		for ($i=0; $i < sizeof(static::$scriptname); $i++) {
			if (isset(static::$uri[$i]) && isset(static::$scriptname[$i])) {
				if (static::$uri[$i] == static::$scriptname[$i]) {
					unset(static::$uri[$i]);
				}
			}
		}
		static::$uri = array_values(static::$uri);
		if (empty(static::$uri[0])) {
			static::$uri[0] = '/';
		}
	}
	/**
	 * Retrieve the current URI
	 * @param  boolean $array TRUE will return the uri as array (seperated by '/')
	 * @return array OR string The current URI
	 */
	public function getURI($array = false)
	{
		if ($array) {
			return static::$uri;
		} else {
			return implode('/', static::$uri);
		}
	}
	/**
	 * Will process the request on GET only
	 * @param  string $uri_r
	 * @param  closure OR string $callback
	 */
	public function get($uri_r, $callback)
	{
		if (static::$rest == 'GET') {
			self::process($uri_r, $callback);
		}
	}
	/**
	 * Will process the request on POST only
	 * @param  string $uri_r
	 * @param  closure OR string $callback
	 */
	public function post($uri_r, $callback)
	{
		if (static::$rest == 'POST') {
			self::process($uri_r, $callback);
		}
	}
	/**
	 * Will process the request on PUT only
	 * @param  string $uri_r
	 * @param  closure OR string $callback
	 */
	public function put($uri_r, $callback)
	{
		if (static::$rest == 'PUT') {
			self::process($uri_r, $callback);
		}
	}
	/**
	 * Will process the request on DELETE only
	 * @param  string $uri_r
	 * @param  closure OR string $callback
	 */
	public function delete($uri_r, $callback)
	{
		if (static::$rest == 'DELETE') {
			self::process($uri_r, $callback);
		}
	}
	/**
	 * Fallback method if the route was not found
	 * @param  closure OR string $callback
	 */
	public function _404($callback)
	{
		if (static::$served) {
			return;
		}

		if (is_callable($callback)) {
			header('HTTP/1.0 404 Not Found');
			echo $callback(implode('/', static::$uri));
		} else {
				$split = explode('#', $callback);
				$controller = $split[0];
				$method = $split[1];
				self::controller($controller, $method, implode('/', static::$uri), true);
		}
	}
	/**
	 * Register a controller to responde to a request
	 * URL / controller / method / parameter
	 * <code>example.com/home/about</code>
	 * Will call the about() method of a class called home
	 * If only one uri parameter was send the index() method will be called
	 * @param  string  $name
	 * @param  boolean $default
	 */
	public function register($name, $default = false)
	{
		if (!is_string($name)) {
			throw new \Exception("Controller name has to be a string", 1);	
		}

		if ($name == static::$uri[0] || $default) {
			if ($default && static::$uri[0] !== '/') {
				return;
			}
			if (isset(static::$uri[1])) {
				$method = static::$uri[1];
			} else {
				$method = 'index';
			}

			if (isset(static::$uri[2])) {
				$param = static::$uri[2];
			} else {
				$param = null;
			}

			self::controller($name, $method, $param, true);
		}
	}
	/**
	 * This will register the class to responde to the '/' route as well
	 * @param string $name
	 */
	public function setDefault($name)
	{
		self::register($name, true);
	}

	/**
	 * Process the get/post/put/delete methods
	 * @param  string $uri_r
	 * @param  closure OR string $callback
	 */
	protected function process($uri_r, $callback)
	{
		if (self::match($uri_r) && !static::$served) {
			if (is_callable($callback)) {
				echo $callback(self::getParameter($uri_r));
				static::$served = true;
			} else {
				$split = explode('#', $callback);
				$controller = $split[0];
				$method = $split[1];
				self::controller($controller, $method, $uri_r);
			}
		}
	}
	/**
	 * Call a controller
	 * <strong>This will require the CONTROLLERWork component</strong>
	 * @param  string  $controller_n The name of the class
	 * @param  string  $method The name of the method to call
	 * @param  string  $uri_r The current URI OR the parameter which should be passed to the function
	 * @param  boolean $param TRUE will let $uri_r act as parameter
	 * @return [type]
	 */
	protected function controller($controller_n, $method, $uri_r, $param = false)
	{
		$controller['name'] = __NAMESPACE__ . '\\' . $controller_n;
		if (class_exists($controller['name'])) {
			$controller['object'] = Controller::get($controller['name']);
			if ($controller['object']->rest) {
				$method = strtolower(static::$rest) . '_' . $method;
			}

			if (method_exists($controller['object'], $method)) {
				if ($param) {
					$controller['object']->$method($uri_r);
				} else {
					$controller['object']->$method(self::getParameter($uri_r));
				}
				static::$served = true;
			}	
		}
	}
	/**
	 * Check if the whished uri matches the current
	 * @param  string $uri_r The URI to check
	 * @return boolean
	 */
	protected function match($uri_r)
	{
		if ($uri_r !== '/') {
			$uri_r = explode('/', $uri_r);
		} else {
			$uri_r = array('/');
		}
		$uri_r = array_values($uri_r);
		if (count($uri_r) !== count(static::$uri)) {
			return false;
		}
		for ($i=0; $i < count($uri_r); $i++) { 
			if ($uri_r[$i] == static::$uri[$i]) {
				$match = true;
			} elseif ($uri_r[$i] == '(:any)') {
				$match = true;
			} elseif (is_numeric(static::$uri[$i]) && $uri_r[$i] == '(:num)') {
				$match = true;
			} else {
				return false;
			}
		}

		return $match;
	}
	/**
	 * Return the 'parameter' of the uri
	 * @param  string $uri_r The URI template
	 * @return string the parameter
	 */
	protected function getParameter($uri_r)
	{
		if ($uri_r !== '/') {
			$uri_r = explode('/', $uri_r);
		} else {
			return null;
		}
		$uri_r = array_values($uri_r);
		for ($i=0; $i < count($uri_r); $i++) {
			if ($uri_r[$i] == '(:any)') {
				return static::$uri[$i];
			} elseif($uri_r[$i] == '(:num)' && is_numeric(static::$uri[$i])) {
				return static::$uri[$i];
			}
		}

		return null;
	}
}