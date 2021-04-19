<?php
 
require '../config.php';
require './functions.php';

$connect = connect($database);

$json = file_get_contents('php://input');
 
$obj = json_decode($json, true);

$user_name = filter_var($obj["user_name"], FILTER_SANITIZE_STRING);

$user_email = filter_var(strtolower($obj['user_email']), FILTER_VALIDATE_EMAIL);

$user_password = filter_var($obj["user_password"], FILTER_SANITIZE_STRING);

$encryptPass = hash('sha512', $user_password);

if ($user_email && strlen($user_name) >= 3 && strlen($user_password) >= 6) {
	
	$Sql_Query = "SELECT * FROM users WHERE user_email = '".$user_email."' LIMIT 1";

	$sentence = $connect->prepare($Sql_Query);

	$sentence->execute();

	$qResults = $sentence->fetchAll(PDO::FETCH_ASSOC);

	if($qResults){

	$InvalidMSG = 'exist';
	 
	$InvalidMSGJSon = json_encode($InvalidMSG);
	 
	echo $InvalidMSGJSon;

	}else{
	 
	$InvalidMSG = 'success';

	$InvalidMSGJSon = json_encode($InvalidMSG);

	$statement = $connect->prepare("INSERT INTO users (user_id, user_name, user_email, user_password) VALUES (null, :user_name, :user_email, :user_password)");

	$statement->execute(array(
			  	':user_name' => $user_name,
			  	':user_email' => $user_email,
			  	':user_password' => $encryptPass
			  ));
	 
	echo $InvalidMSGJSon;
	 
	}

}else{

	$InvalidMSG = 'incomplete';
	 
	$InvalidMSGJSon = json_encode($InvalidMSG);
	 
	echo $InvalidMSGJSon;
}

?>