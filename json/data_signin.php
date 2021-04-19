<?php
 
require '../config.php';
require './functions.php';

$connect = connect($database);
 
$json = file_get_contents('php://input');
 
$obj = json_decode($json, true);

$user_email = filter_var(strtolower($obj['user_email']), FILTER_VALIDATE_EMAIL);

$user_password = filter_var($obj["user_password"], FILTER_SANITIZE_STRING);

$encryptPass = hash('sha512', $user_password);

if ($user_email && strlen($user_password) >= 6) {

	$Sql_Query = "SELECT * FROM users WHERE user_email = '".$user_email."' AND user_password = '".$encryptPass."' AND user_status = 1 LIMIT 1";

	$sentence = $connect->prepare($Sql_Query);

	$sentence->execute();

	$qResults = $sentence->fetchAll(PDO::FETCH_ASSOC);

	if($qResults){

	$data = array();

	foreach ($qResults as $row) {

	    $user_id = $row['user_id'];
	    $user_name = $row['user_name'];
	    $user_email = $row['user_email'];

	    $data[] = array(
	    'user_id'=> $user_id,
	    'user_name'=> html_entity_decode($user_name),
	    'user_email'=> $user_email,
	    );
	}

	print json_encode($data, JSON_NUMERIC_CHECK);

	}else{
	 
	$InvalidMSG = 'error';
	 
	$InvalidMSGJSon = json_encode($InvalidMSG);
	 
	echo $InvalidMSGJSon;
	 
	}

}else{

	$InvalidMSG = 'incomplete';
	 
	$InvalidMSGJSon = json_encode($InvalidMSG);
	 
	echo $InvalidMSGJSon;
}

?>