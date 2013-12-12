#neurons-router#

The basic router-module for [Neurons](https://github.com/platdesign/Neurons).

##install##
`bower install neurons-router --save`

##provider#

###$routeProvider###

Define route-handlers for `get`, `post`, `put`, `delete` and `all`.

The following example takes effect for all request-methods.

####get($route, $closure)####

	$routeProvider->get('/home', function($response) {
		$response->setBody('Hello World');
	});

