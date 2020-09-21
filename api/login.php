<?php
include_once('../config/database.php');
require "../vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Max-Age: 3600");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$email = $data->email;
$password = $data->password;

if(empty($email) || empty($password)) {
	http_response_code(400);
	echo json_encode(array("message"=>"Missing required parameters"));
	exit();
}

$table_name = 'Users';

$query = "SELECT id, first_name, last_name, password FROM ". $table_name . " WHERE email = ? LIMIT 0,1";

try {
	$stmt = $conn->prepare($query);
	$stmt->bindParam(1,$email);
	$stmt->execute();
	$num = $stmt->rowCount();
} catch(PDOException $e) {
	http_response_code(401);
	echo json_encode(array("message"=>"DB Error", "error"=>$e->getMessage()));
	exit();
}

if($num > 0) {
	
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$id = $row['id'];
	$firstname = $row['first_name'];
	$lastname = $row['last_name'];
	$password2 = $row['password'];
	
	if(password_verify($password,$password2)) {
		
		// REturn the JWT
		$secret_key = "quamharleyknowsmyrnatammylynn";
		$issuer_claim = "http://kennyhefner.com";
		$audience_claim = "http://kennyhefner.com";
		$issuedat_claim = time(); // issued at
		$notbefore_claim = $issuedat_claim + 10; // not before in seconds
		$expire_claim = $issuedat_claim + 60; // expiere time in seconds
		$token = array(
			"iss"=>$issuer_claim,
			"aud"=>$audience_claim,
			"iat"=>$issuedat_claim,
			"nbf"=>$notbefore_claim,
			"exp"=>$expire_claim,
			"data"=>array(
				"id"=>$id,
				"firstname"=>$firstname,
				"lastname"=>$lastname,
				"email"=>$email
			)
		);
		
		http_response_code(200);
		$jwt = JWT::encode($token,$secret_key);
		echo json_encode(
			array(
				"message"=>"Successful login.",
				"jwt"=>$jwt,
				"email"=>$email,
				"expireAt"=>$expire_claim
			)
		);
		exit();
		
	} else {
		http_response_code(401);
		echo json_encode(array("message"=>"Login failed", "password"=>$password));
		exit();
	}
	
} else {
	http_response_code(401);
	echo json_encode(array("message"=>"Login failed", "Invalid credentials"));
	exit();
}