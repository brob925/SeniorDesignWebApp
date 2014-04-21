<?php
	ini_set('display_errors', 1);
	error_reporting(~0);

	date_default_timezone_set("America/Chicago");

	// open database
	$db = new PDO("sqlite:database2.sqlite") or die("cannot open the database");

	// get all nodes
	$getNodes = $db->prepare("SELECT * FROM nodes");
	$getNodes->execute();
	$nodes = $getNodes->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
	<script src='http://www.google.com/jsapi'></script>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
	<style>
		html {
			background-image:url('stardust.png');
		}
		body {
			text-align:center;
			width:800px;
			margin-left:auto;
			margin-right:auto;
			margin-top:0px;
			margin-bottom:0px;
			padding:50px;
			padding-top:0px;
			font-family:Arial;
			background-color:#E0E0E0;
		}
		table {
			width:100%;
			margin-left:auto;
			margin-right:auto;
			margin-top:14px;
		}
		table, td, th {
			border:1px solid black;
		}
		.nodeTables {
			text-align:left;
			padding-top:25px;
		}
		.nodeTitle {
			font-size:24px;
			font-weight:bold;
		}
		canvas {
			float:left;
		}
		iframe {
			margin-top:10px;
			margin-left:100px;
		}
		#nodeSelector {
			float:left;
			width:100%;
			height:21px;
			text-align:center;
			background-color:white;
		}
		.nodeSelection {
			float:left;
			margin-left:1px;
			height:20px;
			background-color:gray;
			color:white;
			font-weight:bold;
			cursor:pointer;
		}
		#sensorSelector {
			float:left;
			width:199px;
			height:410px;
			background-color:gray;
			border-left:1px solid white;
		}
		.sensorSelection {
			padding-top:15px;
			padding-bottom:15px;
			border-bottom:1px solid white;
			color:white;
			font-weight:bold;
			font-size:18px;
			cursor:pointer;
		}
		#chart {
			border:1px solid white;
		}
	</style>
	<script>
	google.load('visualization', '1.1', {'packages':['annotationchart']});
	google.setOnLoadCallback(drawChart);
	var nodeActive = 1;
	var tempActive = true;
	var locationActive = false;
	var radActive = false;
	var co2Active = false;
	var chart;
	function drawChart() {
		clearChart();
		$('.nodeSelection').css('background-color', 'gray');
		$('#node'+nodeActive).css('background-color', 'rgba(151, 187, 205, 1.0)');
		if (locationActive) {
			$('#responseDiv').html('<img src="underconstruction.gif"/>');
			$('.sensorSelection').css('background-color', 'gray');
			$('#location').css('background-color', 'rgba(151, 187, 205, 1.0)');
			return;
		}
		var xmlhttp;
		if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}
		else {// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function() {
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				var responseArray = xmlhttp.responseText.split(';');
				var dataType = responseArray[0];
				var datetimes = responseArray[1].split(',');
				var data = responseArray[2].split(',');
				var chartData = new google.visualization.DataTable();
				chartData.addColumn('datetime', 'Date');
				chartData.addColumn('number', dataType);
				var rows = [];
				for (var i=0; i<data.length; i++) {
					rows.push([new Date(datetimes[i]), Number(data[i])]);
				}
				chartData.addRows(rows);
				chart = new google.visualization.AnnotationChart(document.getElementById('chart'));
				if (data.length > 25) {
					var options = {
						fill:20,
						zoomEndTime:new Date(datetimes[data.length-1]),
						zoomStartTime:new Date(datetimes[data.length-25]),
					};
				} else {
					var options = {
						fill:20,
					};
				}
				chart.draw(chartData, options);
			}
		}
		var getURL = "";
		$('.sensorSelection').css('background-color', 'gray');
		if (tempActive) {
			getURL = "getTemps.php";
			$('#temperature').css('background-color', 'rgba(151, 187, 205, 1.0)');
		} else if (locationActive) {
			getURL = "getLocation.php";
			$('#location').css('background-color', 'rgba(151, 187, 205, 1.0)');
		} else if (radActive) {
			getURL = "getRad.php";
			$('#radiation').css('background-color', 'rgba(151, 187, 205, 1.0)');
		} else if (co2Active) {
			getURL = "getCo2.php";
			$('#carbonDioxide').css('background-color', 'rgba(151, 187, 205, 1.0)');
		}
		xmlhttp.open("GET",getURL+"?nodeid="+nodeActive,true);
		xmlhttp.send();
	}
	
	function tempClick() {
		if (tempActive) return;
		tempActive = true;
		locationActive = false;
		radActive = false;
		co2Active = false;
		drawChart();
	}
	
	function locationClick() {
		if (locationActive) return;
		tempActive = false;
		locationActive = true;
		radActive = false;
		co2Active = false;
		drawChart();
	}
	
	function radClick() {
		if (radActive) return;
		tempActive = false;
		locationActive = false;
		radActive = true;
		co2Active = false;
		drawChart();
	}
	
	function co2Click() {
		if (co2Active) return;
		tempActive = false;
		locationActive = false;
		radActive = false;
		co2Active = true;
		drawChart();
	}
	
	function clearChart() {
		$('#chart').empty();
	}
	
	</script>
</head>

<body>
	<img src="caution.gif">
	<h1>ECEN 403 - Group 22's Project Website</h1>
	<div id="nodeSelector">
		<?php 
		$numNodes = count($nodes);
		$divWidth = (799-$numNodes)/$numNodes;
		foreach($nodes as $node) {?>
			<div class="nodeSelection" id="node<?php echo $node[0]; ?>" style="width:<?php echo $divWidth ?>px;" onclick="nodeActive=<?php echo $node[0]; ?>;drawChart()">Node <?php echo $node[0]; ?></div>
		<?php } ?>
		<br clear="all">
	</div>
	<div id="sensorSelector">
		<div class="sensorSelection" id="temperature" onclick="tempClick()">Temperature</div>
		<div class="sensorSelection" id="location" onclick="locationClick()">Location</div>
		<div class="sensorSelection" id="radiation" onclick="radClick()">Radiation</div>
		<div class="sensorSelection" id="carbonDioxide" onclick="co2Click()">Carbon Dioxide</div>
	</div>
	<div id="#responseDiv"><div id="chart" style="width:598px;height:408px;float:left;"></div></div>
	<br clear="all">
</body>