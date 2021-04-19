<?php
 
require '../config.php';
require '../routes.php';
require "../strings.php";
require './functions.php';

$connect = connect($database);

$settings = getSettings($connect);

$brand = getBrand($connect);

$smtp = getSmtp($connect);

$urlPath = new Routes();

require_once '../classes/phpmailer/vendor/phpmailer/phpmailer/src/Exception.php';
require_once '../classes/phpmailer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once '../classes/phpmailer/vendor/phpmailer/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

$json = file_get_contents('php://input');
 
$obj = json_decode($json, true);

$user_email = filter_var(strtolower($obj['user_email']), FILTER_VALIDATE_EMAIL);

$user_password = generatePassword();

$encryptPass = hash('sha512', $user_password);

if ($user_email) {

$Sql_Query = "SELECT * FROM users WHERE user_email = '".$user_email."' LIMIT 1";

    $sentence = $connect->prepare($Sql_Query);

    $sentence->execute();

    $qResults = $sentence->fetch();
    $user_name = $qResults['user_name'];

    if(!$qResults){

    $invalidMSG = 'email-not-exist';
     
    $invalidMSGJSon = json_encode($invalidMSG);
     
    echo $invalidMSGJSon;

    }else{
     
    	$statment = $connect->prepare(
    	"UPDATE users SET user_password = :user_password WHERE user_email = :user_email"
    	);

    	$statment->execute(array(

    		':user_password' => $encryptPass,
    		':user_email' => $user_email

    	));

    try {

        $mail->isSMTP();                                          
        $mail->Host       = $smtp['st_smtphost'];                
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = $smtp['st_smtpemail'];              
        $mail->Password   = $smtp['st_smtppassword'];                             
        $mail->SMTPSecure = $smtp['st_smtpencrypt'];
        $mail->Port       = $smtp['st_smtpport'];


        $mail->setFrom($smtp['st_smtpemail'], $settings['st_sitename']);
        $mail->addAddress($user_email);


        $mail->isHTML(true);
        $mail->Subject = _EMAILSUBJECT;
        $mail->Body = "<div style='background: #f7f6f6; padding: 40px;'><div style='display: block; max-width: 800px; margin-left: auto; margin-right: auto;'><div style='background: #ffffff; padding: 20px; border: 1px solid #dee5e7; border-radius: 6px; text-align: center; display: block;'><a href='".$urlPath->home()."'><img src='".$urlPath->image($brand['st_darklogo'])."' style='width: 100%; max-width: 180px; border: 0;'/></a></div><br/><div style='padding: 20px; border-radius: 6px; font-size: 14px; background-color: #ffffff; border: 1px solid #dee5e7;'>"._DEAR." <b>".$user_name."</b>,<br/><br/>"._EMAILMESSAGERESET." <b>".$user_password."</b></div><br/><div style='display: block; text-align: center; font-size: 12px; color: #9E9E9E;'>"._COPYRIGHT."</div></div></div>";

        $mail->send();

    	$validMSG = 'success';
    	 
    	$validMSGJSon = json_encode($validMSG);
    	 
    	echo $validMSGJSon;

    } catch (Exception $e) {

    } 

    }
    
}else{

    $InvalidMSG = 'incomplete';
     
    $InvalidMSGJSon = json_encode($InvalidMSG);
     
    echo $InvalidMSGJSon;
}

?>