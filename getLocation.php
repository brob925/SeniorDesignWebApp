<?php
	date_default_timezone_set("America/Chicago");
	$db = new PDO("sqlite:database2.sqlite") or die("cannot open the database");
	$nodeId = $_GET["nodeid"];
	$getLocs = $db->prepare("SELECT Latitude, Longitude, Created FROM readings WHERE Node_ID = " . $nodeId);
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