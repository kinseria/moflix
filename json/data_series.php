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

$sqlQuery = "SELECT series.*, GROUP_CONCAT(DISTINCT(genres.genre_title) SEPARATOR ', ') AS serie_genre, AVG(rating) AS serie_rate FROM series JOIN series_genres ON series_genres.serie_id = series.serie_id LEFT JOIN series_reviews ON series_reviews.item = series.serie_id JOIN genres ON genres.genre_id = series_genres.genre_id WHERE series.serie_status = 1";

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $sqlQuery .= " AND series.serie_id=".$_GET["id"];
}

if(isset($_GET['search']) && !empty($_GET['search'])) {
    $sqlQuery .= " AND series.serie_title LIKE '%".$_GET["id"]."%' OR series.serie_stars LIKE '%".$_GET["id"]."%'";
}

if(isset($_GET['genre']) && !empty($_GET['genre'])) {
    $sqlQuery .= " AND series.serie_id IN (SELECT serie_id FROM series_genres WHERE genre_id = ".$_GET["genre"].")";
}

if(isset($_GET['year']) && !empty($_GET['year'])) {
    $sqlQuery .= " AND series.serie_year = '".$_GET['year']."'";
}

if(isset($_GET['featured']) && !empty($_GET['featured'])) {
    $sqlQuery .= " AND series.serie_featured = '".$_GET['featured']."'";
}
    $sqlQuery .= " GROUP BY series.serie_id";

if(isset($_GET['order']) && !empty($_GET['order'])) {

if ($_GET["order"] === "asc") {

    $sqlQuery .= " ORDER BY series.serie_date ASC";

}else if ($_GET["order"] === "desc"){

    $sqlQuery .= " ORDER BY series.serie_date DESC";

    }
}

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

    $serie_id = $row['serie_id'];
    $serie_title = $row['serie_title'];
    $serie_genre = $row['serie_genre'];
    $serie_description = $row['serie_description'];
    $serie_year = $row['serie_year'];
    $serie_trailer = $row['serie_trailer'];
    $serie_stars = $row['serie_stars'];
    $serie_featured = $row['serie_featured'];
    $serie_rate = $row['serie_rate'];
    $serie_image = $row['serie_image'];

    $data[] = array(
    'serie_id'=> $serie_id,
    'serie_title'=> html_entity_decode($serie_title),
    'serie_genre'=> $serie_genre,
    'serie_description'=> $serie_description,
    'serie_year'=> $serie_year,
    'serie_trailer'=> $serie_trailer,
    'serie_stars'=> separateComma($serie_stars),
    'serie_featured'=> $serie_featured,
    'serie_rate'=> round($serie_rate, 1),
    'serie_rate_half'=> round($serie_rate/2, 1),
    'serie_image'=> $serie_image
    );
}

print json_encode($data, JSON_NUMERIC_CHECK);

?>