<?php
	date_default_timezone_set("America/Chicago");
	$db = new PDO("sqlite:database2.sqlite") or die("cannot open the database");
	$nodeId = $_GET["nodeid"];
	$queryString = "";
	if ($_GET['startdate'] != 'null' && $_GET['enddate'] != 'null') {
		$dateArray =  explode(" ", $_GET['startdate']);
		$dateToParse = $dateArray[1] . " " . $dateArray[2] . " " . $dateArray[3];
		$startDateObject = date_create_from_format("M d Y",  $dateToParse);
		$dateArray =  explode(" ", $_GET['enddate']);
		$dateToParse = $dateArray[1] . " " . $dateArray[2] . " " . $dateArray[3];
		$endDateObject = date_create_from_format("M d Y",  $dateToParse);
		$queryString = "SELECT Temperature, Created FROM readings WHERE Node_ID = " . $nodeId .
					   " AND Created BETWEEN \"". date_format($startDateObject, 'Y-m-d') .
					   "\" AND \"" . date_format($endDateObject, 'Y-m-d') . "\"";
	
	} else {
		$queryString = "SELECT Temperature, Created FROM readings WHERE Node_ID = " . $nodeId;
	}
	$getTemps = $db->prepare($queryString);
	$getTemps->execute();
	$temps = $getTemps->fetchAll();
	$labels = "";
	$data = "";
	foreach ($temps as $temp) {
		$temperature = $temp[0];
		$time = date_create($temp[1]);
		$labels .= date_format($time, "Y/m/d H:i:s") . ",";
		$data .= $temperature . ",";
	}
	$labels = substr_replace($labels, "", -1);
	$data = substr_replace($data, "", -1);
	echo "Temperature;" . $labels . ";" . $data;
?>
