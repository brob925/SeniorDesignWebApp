<?php
	date_default_timezone_set("America/Chicago");
	$db = new PDO("sqlite:database2.sqlite") or die("cannot open the database");
	$nodeId = $_GET["nodeid"];
	$getCo2 = $db->prepare("SELECT CO2, Created FROM readings WHERE Node_ID = " . $nodeId);
	$getCo2->execute();
	$co2s = $getCo2->fetchAll();
	$labels = "";
	$data = "";
	foreach ($co2s as $co2) {
		$carbonDioxide = $co2[0];
		$time = date_create($co2[1]);
		$labels .= date_format($time, "Y/m/d H:i:s") . ",";
		$data .= $carbonDioxide . ",";
	}
	$labels = substr_replace($labels, "", -1);
	$data = substr_replace($data, "", -1);
	echo "Carbon Dioxide;" . $labels . ";" . $data;
?>