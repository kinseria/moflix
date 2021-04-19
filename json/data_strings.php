<?php

header('Content-Type: application/json');
header("access-control-allow-origin: *");

require '../config.php';
require './functions.php';

$connect = connect($database);

$sqlQuery = "SELECT * FROM strings";

$sentence = $connect->prepare($sqlQuery);

$sentence->execute();

$qResults = $sentence->fetchAll(PDO::FETCH_ASSOC);

$data = array();

foreach ($qResults as $row) {

    $st_aboutus = $row['st_aboutus'];
    $st_privacypolicy = $row['st_privacypolicy'];
    $st_termsofservice = $row['st_termsofservice'];

    $data[] = array(
    'st_aboutus'=> html_entity_decode($st_aboutus),
    'st_privacypolicy'=> html_entity_decode($st_privacypolicy),
    'st_termsofservice'=> html_entity_decode($st_termsofservice)
    );
}

print json_encode($data, JSON_NUMERIC_CHECK);

?>