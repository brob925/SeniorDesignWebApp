<?php
	date_default_timezone_set("America/Chicago");
	$db = new PDO("sqlite:database.sqlite") or die("cannot open the database");
	$nodeId = $_GET["nodeid"];
	$getRads = $db->prepare("SELECT Radiation, Created FROM readings WHERE Node_ID = " . $nodeId);
	$getRads->execute();
	$rads = $getRads->fetchAll();
	$labels = "";
	$data = "";
	foreach ($rads as $rad) {
		$radiation = $rad[0];
		$time = date_create($rad[1]);
		$labels .= date_format($time, "g:i A") . ",";
		$data .= $radiation . ",";
	}
	$labels = substr_replace($labels, "", -1);
	$data = substr_replace($data, "", -1);
	echo $labels . ";" . $data;
?>