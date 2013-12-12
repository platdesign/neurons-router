<?PHP 
	
	namespace router;
	use nrns;
	
	
	

	class routeProvider extends nrns\Provider {
		
		// Load routeSetter-methods (when, get, put, post, delete)
		use routeSetter;
		
		
		private $routes = ["ALL"=>[], "GET"=>[], "POST"=>[], "PUT"=>[], "DELETE"=>[]];
			
		private $activeRoute;
		private $otherwiseRoute;
		
		private $routeClassname = 'router\route';
		
		
		
		public function __construct($nrns, $request, $injectionProvider) {
			$this->injection 	= $injectionProvider;
			$this->request 		= $request;
			$this->nrns			= $nrns;
			
			$this->checkForHtaccess();
			
			$this->otherwise = function(){};
				
			$this->nrns->on('init', function(){
				
				// Create empty route for otherwise and set an emtpy controller
				//$this->otherwiseRoute = $this->injection->invoke($this->routeClassname);
				
				$this->activeRoute = $this->findRoute();
			});
		
			// Add event to the app which executes the active route on app-start
			$this->nrns->on('run', function(){
				
				if($this->activeRoute) {
					if( method_exists($this->activeRoute, 'call') ) {
						$this->activeRoute->call();
					}
				} else {
					nrns::$injection->invoke($this->otherwise, ['route'=>null]);
				}
				
				
			});
		}
		
		
	
		
		
		public function getActiveRoute() {
			return $this->activeRoute;
		}
		
		public function otherwise($closure) {
			$this->otherwise = $closure;
			return $this;
		}
		
		
		
		
		public function addRoute($method, $route, $then) {
			$route = str_replace("//", "/", $route);

			$routeObject = $this->injection->invoke($this->routeClassname, [
				'method'	=>	$method,
				'route'		=>	$route,
				'then'		=>	$then
			]);
			
			return $this->routes[$method][$route] = $routeObject;
		}




		
		private function findRoute() {
			$method = $this->request->getMethod();
			$route 	= $this->request->getRoute();
			
			if( $all = $this->findRouteObject("ALL", $route) ) {
				return $all;
			} elseif($else = $this->findRouteObject($method, $route)) {
				return $else;
			}
			
			return $this->otherwiseRoute;
		}
		
		
		public function findRouteObject($method, $route) {
			
			$routes = $this->routes[$method];

			if( count($routes) > 0 ) {
				
				// look up in static routes
				if( isset($routes[ $route ]) ) {
					return $routes[ $route ];
				} else {
				
				// look up in dynamic routes
					foreach ($routes as $object) {
						if( $object->matchesWith($route) ) {
							return $object;
						}
					}
				}
				
			}
			
		}


		
		
		
    	public function getService() {
    		return $this->activeRoute;
    	}
	
	
	
	
	
	
	
	
	
	
	
		public function getRoutes($method='ALL') {
			$routes = $this->routes[strtoupper($method)];
			$result = [];
			foreach($routes as $route) {
				$result[] = $route->_route;
			}
			return $result;
		}
	
	
		private function checkForHtaccess() {
			
			$htaccessFile = __SCRIPT__."/.htaccess";
			
			
			if(!file_exists($htaccessFile)) {
				
				$htaccessContent = str_replace("\t", "", 'RewriteEngine On

					RewriteBase '.__SCRIPT__.'

					# Remove double slashes in whole URL
					RewriteCond %{REQUEST_URI} ^(.*)//(.*)$
					RewriteRule . %1/%2 [R=301,L]

					# Send each Request to index.php
					RewriteCond %{REQUEST_FILENAME} !-f
					RewriteCond %{REQUEST_FILENAME} !-d
					RewriteRule ^ index.php [QSA,L]
				');
				
				if( is_writable($htaccessFile) ) {
					file_put_contents($htaccessFile, $htaccessContent);
				} else {
					throw nrns::Exception("Create .htaccess-File at <br><pre>".$htaccessFile."</pre> with following Content<hr><pre>".$htaccessContent."</pre>");
				}
				
				
				
			}
			
		}
		
		
	
	}
	
	

?>