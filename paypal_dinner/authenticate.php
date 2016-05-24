<?php 
require('config.php');
$draw_no = rand(500,1300);

// echo '<pre>';
// print_r($_GET);exit;
$emp_username = $_GET['name'];
// update database.

$servername = DB_HOST;
$dbname = DB_NAME;
$dbusername = DB_USER;
$password = DB_PASS;
$tablename = 'employee';
$status = 1;

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
// check if user exists and has status =1 in database

$statement = $conn->prepare("SELECT id,username,status,draw_no from employee WHERE username=:username");
$statement->bindValue(':username', $emp_username, PDO::PARAM_STR);
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
// echo '<pre>';
// print_r($result[0]);exit;
if(!$result) {
	echo "This user $emp_username, does not exists~1";exit;//1 for error , producing beep error sound, return 2 for success sound line 35,40
}
if($result[0]['id'] > 0 && $result[0]['status'] == 1){
	//user already exists and status is 1 display the draw number
	echo "Welcome ".$emp_username.', You are registered already and your lucky draw number is = '.$result[0]['draw_no'].'~2';exit;
} else{
	$statement = $conn->prepare("UPDATE $tablename SET status=:status, draw_no=:draw_no WHERE username=:username");
	$statement->bindValue(':status', $status, PDO::PARAM_STR);
	$statement->bindValue(':draw_no', $draw_no, PDO::PARAM_STR);
	$statement->bindValue(':username', $emp_username, PDO::PARAM_STR);
	$statement->execute();
	echo "Welcome <b>".$emp_username.'</b>, your lucky draw number is = '.$draw_no.'~2';exit;
}


	
	