<?php

require "core.php";

$errors = '';

// Title

$titleHeader = getTitle($connect, _MOVIESPAGETITLE);

// Get All Movies

if (getParamsGenre()) {

	$genreGet = clearGetData(getParamsGenre());

	$rMovies = getAllMovies($connect, $site_config['items_page'], $genreGet);

}else{

	$rMovies = getAllMovies($connect, $site_config['items_page']);

}

// Pages

$numPages = numTotalMovies($site_config['items_page'], $connect);


require './header.php';
require './top.php';
require './views/movies.view.php';
require './sidemenu.php';
require './bottom.php';
require './footer.php';

?>