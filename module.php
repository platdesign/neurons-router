<?PHP

	
	
	
	
	
	$module = nrns::module("router", []);
	
	
	$module->config(function(){
	
		require 'trait/routeSetter.php';
		require 'service/route.php';
	
		require 'provider/routeProvider.php';
	
	
	});
	
	$module->provider("routeProvider", "router\\routeProvider");
	
	
	
	
	

?>