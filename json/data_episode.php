<?php

header('Content-Type: application/json');
header("access-control-allow-origin: *");

require '../config.php';
require './functions.php';

$connect = connect($database);

$sqlQuery = "SELECT episodes.*, serie_title AS serie_title FROM episodes JOIN series ON series.serie_id = episodes.episode_serie WHERE episodes.episode_id = '".$_GET['id']."' LIMIT 1";

$sentence = $connect->prepare($sqlQuery);

$sentence->execute();

$qResults = $sentence->fetchAll(PDO::FETCH_ASSOC);

$data = array();

foreach ($qResults as $row) {

    $episode_id = $row['episode_id'];
    $episode_title = $row['episode_title'];
    $serie_title = $row['serie_title'];
    $episode_description = $row['episode_description'];
    $episode_link = $row['episode_link'];
    $episode_date = formatDate($row['episode_date']);
    $episode_image = $row['episode_image'];

    $data[] = array(
    'episode_id'=> $episode_id,
    'serie_title'=> html_entity_decode($serie_title),
    'episode_title'=> html_entity_decode($episode_title),
    'episode_description'=> $episode_description,
    'episode_link'=> $episode_link,
    'episode_date'=> $episode_date,
    'episode_image'=> $episode_image
    );
}

print json_encode($data, JSON_NUMERIC_CHECK);

?>