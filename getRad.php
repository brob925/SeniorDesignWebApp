<?php
	date_default_timezone_set("America/Chicago");
	$db = new PDO("sqlite:database2.sqlite") or die("cannot open the database");
	$nodeId = $_GET["nodeid"];
	$getRads = $db->prepare("SELECT Radiation, Created FROM readings WHERE Node_ID = " . $nodeId);
	$getRads->execute();
	$rads = $getRads->fetchAll();
	$labels = "";
	$data = "";
	foreach ($rads as $rad) {
		$radiation = $rad[0];
		$time = date_create($rad[1]);
		$labels .= date_format($time, "Y/m/d H:i:s") . ",";
		$data .= $radiation . ",";
	}
	$labels = substr_replace($labels, "", -1);
	$data = substr_replace($data, "", -1);
	echo "Radiation;" . $labels . ";" . $data;
?>