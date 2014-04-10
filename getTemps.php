<?php
	date_default_timezone_set("America/Chicago");
	$db = new PDO("sqlite:database.sqlite") or die("cannot open the database");
	$nodeId = $_GET["nodeid"];
	$getTemps = $db->prepare("SELECT Temperature, Created FROM readings WHERE Node_ID = " . $nodeId);
	$getTemps->execute();
	$temps = $getTemps->fetchAll();
	$labels = "";
	$data = "";
	foreach ($temps as $temp) {
		$temperature = $temp[0];
		$time = date_create($temp[1]);
		$labels .= date_format($time, "g:i A") . ",";
		$data .= $temperature . ",";
	}
	$labels = substr_replace($labels, "", -1);
	$data = substr_replace($data, "", -1);
	echo $labels . ";" . $data;
?>
