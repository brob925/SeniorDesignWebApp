<?php
	date_default_timezone_set("America/Chicago");
	$db = new PDO("sqlite:database2.sqlite") or die("cannot open the database");
	$nodeId = $_GET['nodeid'];
	$queryString = "";
	if ($_GET['startdate'] != 'null' && $_GET['enddate'] != 'null') {
		$dateArray =  explode(" ", $_GET['startdate']);
		$dateToParse = $dateArray[1] . " " . $dateArray[2] . " " . $dateArray[3];
		$startDateObject = date_create_from_format("M d Y",  $dateToParse);
		$dateArray =  explode(" ", $_GET['enddate']);
		$dateToParse = $dateArray[1] . " " . $dateArray[2] . " " . $dateArray[3];
		$endDateObject = date_create_from_format("M d Y",  $dateToParse);
		$queryString = "SELECT Latitude, Longitude, Created FROM readings WHERE Node_ID = " . $nodeId .
					   " AND Created BETWEEN \"". date_format($startDateObject, 'Y-m-d') .
					   "\" AND \"" . date_format($endDateObject, 'Y-m-d') . "\"";
	
	} else {
		$queryString = "SELECT Latitude, Longitude, Created FROM readings WHERE Node_ID = " . $nodeId;
	}
	$getLocs = $db->prepare($queryString);
	$getLocs->execute();
	$locations = $getLocs->fetchAll();
	$dates = "";
	$latitudes = "";
	$longitudes = "";
	foreach ($locations as $row) {
		$latitude = $row[0];
		$longitude = $row[1];
		$time = date_create($row[2]);
		$dates .= date_format($time, "Y/m/d H:i:s") . ",";
		$latitudes .= $latitude . ",";
		$longitudes .= $longitude . ",";
	}
	$dates = substr_replace($dates, "", -1);
	$latitudes = substr_replace($latitudes, "", -1);
	$longitudes = substr_replace($longitudes, "", -1);
	echo "Location;" . $dates . ";" . $latitudes . ";" . $longitudes;
?>