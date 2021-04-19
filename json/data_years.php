<?php

header('Content-Type: application/json');
header("access-control-allow-origin: *");

require '../config.php';
require './functions.php';

$connect = connect($database);

$sqlQuery = "SELECT movie_year AS year FROM movies GROUP BY movie_year ORDER BY movie_year ASC";

$sentence = $connect->prepare($sqlQuery);

$sentence->execute();

$qResults = $sentence->fetchAll(PDO::FETCH_ASSOC);

$data = array();

foreach ($qResults as $row) {

    $year = $row['year'];

    $data[] = array(
    'year'=> $year,
    );
}

print json_encode($data, JSON_NUMERIC_CHECK);

?>