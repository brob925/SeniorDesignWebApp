<?php
	/* Conncet to database and pull temperature information inbetween the provided start and end dates */
	date_default_timezone_set("America/Chicago");
	$db = new PDO("sqlite:../database2.sqlite") or die("cannot open the database");
	$nodeId = $_GET["nodeid"]; // get what node we need info for
	$queryString = "";
	if ($_GET['startdate'] != 'null' && $_GET['enddate'] != 'null') {
		// if theres a start date and end date specified only pull data from there
		// need to get date in the correct format though for database query
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
		// if no start/end date is specifed then be pull all the data
		$queryString = "SELECT Temperature, Created FROM readings WHERE Node_ID = " . $nodeId;
	}
	// execute query on the database
	$getTemps = $db->prepare($queryString);
	$getTemps->execute();
	$temps = $getTemps->fetchAll();
	// put data from query into a nice format so javascript on client
	// side doesn't have to do too much
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
	// send response to client javascript
	echo "Temperature;" . $labels . ";" . $data;
?>
