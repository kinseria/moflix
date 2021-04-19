<?php

$page = 1;
if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}

$limit = 10;
if(!empty($_GET['limit'])) {
    $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT);
}

$offset = ($page - 1) * $limit;

header('Content-Type: application/json');
header("access-control-allow-origin: *");

require '../config.php';
require './functions.php';

$connect = connect($database);

$sqlQuery = "SELECT * FROM episodes";

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $sqlQuery .= " WHERE episode_serie = '".$_GET['id']."'";
}

$sqlQuery .= " AND episode_status = 1 ORDER BY episode_title ASC";


if(isset($_GET['page']) && !empty($_GET['page'])) {
    $sqlQuery .= " LIMIT ".$offset.",".$limit;
}

if(isset($_GET['limit']) && !empty($_GET['limit']) && !isset($_GET['page'])) {
    $sqlQuery .= " LIMIT ".$limit;
}

$sentence = $connect->prepare($sqlQuery);

$sentence->execute();

$qResults = $sentence->fetchAll(PDO::FETCH_ASSOC);

$data = array();

foreach ($qResults as $row) {

    $episode_id = $row['episode_id'];
    $episode_title = $row['episode_title'];
    $episode_description = $row['episode_description'];
    $episode_link = $row['episode_link'];
    $episode_date = formatDate($row['episode_date']);
    $episode_image = $row['episode_image'];

    $data[] = array(
    'episode_id'=> $episode_id,
    'episode_title'=> html_entity_decode($episode_title),
    'episode_description'=> $episode_description,
    'episode_link'=> $episode_link,
    'episode_date'=> $episode_date,
    'episode_image'=> $episode_image
    );
}

print json_encode($data, JSON_NUMERIC_CHECK);

?>