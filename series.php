<?php

require "core.php";

$errors = '';

// Title

$titleHeader = getTitle($connect, _SERIESPAGETITLE);

// Get All Series

if (getParamsGenre()) {

	$genreGet = clearGetData(getParamsGenre());

	$rSeries = getAllSeries($connect, $site_config['items_page'], $genreGet);

}else{

	$rSeries = getAllSeries($connect, $site_config['items_page']);

}

// Pages

$numPages = numTotalSeries($site_config['items_page'], $connect);


require './header.php';
require './top.php';
require './views/series.view.php';
require './sidemenu.php';
require './bottom.php';
require './footer.php';

?> 