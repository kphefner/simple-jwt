<?php
	include_once('../config/database.php');
	require "../vendor/autoload.php";
	use \Firebase\JWT\JWT;
	
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	header("Access-Control-Allow-Max-Age: 3600");
	header("Access-Control-Allow-Methods: POST");
	header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
	
	$secret_key = "quamharleyknowsmyrnatammylynn";
	$jwt = null;
	
	$databaseService = new DatabaseService();
	$conn = $databaseService->getConnection();
	
	$data = json_decode(file_get_contents("php://input"));
	
	// FOR additional api requests like this one, we expect $_POST['token'] to be the JWT token returned at login
	
	if(!empty($data->token)) {
		
		// IS the token valid
		try {
			$decoded = JST::decode($data->token,$secret_key,array('HS256'));
			
			// do the protected actions
		
		} catch(Exception $e) {
			
			http_response_code(401);
			echo json_encode(array("message"=>"Access Denied"));
			exit();
		
		}
	
	} else {
	
	// return 401 error
	http_response_code(401);
	echo json_encode(array("message"=>"Bad Request"));
	exit();

}