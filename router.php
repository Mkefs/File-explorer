<?php 
	class Router {
		private static $routes = [];
		private static $routeNotFound = null;

		public static function add($path, $func, $method = 'get') {
			array_push(self::$routes, [
				'path' => $path,
				'func' => $func,
				'method' => $method
			]);
		}

		public static function e404($func) {
			self::$routeNotFound = $func;
		}

		public static function run() {
			$method = strtolower($_SERVER['REQUEST_METHOD']);
			$url = $_GET['r'];
			$found = false;
			foreach(self::$routes as $route) { 
				if(strtolower($route['method']) == $method
					&& $route['path'] == $url) {
					$found = true;
					call_user_func($route['func']);
					break;
				}
			}
			if(!$found) {
				http_response_code(404);
				if(self::$routeNotFound != null) {
					call_user_func(self::$routeNotFound);
				} else {
					echo "404 not found";
				}
			}
		}
	}
