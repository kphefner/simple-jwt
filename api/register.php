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

$firstName = $data->first_name;
$lastName = $data->last_name;
$email = $data->email;
$password = $data->password;

if(empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
	http_response_code(400);
	echo json_encode(array("message"=>"Missing required parameters"));
	exit();
}

$table_name = 'Users';

$query = "SELECT email FROM ". $table_name . " WHERE email = ? LIMIT 0,1";

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
	// USER EMAIL Already exists
	// Fall through error
	http_response_code(400);
	echo json_encode(array("message"=>"User already exists"));
	exit();
}

$query = "INSERT INTO ". $table_name ." SET 
first_name = :firstname,
last_name = :lastname,
email = :email,
password = :password";

try {
	$stmt = $conn->prepare($query);
	$stmt->bindParam(':firstname',$firstName);
	$stmt->bindParam(':lastname',$lastName);
	$stmt->bindParam(':email',$email);
	$password_hash = password_hash($password, PASSWORD_BCRYPT);
	$stmt->bindParam(':password',$password_hash);
	$stmt->execute();
	http_response_code(200);
	echo json_encode(array("message"=>"User was successfully registered"));
	exit;
} catch(PDOException $e) {
	http_response_code(401);
	echo json_encode(array("message"=>"DB Error", "error"=>$e->getMessage()));
	exit();
}

// Fall through error
http_response_code(400);
echo json_encode(array("message"=>"Unable to register user"));
 
?>