<?php

function connect($database){
    try{
        $connect = new PDO('mysql:host='.$database['host'].';dbname='.$database['db'],$database['user'],$database['pass'], array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES  \'UTF8\''));
        return $connect;
        
    }catch (PDOException $e){
        return false;
    }
}

function separateComma($data){

    $string = str_replace(",", ", ", $data);
    return $string;

}

function generatePassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);
}

function formatDate($date){

    $convert = strtotime($date);
    $output = date("d/m/y", $convert);
    return $output;

}

function clearData($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars ($data);
    return $data;
}

function getSettings($connect){
    
    $sentence = $connect->prepare("SELECT * FROM settings"); 
    $sentence->execute();
    return $sentence->fetch();
}

function getSmtp($connect)
{
    
    $sentence = $connect->prepare("SELECT * FROM smtp"); 
    $sentence->execute();
    return $sentence->fetch();
}

function getBrand($connect)
{
    
    $sentence = $connect->prepare("SELECT * FROM brand"); 
    $sentence->execute();
    return $sentence->fetch();
}

?>