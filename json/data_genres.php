<?php

header('Content-Type: application/json');
header("access-control-allow-origin: *");

require '../config.php';
require './functions.php';

$connect = connect($database);

$sqlQuery = "SELECT * FROM genres";

$sentence = $connect->prepare($sqlQuery);

$sentence->execute();

$qResults = $sentence->fetchAll(PDO::FETCH_ASSOC);

$data = array();

foreach ($qResults as $row) {

	$genre_id = $row['genre_id'];
    $genre_title = $row['genre_title'];
    $genre_image = $row['genre_image'];

    $data[] = array(
    	'genre_id'=> $genre_id,
    	'genre_title'=> html_entity_decode($genre_title),
    	'genre_image'=> $genre_image,
    	);
}

print json_encode($data, JSON_NUMERIC_CHECK);

?>