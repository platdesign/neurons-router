<?PHP

namespace router;
use nrns;

class route {
	
	use nrns\events;
	
	private $method;
	public $_route, $params = [], $route;
	
	
	const paramRegEx = "#:([\w.-]+)\+?#";
	
	
	
	
	
	public function __construct($method, $route, $then) {

		$this->method 	= $method;
		$this->_route 	= $route;
		$this->then 	= $then;
		$this->params	= (object) [];
		
	}
	
	
	public function call() {
		nrns::$injection->invoke($this->then, ['route'=>$this]);
	}
	
	

	
	
	
	
	
	
	
	
	private function setParams($vals) {
		$keys = $this->getKeys();
		$this->params = (object) array_combine($keys, $vals);
	}
	
	private function getKeys() {
		preg_match_all(self::paramRegEx, $this->_route, $matches);
		$result = [];
		foreach($matches[0] as $key) {
			$result[] = substr($key, 1);
		}
		return $result;
	}
	
	public function matchesWith($route) {
		$pattern = preg_replace(self::paramRegEx, "([\w.-]+)", $this->_route);
		
        if (preg_match('#^/?' . $pattern . '/?$#', $route, $matches)) {
			unset($matches[0]);
			
			$this->setParams($matches);
			$this->route = $route;
            return true;
        }
		
	}
	
	
	public function __tostring() {
		return (string) nrns::$injection->service('request')->getRoute();
	}
}

?>