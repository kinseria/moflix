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

$queryGet = clearData($_GET['query']);

$connect = connect($database);

$sqlQuery = "(SELECT movie_id as id, movie_image as image, movie_title as title, movie_year as year, 'movie' as type FROM movies WHERE movie_title LIKE '%" . $queryGet . "%') UNION (SELECT serie_id as id, serie_image as image, serie_title as title, serie_year as year, 'serie' as type FROM series WHERE serie_title LIKE '%" . $queryGet . "%')";

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

    $id = $row['id'];
    $title = $row['title'];
    $image = $row['image'];
    $type = $row['type'];
    $year = $row['year'];

    $data[] = array(
    'id'=> $id,
    'title'=> html_entity_decode($title),
    'image'=> $image,
    'type'=> $type,
    'year'=> $year,
    );
}

print json_encode($data, JSON_NUMERIC_CHECK);

?>