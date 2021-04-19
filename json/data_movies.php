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

$sqlQuery = "SELECT movies.*, GROUP_CONCAT(DISTINCT(genres.genre_title) SEPARATOR ', ') AS movie_genre, AVG(rating) AS movie_rate FROM movies JOIN movies_genres ON movies_genres.movie_id = movies.movie_id LEFT JOIN movies_reviews ON movies_reviews.item = movies.movie_id JOIN genres ON genres.genre_id = movies_genres.genre_id WHERE movies.movie_status = 1";

if(isset($_GET['id']) && !empty($_GET['id'])) {
    $sqlQuery .= " AND movies.movie_id=".$_GET["id"];
}

if(isset($_GET['search']) && !empty($_GET['search'])) {
    $sqlQuery .= " AND movies.movie_title LIKE '%".$_GET["id"]."%' OR movies.movie_stars LIKE '%".$_GET["id"]."%'";
}

if(isset($_GET['genre']) && !empty($_GET['genre'])) {
    $sqlQuery .= " AND movies.movie_id IN (SELECT movie_id FROM movies_genres WHERE genre_id = ".$_GET["genre"].")";
}

if(isset($_GET['year']) && !empty($_GET['year'])) {
    $sqlQuery .= " AND movies.movie_year = '".$_GET['year']."'";
}

if(isset($_GET['featured']) && !empty($_GET['featured'])) {
    $sqlQuery .= " AND movies.movie_featured = '".$_GET['featured']."'";
}
    $sqlQuery .= " GROUP BY movies.movie_id";

if(isset($_GET['order']) && !empty($_GET['order'])) {

if ($_GET["order"] == "asc") {

    $sqlQuery .= " ORDER BY movies.movie_date ASC";

}else{

    $sqlQuery .= " ORDER BY movies.movie_date DESC";

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

    $movie_id = $row['movie_id'];
    $movie_title = $row['movie_title'];
    $movie_genre = $row['movie_genre'];
    $movie_description = $row['movie_description'];
    $movie_year = $row['movie_year'];
    $movie_trailer = $row['movie_trailer'];
    $movie_link = $row['movie_link'];
    $movie_stars = $row['movie_stars'];
    $movie_featured = $row['movie_featured'];
    $movie_rate = $row['movie_rate'];
    $movie_duration = $row['movie_duration'];
    $movie_image = $row['movie_image'];

    $data[] = array(
    'movie_id'=> $movie_id,
    'movie_title'=> html_entity_decode($movie_title),
    'movie_genre'=> $movie_genre,
    'movie_description'=> $movie_description,
    'movie_year'=> $movie_year,
    'movie_trailer'=> $movie_trailer,
    'movie_link'=> $movie_link,
    'movie_stars'=> separateComma($movie_stars),
    'movie_featured'=> $movie_featured,
    'movie_rate'=> round($movie_rate, 1),
    'movie_rate_half'=> round($movie_rate/2, 1),
    'movie_duration'=> $movie_duration,
    'movie_image'=> $movie_image
    );
}

print json_encode($data, JSON_NUMERIC_CHECK);

?>